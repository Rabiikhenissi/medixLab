/**
 * Medix guided onboarding tour.
 *
 * Renders a dark overlay with a "spotlight" hole around the current step's
 * target, keeps everything else unclickable, and shows a tooltip. Steps are
 * read from #medix-tour-root and auto-started on first login (data-autostart).
 */
(() => {
    const root = document.getElementById('medix-tour-root');
    if (!root) return;

    let steps;
    try {
        steps = JSON.parse(root.dataset.steps || '[]');
    } catch {
        steps = [];
    }
    if (!steps.length) return;

    let ui;
    try {
        ui = JSON.parse(root.dataset.ui || '{}');
    } catch {
        ui = {};
    }

    const autostart = root.dataset.autostart === '1';
    const completeUrl = root.dataset.completeUrl || '';
    const csrfToken = () => (document.querySelector('meta[name="csrf-token"]') || {}).content || '';

    const PAD = 10;
    const state = { active: false, index: 0, target: null, cleanup: null };

    // ── build the tour DOM ────────────────────────────────────────────────
    const host = document.createElement('div');
    host.id = 'medix-tour-host';
    host.innerHTML = `
        <div class="mt-mask" data-mask="top"></div>
        <div class="mt-mask" data-mask="right"></div>
        <div class="mt-mask" data-mask="bottom"></div>
        <div class="mt-mask" data-mask="left"></div>
        <div class="mt-hole-ring"></div>
        <div class="mt-card">
            <div class="mt-dots"></div>
            <h4 class="mt-title"></h4>
            <p class="mt-text"></p>
            <p class="mt-hint"></p>
            <div class="mt-actions">
                <button type="button" class="mt-skip"></button>
                <button type="button" class="mt-next"></button>
            </div>
        </div>`;
    document.body.appendChild(host);

    const masks = {
        top: host.querySelector('[data-mask="top"]'),
        right: host.querySelector('[data-mask="right"]'),
        bottom: host.querySelector('[data-mask="bottom"]'),
        left: host.querySelector('[data-mask="left"]'),
    };
    const ring = host.querySelector('.mt-hole-ring');
    const card = host.querySelector('.mt-card');
    const dotsEl = host.querySelector('.mt-dots');
    const titleEl = host.querySelector('.mt-title');
    const textEl = host.querySelector('.mt-text');
    const hintEl = host.querySelector('.mt-hint');
    const skipBtn = host.querySelector('.mt-skip');
    const nextBtn = host.querySelector('.mt-next');
    const replayBtn = document.getElementById('medix-tour-replay');

    const label = (key, fallback) => ui[key] || fallback;
    skipBtn.textContent = label('skip', 'Passer');
    nextBtn.textContent = label('next', 'Suivant');

    const vw = () => document.documentElement.clientWidth;
    const vh = () => document.documentElement.clientHeight;

    // ── helpers ───────────────────────────────────────────────────────────
    function findTarget(step) {
        const selectors = Array.isArray(step.target) ? step.target : [step.target];
        for (const selector of selectors) {
            if (!selector) continue;
            try {
                const el = document.querySelector(selector);
                if (el) return el;
            } catch {
                /* invalid selector — try the next one */
            }
        }
        return null;
    }

    function resolve(index) {
        for (let i = index; i < steps.length; i++) {
            const step = steps[i];
            if (!step.target) return { index: i, step, target: null };
            const target = findTarget(step);
            if (target) return { index: i, step, target };
        }
        return null;
    }

    function rectOf(target) {
        const r = target.getBoundingClientRect();
        return {
            top: Math.max(0, r.top - PAD),
            left: Math.max(0, r.left - PAD),
            right: Math.min(vw(), r.right + PAD),
            bottom: Math.min(vh(), r.bottom + PAD),
            width: r.width + PAD * 2,
            height: r.height + PAD * 2,
        };
    }

    function position(target) {
        if (!target) {
            masks.top.style.cssText = 'top:0;left:0;width:100%;height:100%;';
            masks.right.style.cssText = 'top:0;left:0;width:0;height:0;';
            masks.bottom.style.cssText = 'top:0;left:0;width:0;height:0;';
            masks.left.style.cssText = 'top:0;left:0;width:0;height:0;';
            ring.style.display = 'none';
            card.style.top = '50%';
            card.style.left = '50%';
            card.style.transform = 'translate(-50%, -50%)';
            return;
        }

        const r = rectOf(target);
        masks.top.style.cssText = `top:0;left:0;width:100%;height:${r.top}px;`;
        masks.bottom.style.cssText = `top:${r.bottom}px;left:0;width:100%;height:${vh() - r.bottom}px;`;
        masks.left.style.cssText = `top:${r.top}px;left:0;width:${r.left}px;height:${r.height}px;`;
        masks.right.style.cssText = `top:${r.top}px;left:${r.right}px;width:${vw() - r.right}px;height:${r.height}px;`;

        ring.style.display = 'block';
        ring.style.top = `${r.top}px`;
        ring.style.left = `${r.left}px`;
        ring.style.width = `${r.width}px`;
        ring.style.height = `${r.height}px`;

        const cardW = card.offsetWidth || 340;
        const cardH = card.offsetHeight || 160;
        let top;
        if (r.bottom + 16 + cardH <= vh()) {
            top = r.bottom + 16;
        } else if (r.top - 16 - cardH >= 0) {
            top = r.top - 16 - cardH;
        } else {
            top = Math.max(12, (vh() - cardH) / 2);
        }
        const left = Math.min(
            Math.max(12, r.left + r.width / 2 - cardW / 2),
            Math.max(12, vw() - cardW - 12),
        );
        card.style.top = `${top}px`;
        card.style.left = `${left}px`;
        card.style.transform = 'none';
    }

    function renderDots() {
        dotsEl.innerHTML = '';
        steps.forEach((_, i) => {
            const dot = document.createElement('span');
            dot.className = 'mt-dot' + (i === state.index ? ' on' : '');
            dotsEl.appendChild(dot);
        });
    }

    // ── action steps (wait for the user to actually do it) ────────────────
    function detachAction() {
        if (state.cleanup) {
            state.cleanup();
            state.cleanup = null;
        }
    }

    function attachAction(target, step) {
        detachAction();
        if (!step.action || !target) return;
        const type = step.action.type;

        if (type === 'input') {
            const handler = () => {
                if (target.value && target.value.trim().length > 0) advance();
            };
            target.addEventListener('input', handler);
            state.cleanup = () => target.removeEventListener('input', handler);
        } else if (type === 'click') {
            const handler = (e) => { e.preventDefault(); advance(); };
            const keyHandler = (e) => {
                if (e.key === 'Enter') { e.preventDefault(); advance(); }
            };
            target.addEventListener('click', handler);
            target.addEventListener('keydown', keyHandler);
            state.cleanup = () => {
                target.removeEventListener('click', handler);
                target.removeEventListener('keydown', keyHandler);
            };
        } else if (type === 'submit') {
            const handler = (e) => { e.preventDefault(); advance(); };
            target.addEventListener('submit', handler);
            state.cleanup = () => target.removeEventListener('submit', handler);
        }
    }

    // ── tour flow ─────────────────────────────────────────────────────────
    function show(index) {
        const resolved = resolve(index);
        if (!resolved) {
            finish();
            return;
        }

        state.index = resolved.index;
        state.target = resolved.target;

        titleEl.textContent = resolved.step.title || '';
        textEl.textContent = resolved.step.text || '';
        hintEl.textContent = resolved.step.action ? label('hint', '') : '';
        nextBtn.textContent =
            resolved.index === steps.length - 1
                ? label('finish', 'Terminer')
                : label('next', 'Suivant');
        renderDots();

        const isFixed = resolved.target && getComputedStyle(resolved.target).position === 'fixed';
        if (resolved.target && !isFixed) {
            try {
                resolved.target.scrollIntoView({ block: 'center', behavior: 'smooth' });
            } catch {
                /* non-scrollable target */
            }
        }

        requestAnimationFrame(() => {
            host.classList.add('active');
            position(resolved.target);
            attachAction(resolved.target, resolved.step);
        });
    }

    function advance() {
        detachAction();
        show(state.index + 1);
    }

    function complete() {
        if (!completeUrl) return;
        fetch(completeUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken(),
            },
            body: JSON.stringify({}),
        }).catch(() => {});
    }

    function finish() {
        detachAction();
        host.classList.remove('active');
        state.active = false;
        complete();
    }

    function start() {
        if (state.active) return;
        state.active = true;
        show(0);
    }

    // ── events ────────────────────────────────────────────────────────────
    skipBtn.addEventListener('click', finish);
    nextBtn.addEventListener('click', () => {
        if (state.index >= steps.length - 1) {
            finish();
        } else {
            advance();
        }
    });
    window.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && state.active) finish();
    });

    if (replayBtn) {
        replayBtn.addEventListener('click', () => {
            if (state.active) return;
            state.index = 0;
            state.active = true;
            show(0);
        });
    }

    let ticking = false;
    const reposition = () => {
        if (!state.active) return;
        position(state.target);
    };
    window.addEventListener('scroll', () => {
        if (!ticking) {
            ticking = true;
            requestAnimationFrame(() => {
                reposition();
                ticking = false;
            });
        }
    }, { passive: true });
    window.addEventListener('resize', reposition);

    // ── init ──────────────────────────────────────────────────────────────
    if (autostart) {
        const boot = () => setTimeout(start, 600);
        if (document.readyState === 'complete') {
            boot();
        } else {
            window.addEventListener('load', boot);
        }
    }
})();
