@php
    $position = $position ?? 'right';
    $bottom = $bottom ?? 24;
    $side = $position === 'left' ? 24 : null;
    $sideRight = $position === 'right' ? 24 : null;
@endphp

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/open-dyslexic@5.0.20/index.min.css" media="print" onload="this.media='all'">

<style>
#a11y-toggle {
    position: fixed;
    bottom: {{ $bottom }}px;
    {{ $position }}: {{ $side ?? $sideRight }}px;
    z-index: 9999;
    width: 48px;
    height: 48px;
    border-radius: 50%;
    border: none;
    background: #4f46e5;
    color: #fff;
    font-size: 22px;
    cursor: pointer;
    box-shadow: 0 4px 16px rgba(79,70,229,.35);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform .2s, box-shadow .2s;
}
#a11y-toggle:hover { transform: scale(1.08); box-shadow: 0 6px 24px rgba(79,70,229,.45); }
#a11y-toggle:focus-visible { outline: 3px solid #f59e0b; outline-offset: 3px; }

#a11y-panel {
    position: fixed;
    bottom: calc({{ $bottom }}px + 58px);
    {{ $position }}: {{ $side ?? $sideRight }}px;
    z-index: 9998;
    width: 280px;
    background: #fff;
    border-radius: 16px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 12px 48px rgba(0,0,0,.12);
    padding: 18px;
    transform-origin: bottom {{ $position }};
    transition: opacity .2s, transform .2s;
    opacity: 0;
    transform: scale(.92) translateY(8px);
    pointer-events: none;
}
#a11y-panel.open {
    opacity: 1;
    transform: scale(1) translateY(0);
    pointer-events: auto;
}
#a11y-panel h3 {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .05em;
    color: #1e293b;
    margin: 0 0 14px 0;
    display: flex;
    align-items: center;
    gap: 8px;
}
#a11y-panel h3 small {
    font-size: 9px;
    font-weight: 400;
    color: #94a3b8;
    text-transform: none;
    letter-spacing: 0;
}

.a11y-group {
    margin-bottom: 10px;
}
.a11y-group:last-child { margin-bottom: 0; }

.a11y-group-label {
    font-size: 10px;
    font-weight: 600;
    color: #64748b;
    margin-bottom: 5px;
    display: block;
}

.a11y-row {
    display: flex;
    gap: 6px;
}

