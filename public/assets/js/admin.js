(function () {
  function setupSidebarToggle() {
    const body = document.body;
    const buttons = Array.from(document.querySelectorAll("[data-sidebar-toggle]"));
    if (!body || buttons.length === 0) return;

    const storageKey = "adminSidebarCollapsed";
    let collapsed = false;

    try {
      const stored = window.localStorage.getItem(storageKey);
      collapsed = stored === "1";
    } catch (error) {
      collapsed = false;
    }

    function syncButton(button, isCollapsed) {
      const openLabel = button.dataset.labelOpen || "Ocultar menu";
      const closedLabel = button.dataset.labelClosed || "Mostrar menu";
      const label = isCollapsed ? closedLabel : openLabel;
      button.setAttribute("aria-expanded", isCollapsed ? "false" : "true");
      button.setAttribute("aria-label", label);
    }

    function applySidebarState(isCollapsed) {
      body.classList.toggle("admin-sidebar-collapsed", isCollapsed);
      buttons.forEach((button) => syncButton(button, isCollapsed));
    }

    function persistSidebarState(isCollapsed) {
      try {
        window.localStorage.setItem(storageKey, isCollapsed ? "1" : "0");
      } catch (error) {
        return;
      }
    }

    buttons.forEach((button) => {
      button.addEventListener("click", () => {
        collapsed = !collapsed;
        applySidebarState(collapsed);
        persistSidebarState(collapsed);
      });
    });

    applySidebarState(collapsed);
  }

  function setupPaymentFieldToggles() {
    const toggles = Array.from(document.querySelectorAll("[data-payment-toggle]"));
    if (toggles.length === 0) return;

    const syncGroupState = (method, enabled) => {
      const block = document.querySelector('[data-payment-fields="' + method + '"]');
      if (!block) return;

      block.hidden = false;
      block.classList.toggle("is-disabled", !enabled);
      block.setAttribute("aria-disabled", enabled ? "false" : "true");

      block.querySelectorAll("input, textarea, select, button").forEach((field) => {
        if (!(field instanceof HTMLElement)) return;
        if (field.matches('[type="hidden"]')) return;
        field.toggleAttribute("disabled", !enabled);
      });
    };

    toggles.forEach((toggle) => {
      const method = toggle.getAttribute("data-payment-toggle");
      if (!method) return;

      const apply = () => syncGroupState(method, !!toggle.checked);
      toggle.addEventListener("change", apply);
      apply();
    });
  }

  function setupPaymentManualToggle() {
    const toggle = document.querySelector("[data-payment-manual-toggle]");
    const block = document.querySelector("[data-payment-manual-fields]");
    if (!toggle || !block) return;

    const sync = () => {
      const enabled = !!toggle.checked;
      block.hidden = false;
      block.classList.toggle("is-disabled", !enabled);
      block.setAttribute("aria-disabled", enabled ? "false" : "true");

      block.querySelectorAll("input, textarea, select, button").forEach((field) => {
        if (!(field instanceof HTMLElement)) return;
        if (field.matches('[type="hidden"]')) return;
        field.toggleAttribute("disabled", !enabled);
      });

      recalcPaymentSchedule();
    };

    toggle.addEventListener("change", sync);
    sync();
  }

  function setupHeaderMediaToggle() {
    const toggle = document.querySelector("[data-header-media-toggle]");
    const block = document.querySelector("[data-header-media-fields]");
    if (!toggle || !block) return;

    const sync = () => {
      const enabled = !!toggle.checked;
      block.classList.toggle("is-disabled", !enabled);
      block.setAttribute("aria-disabled", enabled ? "false" : "true");

      block.querySelectorAll("input, textarea, select, button").forEach((field) => {
        if (!(field instanceof HTMLElement)) return;
        if (field.matches('[type="hidden"]')) return;
        field.toggleAttribute("disabled", !enabled);
      });
    };

    toggle.addEventListener("change", sync);
    sync();
  }

  function setupHeaderLayoutToggle() {
    const select = document.querySelector("[data-header-layout-select]");
    const block = document.querySelector("[data-header-layout-fields]");
    if (!select || !block) return;

    const sync = () => {
      const enabled = select.value === "aditivo";
      block.classList.toggle("is-disabled", !enabled);
      block.setAttribute("aria-disabled", enabled ? "false" : "true");

      block.querySelectorAll("input, textarea, select, button").forEach((field) => {
        if (!(field instanceof HTMLElement)) return;
        field.toggleAttribute("disabled", !enabled);
      });
    };

    select.addEventListener("change", sync);
    sync();
  }

  function setupAcceptanceModeToggle() {
    const toggle = document.querySelector("[data-acceptance-mode-toggle]");
    const contractBlock = document.querySelector('[data-acceptance-fields="contract"]');
    const summaryBlock = document.querySelector('[data-acceptance-fields="summary"]');
    if (!toggle || !contractBlock || !summaryBlock) return;

    const setBlockState = (block, enabled) => {
      block.hidden = !enabled;
      block.classList.toggle("is-disabled", !enabled);
      block.setAttribute("aria-disabled", enabled ? "false" : "true");

      block.querySelectorAll("input, textarea, select, button").forEach((field) => {
        if (!(field instanceof HTMLElement)) return;
        if (field.matches('[type="hidden"]')) return;
        field.toggleAttribute("disabled", !enabled);
      });
    };

    const sync = () => {
      const summaryEnabled = !!toggle.checked;
      setBlockState(contractBlock, !summaryEnabled);
      setBlockState(summaryBlock, summaryEnabled);
    };

    toggle.addEventListener("change", sync);
    sync();
  }

  function setupSyncedTableScrollbars() {
    const tableWraps = Array.from(document.querySelectorAll(".table-wrap"));
    if (tableWraps.length === 0) return;

    tableWraps.forEach((wrap) => {
      if (!(wrap instanceof HTMLElement)) return;

      const top = wrap.querySelector("[data-table-scroll-top]");
      const topInner = wrap.querySelector("[data-table-scroll-top-inner]");
      const bottom = wrap.querySelector("[data-table-scroll-bottom]");
      const table = wrap.querySelector("table");

      if (!(top instanceof HTMLElement) || !(topInner instanceof HTMLElement) || !(bottom instanceof HTMLElement) || !(table instanceof HTMLElement)) {
        return;
      }

      let syncing = false;

      const syncWidths = () => {
        const width = Math.max(table.scrollWidth, bottom.scrollWidth, bottom.clientWidth + 1);
        topInner.style.width = width + "px";
      };

      top.addEventListener("scroll", () => {
        if (syncing) return;
        syncing = true;
        bottom.scrollLeft = top.scrollLeft;
        syncing = false;
      });

      bottom.addEventListener("scroll", () => {
        if (syncing) return;
        syncing = true;
        top.scrollLeft = bottom.scrollLeft;
        syncing = false;
      });

      window.addEventListener("resize", syncWidths);
      syncWidths();
      window.requestAnimationFrame(syncWidths);
      window.setTimeout(syncWidths, 120);
    });
  }

  function setupProposalPanelCollapse() {
    const panels = Array.from(document.querySelectorAll("section.panel, article.panel"));
    if (panels.length === 0) return;

    const storageKey = "proposalPanelCollapseState";
    let savedState = {};

    try {
      const raw = window.localStorage.getItem(storageKey);
      savedState = raw ? JSON.parse(raw) || {} : {};
    } catch (error) {
      savedState = {};
    }

    const persistState = () => {
      try {
        window.localStorage.setItem(storageKey, JSON.stringify(savedState));
      } catch (error) {
        return;
      }
    };

    const slugify = (value) => String(value || "")
      .toLowerCase()
      .normalize("NFD")
      .replace(/[\u0300-\u036f]/g, "")
      .replace(/[^a-z0-9]+/g, "-")
      .replace(/^-+|-+$/g, "") || "painel";

    panels.forEach((panel, index) => {
      if (!(panel instanceof HTMLElement)) return;
      if (panel.classList.contains("panel-inner") || panel.classList.contains("compact")) return;
      if (panel.classList.contains("panel-collapsible-ready")) return;

      const heading = Array.from(panel.children).find((child) => child instanceof HTMLElement && child.tagName === "H2");
      if (!(heading instanceof HTMLElement)) return;

      const key = slugify(heading.textContent) + "-" + index;
      const header = document.createElement("div");
      const body = document.createElement("div");
      const toggle = document.createElement("button");
      const icon = document.createElement("span");

      header.className = "panel-collapse-header";
      body.className = "panel-collapse-body";
      body.id = "panel-collapse-body-" + key;

      toggle.type = "button";
      toggle.className = "panel-collapse-toggle";
      toggle.setAttribute("aria-controls", body.id);

      icon.className = "panel-collapse-icon";
      icon.setAttribute("aria-hidden", "true");
      toggle.appendChild(icon);

      panel.classList.add("panel-collapsible", "panel-collapsible-ready");
      panel.insertBefore(header, heading);
      header.appendChild(heading);
      header.appendChild(toggle);
      header.insertAdjacentElement("afterend", body);

      while (body.nextSibling) {
        body.appendChild(body.nextSibling);
      }

      const setCollapsed = (collapsed) => {
        panel.classList.toggle("is-collapsed", collapsed);
        body.hidden = collapsed;
        toggle.setAttribute("aria-expanded", collapsed ? "false" : "true");
        toggle.setAttribute("aria-label", collapsed ? "Expandir painel" : "Recolher painel");
        toggle.dataset.collapsed = collapsed ? "1" : "0";
        savedState[key] = collapsed ? 1 : 0;
        persistState();
      };

      toggle.addEventListener("click", () => {
        setCollapsed(!panel.classList.contains("is-collapsed"));
      });

      setCollapsed(savedState[key] === 1);
    });
  }

  function parseMoney(value) {
    if (!value) return 0;
    const normalized = String(value)
      .replace(/[R$\s]/g, "")
      .replace(/\./g, "")
      .replace(",", ".");
    const number = Number(normalized);
    return Number.isFinite(number) ? number : 0;
  }

  function formatMoney(value) {
    return new Intl.NumberFormat("pt-BR", {
      style: "currency",
      currency: "BRL",
    }).format(value || 0);
  }

  function numberToWords(value) {
    const units = ["zero", "um", "dois", "tres", "quatro", "cinco", "seis", "sete", "oito", "nove", "dez", "onze", "doze", "treze", "quatorze", "quinze", "dezesseis", "dezessete", "dezoito", "dezenove"];
    const tens = ["", "", "vinte", "trinta", "quarenta", "cinquenta", "sessenta", "setenta", "oitenta", "noventa"];
    const hundreds = ["", "cento", "duzentos", "trezentos", "quatrocentos", "quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos"];

    function intWords(n) {
      n = Math.floor(n);
      if (n < 20) return units[n];
      if (n < 100) {
        const d = Math.floor(n / 10);
        const r = n % 10;
        return r ? tens[d] + " e " + intWords(r) : tens[d];
      }
      if (n === 100) return "cem";
      if (n < 1000) {
        const h = Math.floor(n / 100);
        const r = n % 100;
        return r ? hundreds[h] + " e " + intWords(r) : hundreds[h];
      }
      if (n < 1000000) {
        const t = Math.floor(n / 1000);
        const r = n % 1000;
        const prefix = t === 1 ? "mil" : intWords(t) + " mil";
        if (!r) return prefix;
        return prefix + (r < 100 ? " e " : " ") + intWords(r);
      }
      return String(n);
    }

    const inteiro = Math.floor(value);
    const centavos = Math.round((value - inteiro) * 100);
    let frase = inteiro === 1 ? "um real" : `${intWords(inteiro)} reais`;
    if (inteiro === 0) frase = "zero reais";
    if (centavos > 0) {
      frase += centavos === 1 ? " e um centavo" : ` e ${intWords(centavos)} centavos`;
    }
    return frase;
  }

  function maskMoney(input) {
    let raw = input.value.replace(/[^\d]/g, "");
    if (raw === "") {
      input.value = "0,00";
      return;
    }
    const cents = Number(raw) / 100;
    input.value = cents.toLocaleString("pt-BR", {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
    });
  }

  function recalcTotal() {
    const totalEl = document.getElementById("proposal-total");
    const totalWordsEl = document.getElementById("proposal-total-words");
    if (!totalEl) return;

    let total = 0;

    document.querySelectorAll(".discipline-card").forEach((card) => {
      const checkbox = card.querySelector('input[type="checkbox"]');
      const valueInput = card.querySelector('input[data-money-input]');
      if (!checkbox || !valueInput) return;
      if (checkbox.checked) total += parseMoney(valueInput.value);
    });

    document.querySelectorAll(".custom-discipline-row").forEach((row) => {
      const active = row.querySelector("input[data-custom-discipline-active]");
      const valueInput = row.querySelector('input[data-money-input]');
      if (!active || !valueInput) return;
      if (active.checked) total += parseMoney(valueInput.value);
    });

    totalEl.textContent = formatMoney(total);
    if (totalWordsEl) {
      totalWordsEl.textContent = numberToWords(total);
    }
    recalcPaymentSchedule();
  }

  function syncPaymentScheduleRow(row) {
    if (!row) return;
    const typeField = row.querySelector("[data-payment-row-type]");
    const amountFieldWrap = row.querySelector(".payment-row-amount-field");
    const amountField = row.querySelector("[data-payment-row-amount]");
    if (!typeField || !amountFieldWrap || !amountField) return;

    const isSubtitle = typeField.value === "subtitle";
    amountFieldWrap.style.display = isSubtitle ? "none" : "";
    amountField.disabled = isSubtitle;
    if (isSubtitle) {
      amountField.value = "0,00";
    }
  }

  function paymentScheduleState() {
    const manualToggle = document.querySelector("[data-payment-manual-toggle]");
    if (manualToggle && !manualToggle.checked) {
      return { total: 0, hasLines: false };
    }

    let total = 0;
    let hasLines = false;

    document.querySelectorAll(".payment-row").forEach((row) => {
      const typeField = row.querySelector("[data-payment-row-type]");
      const amountField = row.querySelector("[data-payment-row-amount]");
      if (!typeField || !amountField) return;
      if (typeField.value === "subtitle") return;
      hasLines = true;
      total += parseMoney(amountField.value);
    });

    return { total, hasLines };
  }

  function recalcPaymentSchedule() {
    const scheduleTotalEl = document.querySelector("[data-payment-schedule-total]");
    const proposalTotalEl = document.querySelector("[data-payment-proposal-total]");
    const proposalTotalSource = document.getElementById("proposal-total");
    const state = paymentScheduleState();

    document.querySelectorAll(".payment-row").forEach(syncPaymentScheduleRow);

    if (scheduleTotalEl) {
      scheduleTotalEl.textContent = formatMoney(state.total);
    }
    if (proposalTotalEl && proposalTotalSource) {
      proposalTotalEl.textContent = proposalTotalSource.textContent || formatMoney(0);
    }
  }

  function setupPaymentScheduleEditor() {
    const form = document.getElementById("proposal-form");
    if (!form) return;

    document.querySelectorAll("[data-payment-row-type]").forEach((field) => {
      if (field.dataset.boundPaymentType === "1") return;
      field.dataset.boundPaymentType = "1";
      field.addEventListener("change", () => {
        const row = field.closest(".payment-row");
        syncPaymentScheduleRow(row);
        recalcPaymentSchedule();
      });
      syncPaymentScheduleRow(field.closest(".payment-row"));
    });

    if (form.dataset.paymentValidationBound !== "1") {
      form.dataset.paymentValidationBound = "1";
      form.addEventListener("submit", (event) => {
        const proposalTotal = parseMoney((document.getElementById("proposal-total") || {}).textContent || "0");
        const state = paymentScheduleState();
        if (!state.hasLines) return;
        if (Math.abs(state.total - proposalTotal) < 0.01) return;

        event.preventDefault();
        window.alert("A soma da forma de pagamento precisa ser igual ao valor total da proposta antes de salvar.");
      });
    }

    recalcPaymentSchedule();
  }

  function bindMoneyInput(input) {
    if (!input || input.dataset.boundMoney === "1") return;
    input.dataset.boundMoney = "1";
    input.addEventListener("input", () => {
      maskMoney(input);
      recalcTotal();
    });
    input.addEventListener("blur", () => maskMoney(input));
  }

  function bindCheckInput(input) {
    if (!input || input.dataset.boundCheck === "1") return;
    input.dataset.boundCheck = "1";
    input.addEventListener("change", recalcTotal);
  }

  function bindRowInputs(root) {
    if (!root) return;
    root.querySelectorAll("input[data-money-input]").forEach(bindMoneyInput);
    root.querySelectorAll("input[data-discipline-check]").forEach(bindCheckInput);
    root.querySelectorAll("input[data-custom-discipline-active]").forEach(bindCheckInput);
  }

  function cloneTemplate(templateId, index) {
    const tpl = document.getElementById(templateId);
    if (!tpl) return null;
    return tpl.innerHTML.replace(/__INDEX__/g, String(index));
  }

  function setupRepeater(listId, buttonId, templateId) {
    const list = document.getElementById(listId);
    const button = document.getElementById(buttonId);
    if (!list || !button) return;

    button.addEventListener("click", () => {
      const nextIndex = Number(list.dataset.nextIndex || list.children.length || 0);
      const html = cloneTemplate(templateId, nextIndex);
      if (!html) return;
      const wrap = document.createElement("div");
      wrap.innerHTML = html;
      const row = wrap.firstElementChild;
      if (!row) return;
      list.appendChild(row);
      list.dataset.nextIndex = String(nextIndex + 1);
      bindRowInputs(row);
      row.querySelectorAll("[data-payment-row-type]").forEach((field) => {
        field.addEventListener("change", () => {
          const currentRow = field.closest(".payment-row");
          syncPaymentScheduleRow(currentRow);
          recalcPaymentSchedule();
        });
      });
      syncPaymentScheduleRow(row);
      recalcTotal();
    });
  }

  document.addEventListener("click", (event) => {
    const target = event.target;
    if (!(target instanceof HTMLElement)) return;
    if (!target.classList.contains("repeater-remove")) return;

    const row = target.closest(".repeater-row");
    if (!row) return;
    row.remove();
    recalcTotal();
  });

  setupSidebarToggle();
  setupProposalPanelCollapse();
  setupPaymentManualToggle();
  setupPaymentFieldToggles();
  setupHeaderMediaToggle();
  setupHeaderLayoutToggle();
  setupAcceptanceModeToggle();
  setupSyncedTableScrollbars();
  bindRowInputs(document);
  setupRepeater("files-list", "add-file-row-button", "file-row-template");
  setupRepeater("stages-list", "add-stage-row-button", "stage-row-template");
  setupRepeater("guidelines-list", "add-guideline-row-button", "guideline-row-template");
  setupRepeater("custom-disciplines-list", "add-custom-discipline-button", "custom-discipline-template");
  setupRepeater("payment-schedule-list", "add-payment-row-button", "payment-row-template");
  setupPaymentScheduleEditor();
  recalcTotal();
})();
