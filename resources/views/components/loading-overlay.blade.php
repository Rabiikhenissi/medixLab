<div id="global-loading-overlay" class="fixed inset-0 z-[99999] flex flex-col items-center justify-center bg-slate-950/40 backdrop-blur-md opacity-0 pointer-events-none transition-opacity duration-300 ease-in-out">
    <div class="flex flex-col items-center gap-4 bg-white/10 backdrop-blur-lg px-8 py-6 rounded-2xl border border-white/20 shadow-2xl animate-fade-in select-none">
        <!-- Modern Animated Circular Loader -->
        <div class="relative w-12 h-12">
            <!-- Background track -->
            <svg class="w-full h-full text-white/20" viewBox="0 0 38 38" fill="none" stroke="currentColor" stroke-width="3">
                <circle cx="19" cy="19" r="16"></circle>
            </svg>
            <!-- Spinning indicator -->
            <svg class="absolute inset-0 w-full h-full text-white animate-spin" viewBox="0 0 38 38" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round">
                <path d="M35 19a16 16 0 0 1-16 16M3 19a16 16 0 0 1 16-16"></path>
            </svg>
        </div>
        <span id="loading-overlay-text" class="text-xs font-semibold text-white/90 tracking-widest uppercase">Chargement...</span>
    </div>
</div>

<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: scale(0.95); }
        to { opacity: 1; transform: scale(1); }
    }
    .animate-fade-in {
        animation: fadeIn 0.25s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
</style>

<script>
    (function() {
        let activeRequests = 0;
        let overlayTimeout = null;
        let dashboardLoading = false;
        const silentUrls = [
            '/unread-count',
            '/get-notifications',
            '/notifications',
            '/access-requests',
            '/save-location',
        ];

        function shouldBeSilent(url) {
            if (!url) return false;
            const urlString = typeof url === 'string' ? url : (url.url || '');
            return silentUrls.some(silent => urlString.includes(silent));
        }

        function showOverlay(message = 'Chargement en cours...', instant = false) {
            const overlay = document.getElementById('global-loading-overlay');
            const textEl = document.getElementById('loading-overlay-text');
            if (!overlay) return;
            
            if (textEl && message) {
                textEl.textContent = message;
            }
            
            clearTimeout(overlayTimeout);
            
            // Show after a small delay (200ms) to avoid flashing on instant actions
            // unless instant=true (e.g. page load)
            var delay = instant ? 0 : 200;
            overlayTimeout = setTimeout(() => {
                overlay.classList.remove('pointer-events-none', 'opacity-0');
                overlay.classList.add('opacity-100');
            }, 200);
        }

        function hideOverlay() {
            if (dashboardLoading) return;
            const overlay = document.getElementById('global-loading-overlay');
            if (!overlay) return;
            
            clearTimeout(overlayTimeout);
            
            if (overlay.classList.contains('opacity-100')) {
                overlay.classList.remove('opacity-100');
                overlay.classList.add('opacity-0');
                overlayTimeout = setTimeout(() => {
                    overlay.classList.add('pointer-events-none');
                }, 300); // Match Tailwind duration-300
            } else {
                overlay.classList.add('pointer-events-none');
            }
        }

        // 1. Monitor page transitions via links
        document.addEventListener('click', (e) => {
            const link = e.target.closest('a');
            if (link && link.href && !link.target && !link.hasAttribute('download') && !link.getAttribute('href').startsWith('#')) {
                try {
                    const url = new URL(link.href);
                    // Only intercept same-origin and actual path changes
                    if (url.origin === window.location.origin && (url.pathname !== window.location.pathname || url.search !== window.location.search)) {
                        showOverlay('Navigation...');
                    }
                } catch (err) {
                    // Ignore malformed URLs
                }
            }
        });

        // 2. Monitor form submissions — skip if form uses swalConfirmSubmit (handled by SweetAlert)
        document.addEventListener('submit', (e) => {
            const form = e.target;
            if (form && !form.target && !form.getAttribute('onsubmit')) {
                showOverlay('Traitement en cours...');
            }
        });

        // 3. Monitor fetch requests
        const originalFetch = window.fetch;
        window.fetch = async function(...args) {
            const url = args[0];
            const silent = shouldBeSilent(url);
            
            if (!silent) {
                activeRequests++;
                showOverlay('Requête en cours...');
            }
            
            try {
                return await originalFetch(...args);
            } finally {
                if (!silent) {
                    activeRequests--;
                    if (activeRequests <= 0) {
                        hideOverlay();
                    }
                }
            }
        };

        // 4. Monitor XMLHttpRequest (AJAX)
        const originalSend = XMLHttpRequest.prototype.send;
        const originalOpen = XMLHttpRequest.prototype.open;

        XMLHttpRequest.prototype.open = function(method, url, ...args) {
            this._url = url;
            return originalOpen.call(this, method, url, ...args);
        };

        XMLHttpRequest.prototype.send = function(...args) {
            const silent = shouldBeSilent(this._url);
            
            if (!silent) {
                activeRequests++;
                showOverlay('Requête en cours...');
                
                this.addEventListener('loadend', () => {
                    activeRequests--;
                    if (activeRequests <= 0) {
                        hideOverlay();
                    }
                });
            }
            return originalSend.apply(this, args);
        };

        // Expose globally so other scripts can use the same loader
        // __showLoading(message, instant) — instant=true skips the 200ms delay
        // __hideLoading() — hides the overlay
        // __setDashboardLoading(bool) — prevents fetch interceptor from hiding while dashboard loads
        window.__showLoading = showOverlay;
        window.__hideLoading = function() {
            dashboardLoading = false;
            hideOverlay();
        };
        window.__setDashboardLoading = function(val) { dashboardLoading = val; };

        // Hide overlay on page show/load (useful when using browser back/forward cache and form submissions)
        window.addEventListener('pageshow', function() { if (activeRequests <= 0) hideOverlay(); });
        window.addEventListener('load', function() { if (activeRequests <= 0) hideOverlay(); });
    })();
</script>
