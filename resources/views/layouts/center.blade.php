<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Espace Etablissement') - Medix eSanté</title>

    <!-- Google Fonts: Outfit & Instrument Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400..700;1,400..700&family=Outfit:wght@100..900&display=swap"
        rel="stylesheet">

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body.center-shell {
            margin: 0;
            padding: 0;
            display: flex;
            min-height: 100vh;
            background: #F8FAFC;
        }

        /* ── SIDEBAR ──────────────────────────────────────── */
        .center-sidebar {
            width: 64px;
            background: #ffffff;
            border-right: 1px solid #e8eef4;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 18px 0;
            gap: 6px;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            z-index: 200;
            flex-shrink: 0;
        }

        .center-sidebar-logo {
            width: 38px;
            height: 38px;
            background: linear-gradient(135deg, #7C3AED, #5B21B6);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 14px;
            flex-shrink: 0;
        }

        .center-sidebar-nav {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            flex: 1;
            width: 100%;
        }

        .center-sidebar-item {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.2s, color 0.2s;
            color: #94a3b8;
            text-decoration: none;
            position: relative;
        }

        .center-sidebar-item:hover {
            background: #f5f3ff;
            color: #7C3AED;
        }

        .center-sidebar-item.active {
            background: rgba(124, 58, 237, 0.08);
            color: #7C3AED;
            border-left: 2px solid #7C3AED;
        }

        .center-sidebar-item svg {
            width: 20px;
            height: 20px;
        }

        .center-sidebar-bottom {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            margin-top: auto;
            padding-bottom: 8px;
        }

        /* ── TOP NAV ──────────────────────────────────────── */
        .center-topnav {
            position: fixed;
            top: 0;
            left: 64px;
            right: 0;
            height: 58px;
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            z-index: 150;
            box-shadow: 0 1px 8px rgba(0, 0, 0, 0.04);
        }

        .center-topnav .brand {
            font-size: 16px;
            font-weight: 800;
            color: #0f172a;
            text-decoration: none;
            letter-spacing: -0.3px;
        }

        .center-topnav .brand span {
            color: #7C3AED;
        }

        .center-topnav .topnav-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .center-topnav .nav-user-name {
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.2;
        }

        .center-topnav .nav-user-role {
            font-size: 10px;
            color: #7C3AED;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .center-topnav .nav-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: linear-gradient(135deg, #7C3AED, #5B21B6);
            color: white;
            font-weight: 700;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            cursor: pointer;
            text-decoration: none;
        }

        .center-topnav .btn-logout {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            color: #64748b;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }

        .center-topnav .btn-logout:hover {
            background: #fee2e2;
            color: #ef4444;
            border-color: #fca5a5;
        }

        .center-topnav .btn-logout svg {
            width: 16px;
            height: 16px;
        }

        /* ── MAIN CONTENT ──────────────────────────────────── */
        .center-main {
            margin-left: 64px;
            padding-top: 58px;
            min-height: 100vh;
            width: calc(100% - 64px);
        }

        .center-content-wrapper {
            padding: 24px 32px;
        }

        @media (max-width: 768px) {
            .center-sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
                z-index: 300;
            }
            .center-sidebar.open {
                transform: translateX(0);
            }
            .center-sidebar-overlay {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(0,0,0,0.4);
                z-index: 250;
            }
            .center-sidebar-overlay.open {
                display: block;
            }
            .center-topnav {
                left: 0;
            }
            .center-main {
                margin-left: 0;
                width: 100%;
            }
            .center-content-wrapper {
                padding: 16px;
            }
            .center-topnav .nav-user-name,
            .center-topnav .nav-user-role {
                display: none;
            }
            .mobile-menu-btn {
                display: flex !important;
            }
            .flex.gap-2.border-b {
                overflow-x: auto;
                flex-wrap: nowrap;
                -webkit-overflow-scrolling: touch;
                scrollbar-width: none;
            }
            .flex.gap-2.border-b::-webkit-scrollbar {
                display: none;
            }
            .flex.gap-2.border-b > a {
                white-space: nowrap;
                flex-shrink: 0;
            }
        }
        @media (max-width: 640px) {
            .center-content-wrapper {
                padding: 12px;
            }
        }

        .mobile-menu-btn {
            display: none;
            width: 34px;
            height: 34px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #64748b;
            flex-shrink: 0;
        }
    </style>

    @yield('styles')
</head>

<body class="center-shell antialiased text-[#1e293b] font-sans">

    <!-- Mobile Sidebar Overlay -->
    <div class="center-sidebar-overlay" id="centerSidebarOverlay" onclick="toggleCenterSidebar()"></div>

    <!-- ══ SIDEBAR ══ -->
    <aside class="center-sidebar" id="centerSidebar">
        <div class="center-sidebar-logo">
            <svg width="20" height="20" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M4 20V8l8 7 8-7v12" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v4M10 5h4" stroke-width="2.2" />
            </svg>
        </div>

        <nav class="center-sidebar-nav">
            @foreach ($sidebarFeatures ?? [] as $feature)
                @if ($feature->code !== 'profile' && (!$feature->view_permission || auth()->user()->hasPermission($feature->view_permission)))
                    @php
                        $isActive = false;
                        if ($feature->route_name && \Illuminate\Support\Facades\Route::has($feature->route_name)) {
                            $current = request()->route()->getName();
                            $prefix = preg_replace('/\.index$/', '', $feature->route_name);
                            $isActive = $current === $feature->route_name || str_starts_with($current, $prefix . '.');
                        }
                        $href = $feature->route_name && \Illuminate\Support\Facades\Route::has($feature->route_name)
                            ? route($feature->route_name)
                            : '#';
                    @endphp
                    <a href="{{ $href }}"
                       class="center-sidebar-item {{ $isActive ? 'active' : '' }}"
                       title="{{ $feature->name }}">
                        @if ($feature->icon)
                            <x-dynamic-component :component="'heroicon-o-' . $feature->icon" style="width:20px;height:20px;" />
                        @else
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:20px;height:20px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        @endif
                    </a>
                @endif
            @endforeach

        </nav>


    </aside>

    <!-- ══ TOP NAV ══ -->
    <nav class="center-topnav">
        <a href="{{ route('center.dashboard') }}" class="brand">Medix <span>eSanté</span></a>

        <div class="topnav-right">
            <button class="mobile-menu-btn" onclick="toggleCenterSidebar()" aria-label="Menu">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                </svg>
            </button>
            {{-- Notification Bell --}}
            <div class="relative" id="centerNotifWrapper">
                <button onclick="centerToggleNotifPanel()" class="relative w-10 h-10 bg-white border border-[#e2e8f0] rounded-xl flex items-center justify-center hover:bg-[#f8fafc] transition cursor-pointer">
                    <svg class="w-5 h-5 text-[#64748b]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                    </svg>
                    <span id="centerNotifBadge" class="hidden absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center">0</span>
                </button>
                <div id="centerNotifPanel" class="hidden fixed top-20 right-4 md:absolute md:top-12 md:right-0 w-80 max-w-[calc(100vw-2rem)] bg-white border border-[#e2e8f0] rounded-2xl shadow-2xl z-50 overflow-hidden">
                    <div class="flex items-center justify-between px-4 py-3 border-b border-[#f1f5f9]">
                        <span class="text-xs font-bold text-[#1e293b] uppercase tracking-wider">Notifications</span>
                        <button onclick="centerMarkAllRead()" class="text-[10px] font-bold text-[#7C3AED] hover:underline cursor-pointer">Tout marquer lu</button>
                    </div>
                    <div id="centerNotifList" class="max-h-72 overflow-y-auto divide-y divide-[#f1f5f9]">
                        <div class="px-4 py-6 text-center text-xs text-[#94a3b8]">Chargement...</div>
                    </div>
                </div>
            </div>

            <div>
                <div class="nav-user-name">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</div>
                <div class="nav-user-role">Établissement</div>
            </div>
            <a href="{{ route('profile.show') }}" class="nav-avatar" title="Mon Profil">
                {{ strtoupper(substr(auth()->user()->first_name, 0, 1)) }}{{ strtoupper(substr(auth()->user()->last_name, 0, 1)) }}
            </a>
            <form action="{{ route('center.logout') }}" method="POST" style="margin:0;padding:0;">
                @csrf
                <button type="submit" class="btn-logout" title="Se déconnecter">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                    </svg>
                </button>
            </form>
        </div>
    </nav>

    <!-- ══ MAIN CONTENT ══ -->
    <main class="center-main">
        <div class="center-content-wrapper">

            <!-- Session Alerts -->
            @if (session('success'))
                <div class="flex items-center justify-between bg-[#f0fdf4] border border-[#bbf7d0] rounded-xl p-4 mb-6 text-sm text-[#166534] font-medium"
                    id="center-success-alert">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-[#16a34a]" fill="none" stroke="currentColor" stroke-width="2.5"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button onclick="document.getElementById('center-success-alert').remove()"
                        class="text-[#94a3b8] hover:text-[#475569] transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            @endif

            @if (session('error'))
                <div class="flex items-center justify-between bg-[#fff1f2] border border-[#fecaca] rounded-xl p-4 mb-6 text-sm text-[#dc2626] font-medium"
                    id="center-error-alert">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-[#ef4444]" fill="none" stroke="currentColor" stroke-width="2.5"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button onclick="document.getElementById('center-error-alert').remove()"
                        class="text-[#cbd5e1] hover:text-[#94a3b8] transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            @endif

            <!-- Navigation Tabs -->
            @php
                $route = request()->route()->getName();
            @endphp

            

            <!-- Page Content -->
            <div class="mt-4">
                <div class="flex items-center gap-2 text-[10px] uppercase tracking-wider font-extrabold text-[#64748b] bg-[#f8fafc]/40 px-4 py-2.5 rounded-xl border border-[#e2e8f0]/40 mb-6 select-none">
                    <a href="{{ route('center.dashboard') }}" class="hover:text-[#7C3AED] transition">Espace Labo</a>
                    <svg class="w-3 h-3 text-[#94a3b8]" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                    @if($route === 'center.dashboard')
                        <span class="text-[#1e293b]">Tableau de Bord</span>
                    @elseif($route === 'center.working-hours')
                        <a href="{{ route('center.working-hours') }}" class="hover:text-[#7C3AED] transition">Horaires</a>
                        <svg class="w-3 h-3 text-[#94a3b8]" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                        <span class="text-[#1e293b]">Exceptions</span>
                    @elseif($route === 'center.consumables')
                        <a href="{{ route('center.consumables') }}" class="hover:text-[#7C3AED] transition">Stock</a>
                        <svg class="w-3 h-3 text-[#94a3b8]" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                        <span class="text-[#1e293b]">Consommables</span>
                    @elseif($route === 'center.equipment')
                        <a href="{{ route('center.equipment') }}" class="hover:text-[#7C3AED] transition">Équipements</a>
                        <svg class="w-3 h-3 text-[#94a3b8]" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                        <span class="text-[#1e293b]">Maintenance</span>
                    @elseif($route === 'center.exam-requests')
                        <span class="text-[#1e293b]">Demandes d'Analyses</span>
                    @elseif(str_contains($route, 'results.create'))
                        <a href="{{ route('center.exam-requests') }}" class="hover:text-[#7C3AED] transition">Demandes d'Analyses</a>
                        <svg class="w-3 h-3 text-[#94a3b8]" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                        <span class="text-[#1e293b]">Saisir les Résultats</span>
                    @elseif(str_contains($route, 'results.edit'))
                        <a href="{{ route('center.exam-requests') }}" class="hover:text-[#7C3AED] transition">Demandes d'Analyses</a>
                        <svg class="w-3 h-3 text-[#94a3b8]" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                        <span class="text-[#1e293b]">Modifier les Résultats</span>
                    @else
                        <span class="text-[#1e293b]">{{ ucfirst(str_replace(['center.', '-'], ['', ' '], $route)) }}</span>
                    @endif
                </div>

                @yield('content')
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function swalConfirmSubmit(form, message) {
            Swal.fire({
                title: 'Confirmer',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0066FF',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Confirmer',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) form.submit();
            });
            return false;
        }
    </script>
    @yield('scripts')
    <x-loading-overlay />

    <script>
        function toggleCenterSidebar() {
            document.getElementById('centerSidebar').classList.toggle('open');
            document.getElementById('centerSidebarOverlay').classList.toggle('open');
        }
    </script>
    <script>
        // ── Notifications ──
        const centerNotifRoutes = {
            list: '{{ route("center.get-notifications") }}',
            unreadCount: '{{ route("center.unread-count") }}',
            markRead: '{{ route("center.mark-as-read", "__ID__") }}',
            markAllRead: '{{ route("center.mark-all-read") }}'
        };

        function centerToggleNotifPanel() {
            const panel = document.getElementById('centerNotifPanel');
            panel.classList.toggle('hidden');
            if (!panel.classList.contains('hidden')) {
                centerLoadNotifications();
            }
        }

        async function centerLoadNotifications() {
            try {
                const res = await fetch(centerNotifRoutes.list);
                const data = await res.json();
                const list = document.getElementById('centerNotifList');
                if (data.notifications.length === 0) {
                    list.innerHTML = '<div class="px-4 py-6 text-center text-xs text-[#94a3b8]">Aucune notification</div>';
                    return;
                }
                list.innerHTML = data.notifications.map(n => `
                    <div class="px-4 py-3 hover:bg-[#f8fafc] transition cursor-pointer ${n.is_read ? '' : 'bg-[#faf5ff]/50'}" onclick="centerMarkRead(${n.id}, this)">
                        <div class="flex items-start gap-2">
                            <div class="w-2 h-2 rounded-full mt-1.5 flex-shrink-0 ${n.is_read ? 'bg-[#cbd5e1]' : 'bg-[#7C3AED]'}"></div>
                            <div class="flex-1 min-w-0">
                                <div class="text-xs font-bold text-[#1e293b]">${n.title}</div>
                                <div class="text-[11px] text-[#64748b] mt-0.5 line-clamp-2">${n.message}</div>
                                <div class="text-[10px] text-[#94a3b8] mt-1">${n.created_at}</div>
                            </div>
                        </div>
                    </div>
                `).join('');
            } catch (e) {
                console.error('Notification load error:', e);
            }
        }

        async function centerUpdateUnreadBadge() {
            try {
                const res = await fetch(centerNotifRoutes.unreadCount);
                const data = await res.json();
                const badge = document.getElementById('centerNotifBadge');
                if (data.unread_count > 0) {
                    badge.textContent = data.unread_count > 99 ? '99+' : data.unread_count;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            } catch (e) {}
        }

        async function centerMarkRead(id, el) {
            try {
                await fetch(centerNotifRoutes.markRead.replace('__ID__', id), {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });
                centerUpdateUnreadBadge();
                centerLoadNotifications();
            } catch (e) {}
        }

        async function centerMarkAllRead() {
            try {
                await fetch(centerNotifRoutes.markAllRead, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });
                centerUpdateUnreadBadge();
                centerLoadNotifications();
            } catch (e) {}
        }

        document.addEventListener('click', function(e) {
            const wrapper = document.getElementById('centerNotifWrapper');
            if (wrapper && !wrapper.contains(e.target)) {
                document.getElementById('centerNotifPanel').classList.add('hidden');
            }
        });

        centerUpdateUnreadBadge();
        setInterval(centerUpdateUnreadBadge, 15000);
    </script>
    @include('components.accessibility-widget')
</body>

</html>