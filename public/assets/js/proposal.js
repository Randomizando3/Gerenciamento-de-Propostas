(function () {
  const ctx = window.PROPOSAL_CONTEXT || {};
  if (ctx.previewMode) {
    initModalOnly();
    return;
  }

  const token = ctx.token;
  if (!token) return;

  const keyBase = `proposal_tracking_${token}`;
  const storedSession = localStorage.getItem(`${keyBase}_session`);
  const storedView = localStorage.getItem(`${keyBase}_view`);

  const state = {
    sessionId: storedSession || null,
    viewId: storedView ? Number(storedView) : null,
    maxScroll: 0,
    visibleSections: new Set(),
    sectionDelta: {},
    lastHeartbeat: Date.now(),
    startedAt: Date.now(),
  };

  function postJson(url, payload, options) {
    return fetch(url, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload),
      keepalive: Boolean(options && options.keepalive),
    }).then((response) => response.json().catch(() => ({})));
  }

  function sendBeaconJson(url, payload) {
    const blob = new Blob([JSON.stringify(payload)], { type: "application/json" });
    if (navigator.sendBeacon) {
      navigator.sendBeacon(url, blob);
      return;
    }
    fetch(url, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload),
      keepalive: true,
    });
  }

  function initTracking() {
    postJson(ctx.trackInitUrl, {
      token,
      session_id: state.sessionId,
    }).then((response) => {
      if (!response || !response.ok) return;
      state.viewId = Number(response.view_id);
      state.sessionId = response.session_id;
      localStorage.setItem(`${keyBase}_session`, String(state.sessionId));
      localStorage.setItem(`${keyBase}_view`, String(state.viewId));
    });
  }

  function calcScrollDepth() {
    const scrollTop = window.scrollY || document.documentElement.scrollTop || 0;
    const scrollHeight = document.documentElement.scrollHeight - window.innerHeight;
    if (scrollHeight <= 0) return 0;
    const depth = (scrollTop / scrollHeight) * 100;
    return Math.max(0, Math.min(100, depth));
  }

  function sectionTimerTick() {
    state.visibleSections.forEach((name) => {
      if (!state.sectionDelta[name]) state.sectionDelta[name] = 0;
      state.sectionDelta[name] += 1;
    });
  }

  function flushHeartbeat(force) {
    if (!state.viewId) return;
    const now = Date.now();
    const elapsedSeconds = Math.max(1, Math.round((now - state.lastHeartbeat) / 1000));
    state.lastHeartbeat = now;

    const payload = {
      view_id: state.viewId,
      scroll_depth: state.maxScroll,
      elapsed_seconds: elapsedSeconds,
      section_times: state.sectionDelta,
    };
    state.sectionDelta = {};

    if (force) {
      sendBeaconJson(ctx.trackHeartbeatUrl, payload);
      return;
    }
    postJson(ctx.trackHeartbeatUrl, payload).catch(() => null);
  }

  function trackEvent(type, payload) {
    if (!state.viewId) return;
    const body = {
      view_id: state.viewId,
      event_type: type,
      payload: payload || {},
    };
    postJson(ctx.trackEventUrl, body, { keepalive: true }).catch(() => null);
  }

  function setupObservers() {
    const sections = document.querySelectorAll("[data-track-section]");
    if (!sections.length) return;
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          const key = entry.target.getAttribute("data-track-section");
          if (!key) return;
          if (entry.isIntersecting) {
            state.visibleSections.add(key);
          } else {
            state.visibleSections.delete(key);
          }
        });
      },
      { threshold: 0.3 }
    );
    sections.forEach((section) => observer.observe(section));
  }

  function setupEvents() {
    const downloadButtons = [
      document.getElementById("download-pdf-btn"),
      document.getElementById("cta-download-btn"),
    ].filter(Boolean);

    downloadButtons.forEach((button) => {
      button.addEventListener("click", () => {
        trackEvent("download_pdf", { source: button.id || "download" });
      });
    });

    const signButtons = [
      document.getElementById("hero-sign-btn"),
      document.getElementById("sign-now-link"),
    ].filter(Boolean);

    signButtons.forEach((button) => {
      button.addEventListener("click", () => {
        trackEvent("click_sign", { source: button.id || "sign" });
      });
    });

    document.querySelectorAll("[data-track-event]").forEach((el) => {
      const type = el.getAttribute("data-track-event");
      if (!type) return;
      el.addEventListener("click", () => trackEvent(type));
    });
  }

  function setupScrollWatcher() {
    state.maxScroll = calcScrollDepth();
    window.addEventListener(
      "scroll",
      () => {
        const depth = calcScrollDepth();
        if (depth > state.maxScroll) {
          state.maxScroll = depth;
        }
      },
      { passive: true }
    );
  }

  function initModalOnly() {
    const modal = document.getElementById("acceptance-modal");
    const openBtn = document.getElementById("open-acceptance-modal");
    const cancelBtn = document.getElementById("cancel-acceptance");
    const checkbox = document.getElementById("accept-terms-checkbox");
    const confirmBtn = document.getElementById("confirm-acceptance");

    if (!modal || !openBtn || !cancelBtn || !checkbox || !confirmBtn) return;

    function closeModal() {
      modal.classList.remove("active");
      modal.setAttribute("aria-hidden", "true");
    }

    openBtn.addEventListener("click", () => {
      modal.classList.add("active");
      modal.setAttribute("aria-hidden", "false");
    });

    cancelBtn.addEventListener("click", closeModal);
    modal.addEventListener("click", (event) => {
      if (event.target === modal) closeModal();
    });

    checkbox.addEventListener("change", () => {
      confirmBtn.classList.toggle("disabled", !checkbox.checked);
    });
  }

  initModalOnly();
  initTracking();
  setupObservers();
  setupEvents();
  setupScrollWatcher();

  setInterval(sectionTimerTick, 1000);
  setInterval(() => flushHeartbeat(false), 15000);

  const confirmBtn = document.getElementById("confirm-acceptance");
  if (confirmBtn) {
    confirmBtn.addEventListener("click", () => {
      if (confirmBtn.classList.contains("disabled")) {
        return;
      }
      trackEvent("accept_terms", { source: "modal_confirm" });
      trackEvent("click_sign", { source: "modal_confirm" });
    });
  }

  window.addEventListener("visibilitychange", () => {
    if (document.visibilityState === "hidden") {
      flushHeartbeat(true);
    }
  });

  window.addEventListener("beforeunload", () => {
    flushHeartbeat(true);
  });
})();

