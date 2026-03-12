(function () {
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

  bindRowInputs(document);
  setupRepeater("files-list", "add-file-row-button", "file-row-template");
  setupRepeater("stages-list", "add-stage-row-button", "stage-row-template");
  setupRepeater("custom-disciplines-list", "add-custom-discipline-button", "custom-discipline-template");
  recalcTotal();
})();