.a11y-btn {
    flex: 1;
    padding: 7px 8px;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    background: #f8fafc;
    font-size: 10px;
    font-weight: 600;
    color: #475569;
    cursor: pointer;
    transition: all .15s;
    text-align: center;
    white-space: nowrap;
}
.a11y-btn:hover { background: #f1f5f9; border-color: #cbd5e1; }
.a11y-btn:focus-visible { outline: 2px solid #f59e0b; outline-offset: 2px; }
.a11y-btn.active {
    background: #eef2ff;
    border-color: #4f46e5;
    color: #4f46e5;
}

.a11y-btn-icon {
    font-size: 13px;
    display: block;
    margin-bottom: 2px;
}

.a11y-btn-danger {
    width: 100%;
    padding: 8px;
    border-radius: 8px;
    border: 1px solid #fecaca;
    background: #fef2f2;
    font-size: 10px;
    font-weight: 700;
    color: #dc2626;
    cursor: pointer;
    transition: all .15s;
    margin-top: 6px;
}
.a11y-btn-danger:hover { background: #fee2e2; }

/* ─── Applied states ─── */
html.a11y-text-large { font-size: 120% !important; }
html.a11y-text-xlarge { font-size: 140% !important; }

html.a11y-high-contrast { filter: contrast(1.4); }
html.a11y-high-contrast img,
html.a11y-high-contrast video,
html.a11y-high-contrast svg { filter: contrast(1.2); }

html.a11y-grayscale { filter: grayscale(1); }
html.a11y-grayscale img,
html.a11y-grayscale video { filter: grayscale(1); }

html.a11y-high-contrast.a11y-grayscale { filter: contrast(1.4) grayscale(1); }
html.a11y-high-contrast.a11y-grayscale img,
html.a11y-high-contrast.a11y-grayscale video { filter: contrast(1.2) grayscale(1); }

html.a11y-hide-images img,
html.a11y-hide-images [style*="background-image"],
html.a11y-hide-images figure,
html.a11y-hide-images picture { display: none !important; }

html.a11y-reduce-motion *,
html.a11y-reduce-motion *::before,
html.a11y-reduce-motion *::after {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
}

html.a11y-dyslexic,
html.a11y-dyslexic body,
html.a11y-dyslexic .font-sans,
html.a11y-dyslexic .font-body {
    font-family: 'Open Dyslexic', 'OpenDyslexic', 'Comic Sans MS', 'Lexie Readable', sans-serif !important;
}

html.a11y-spacing p,
html.a11y-spacing li,
html.a11y-spacing .prose,
html.a11y-spacing .text-content {
    line-height: 2 !important;
    letter-spacing: 0.05em !important;
}

html.a11y-large-cursor *,
html.a11y-large-cursor *:hover {
    cursor: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='32' height='32' viewBox='0 0 32 32'%3E%3Ccircle cx='4' cy='4' r='4' fill='%234f46e5' opacity='.8'/%3E%3C/svg%3E") 4 4, auto !important;
}

html.a11y-focus *:focus-visible {
    outline: 3px solid #f59e0b !important;
    outline-offset: 3px !important;
    box-shadow: 0 0 0 6px rgba(245,158,11,.25) !important;
}
</style>

<button id="a11y-toggle" aria-label="{{ __('components.accessibility.title') }}" title="{{ __('components.accessibility.title') }}">
    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="16" cy="4" r="1"/>
        <path d="m18 19 1-7-6 1"/>
        <path d="m5 8 3-3 5.5 3-2.36 3.5"/>
        <path d="M4.24 14.5a5 5 0 0 0 6.88 6"/>
        <path d="M13.76 17.5a5 5 0 0 0-6.88-6"/>
    </svg>
</button>

<div id="a11y-panel" role="dialog" aria-label="{{ __('components.accessibility.settings_title') }}">
    <h3>
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="16" cy="4" r="1"/>
            <path d="m18 19 1-7-6 1"/>
            <path d="m5 8 3-3 5.5 3-2.36 3.5"/>
            <path d="M4.24 14.5a5 5 0 0 0 6.88 6"/>
            <path d="M13.76 17.5a5 5 0 0 0-6.88-6"/>
        </svg>
        {{ __('components.accessibility.title') }}
        <small>{{ __('components.accessibility.parameters') }}</small>
    </h3>

    {{-- Text Size --}}
    <div class="a11y-group">
        <span class="a11y-group-label">{{ __('components.accessibility.text_size') }}</span>
        <div class="a11y-row">
            <button class="a11y-btn" data-a11y="font-size" data-value="normal">
                <span class="a11y-btn-icon">Aa</span>
                {{ __('components.accessibility.normal') }}
            </button>
            <button class="a11y-btn" data-a11y="font-size" data-value="large">
                <span class="a11y-btn-icon" style="font-size:15px">Aa</span>
                {{ __('components.accessibility.large') }}
            </button>
            <button class="a11y-btn" data-a11y="font-size" data-value="xlarge">
                <span class="a11y-btn-icon" style="font-size:17px">Aa</span>
                {{ __('components.accessibility.xlarge') }}
            </button>
        </div>
    </div>

    {{-- Toggles --}}
    <div class="a11y-group">
        <div class="a11y-row" style="flex-wrap:wrap">
            <button class="a11y-btn" style="flex:1;min-width:calc(50% - 3px)" data-a11y="toggle" data-key="contrast">
                <span class="a11y-btn-icon">◐</span>
                {{ __('components.accessibility.contrast') }}
            </button>
            <button class="a11y-btn" style="flex:1;min-width:calc(50% - 3px)" data-a11y="toggle" data-key="grayscale">
                <span class="a11y-btn-icon">⚫</span>
                {{ __('components.accessibility.grayscale') }}
            </button>
            <button class="a11y-btn" style="flex:1;min-width:calc(50% - 3px)" data-a11y="toggle" data-key="hide-images">
                <span class="a11y-btn-icon">🖼</span>
                {{ __('components.accessibility.hide_images') }}
            </button>
            <button class="a11y-btn" style="flex:1;min-width:calc(50% - 3px)" data-a11y="toggle" data-key="motion">
                <span class="a11y-btn-icon">▸</span>
                {{ __('components.accessibility.reduce_motion') }}
            </button>
            <button class="a11y-btn" style="flex:1;min-width:calc(50% - 3px)" data-a11y="toggle" data-key="dyslexic">
                <span class="a11y-btn-icon">D</span>
                {{ __('components.accessibility.dyslexia_font') }}
            </button>
            <button class="a11y-btn" style="flex:1;min-width:calc(50% - 3px)" data-a11y="toggle" data-key="spacing">
                <span class="a11y-btn-icon">↕</span>
                {{ __('components.accessibility.spacing') }}
            </button>
            <button class="a11y-btn" style="flex:1;min-width:calc(50% - 3px)" data-a11y="toggle" data-key="cursor">
                <span class="a11y-btn-icon">↖</span>
                {{ __('components.accessibility.large_cursor') }}
            </button>
            <button class="a11y-btn" style="flex:1;min-width:calc(50% - 3px)" data-a11y="toggle" data-key="focus">
                <span class="a11y-btn-icon">◎</span>
                {{ __('components.accessibility.enhanced_focus') }}
            </button>
        </div>
    </div>

    {{-- Reset --}}
    <button class="a11y-btn-danger" data-a11y="reset">
        ↺ {{ __('components.accessibility.reset_all') }}
    </button>
</div>

<script>
(function () {
    var STORAGE_KEY = 'a11y_prefs';
    var HTML = document.documentElement;

    // ─── Defaults ───
    var SETTINGS = {
        'font-size': 'normal',
        'contrast': false,
        'grayscale': false,
        'hide-images': false,
        'motion': false,
        'dyslexic': false,
        'spacing': false,
        'cursor': false,
        'focus': false,
    };

    // ─── Class map ───
    var CLASS_MAP = {
        'font-size': { 'large': 'a11y-text-large', 'xlarge': 'a11y-text-xlarge' },
        'contrast': 'a11y-high-contrast',
        'grayscale': 'a11y-grayscale',
        'hide-images': 'a11y-hide-images',
        'motion': 'a11y-reduce-motion',
        'dyslexic': 'a11y-dyslexic',
        'spacing': 'a11y-spacing',
        'cursor': 'a11y-large-cursor',
        'focus': 'a11y-focus',
    };

    function loadPrefs() {
        try {
            var saved = localStorage.getItem(STORAGE_KEY);
            if (saved) {
                var parsed = JSON.parse(saved);
                for (var k in SETTINGS) {
                    if (parsed[k] !== undefined) SETTINGS[k] = parsed[k];
                }
            }
        } catch(e) {}
    }

    function savePrefs() {
        try {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(SETTINGS));
        } catch(e) {}
    }

    function applyAll() {
        // Remove all a11y classes
        for (var k in CLASS_MAP) {
            var cls = CLASS_MAP[k];
            if (typeof cls === 'object') {
                for (var v in cls) HTML.classList.remove(cls[v]);
            } else {
                HTML.classList.remove(cls);
            }
        }

        // Apply font-size
        var fontSize = SETTINGS['font-size'];
        if (fontSize !== 'normal' && CLASS_MAP['font-size'][fontSize]) {
            HTML.classList.add(CLASS_MAP['font-size'][fontSize]);
        }

        // Apply toggles
        for (var k in SETTINGS) {
            if (k === 'font-size') continue;
            if (SETTINGS[k] && CLASS_MAP[k]) {
                HTML.classList.add(CLASS_MAP[k]);
            }
        }

        // Update button states
        document.querySelectorAll('[data-a11y]').forEach(function (btn) {
            var action = btn.getAttribute('data-a11y');
            var key = btn.getAttribute('data-key');
            var value = btn.getAttribute('data-value');

            if (action === 'font-size') {
                btn.classList.toggle('active', SETTINGS['font-size'] === (value || 'normal'));
            } else if (action === 'toggle' && key) {
                btn.classList.toggle('active', !!SETTINGS[key]);
            }
        });
    }

    // ─── Init ───
    loadPrefs();
    applyAll();

    // ─── Toggle panel ───
    var toggleBtn = document.getElementById('a11y-toggle');
    var panel = document.getElementById('a11y-panel');
    if (toggleBtn && panel) {
        toggleBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            panel.classList.toggle('open');
        });
        document.addEventListener('click', function (e) {
            if (!panel.contains(e.target) && e.target !== toggleBtn) {
                panel.classList.remove('open');
            }
        });
    }

    // ─── Button handlers ───
    document.querySelectorAll('[data-a11y]').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            var action = btn.getAttribute('data-a11y');
            var key = btn.getAttribute('data-key');
            var value = btn.getAttribute('data-value');

            if (action === 'font-size') {
                SETTINGS['font-size'] = value || 'normal';
            } else if (action === 'toggle' && key) {
                SETTINGS[key] = !SETTINGS[key];
            } else if (action === 'reset') {
                for (var k in SETTINGS) SETTINGS[k] = (k === 'font-size') ? 'normal' : false;
            }

            savePrefs();
            applyAll();
            panel.classList.remove('open');
        });
    });
})();
</script>