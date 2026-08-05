<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Espace Médecin') - Medix eSanté</title>

    <!-- Google Fonts: Outfit & Instrument Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400..700;1,400..700&family=Outfit:wght@100..900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body.doctor-shell {
            margin: 0;
            padding: 0;
            display: flex;
            min-height: 100vh;
            background: #F8FAFC;
        }

        /* ── SIDEBAR ──────────────────────────────────────── */
        .doctor-sidebar {
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

        .doctor-sidebar-logo {
            width: 38px;
            height: 38px;
            background: linear-gradient(135deg, #0066FF, #0052CC);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 14px;
            flex-shrink: 0;
        }

        .doctor-sidebar-nav {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            flex: 1;
            width: 100%;
        }

        .doctor-sidebar-item {
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

        .doctor-sidebar-item:hover {
            background: #eff6ff;
            color: #0066FF;
        }

        .doctor-sidebar-item.active {
            background: rgba(0, 102, 255, 0.08);
            color: #0066FF;
            border-left: 2px solid #0066FF;
        }

        .doctor-sidebar-item svg {
            width: 20px;
            height: 20px;
        }

        .doctor-sidebar-bottom {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            margin-top: auto;
            padding-bottom: 8px;
        }

        /* ── TOP NAV ──────────────────────────────────────── */
        .doctor-topnav {
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

        .doctor-topnav .brand {
            font-size: 16px;
            font-weight: 800;
            color: #0f172a;
            text-decoration: none;
            letter-spacing: -0.3px;
        }

        .doctor-topnav .brand span {
            color: #0066FF;
        }

        .doctor-topnav .topnav-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .doctor-topnav .nav-user-name {
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.2;
        }

        .doctor-topnav .nav-user-role {
            font-size: 10px;
            color: #0066FF;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .doctor-topnav .nav-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0066FF, #0052CC);
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

        .doctor-topnav .btn-logout {
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

        .doctor-topnav .btn-logout:hover {
            background: #fee2e2;
            color: #ef4444;
            border-color: #fca5a5;
        }

        .doctor-topnav .btn-logout svg {
            width: 16px;
            height: 16px;
        }

        /* ── MAIN CONTENT ──────────────────────────────────── */
        .doctor-main {
            margin-left: 64px;
            padding-top: 58px;
            min-height: 100vh;
            width: calc(100% - 64px);
        }

        .doctor-content-wrapper {
            padding: 24px 32px;
        }

        @media (max-width: 768px) {
            .doctor-sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
                z-index: 300;
            }
            .doctor-sidebar.open {
                transform: translateX(0);
            }
            .doctor-sidebar-overlay {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(0,0,0,0.4);
                z-index: 250;
            }
            .doctor-sidebar-overlay.open {
                display: block;
            }
            .doctor-topnav {
                left: 0;
            }
            .doctor-main {
                margin-left: 0;
                width: 100%;
            }
            .doctor-content-wrapper {
                padding: 16px;
            }
            .doctor-topnav .nav-user-name,
            .doctor-topnav .nav-user-role {
                display: none;
            }
            .mobile-menu-btn {
                display: flex !important;
            }
        }
        @media (max-width: 640px) {
            .doctor-content-wrapper {
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

<body class="doctor-shell antialiased text-[#1e293b] font-sans">

    <!-- Mobile Sidebar Overlay -->
    <div class="doctor-sidebar-overlay" id="doctorSidebarOverlay" onclick="toggleDoctorSidebar()"></div>

    <!-- ══ SIDEBAR ══ -->
    <aside class="doctor-sidebar" id="doctorSidebar">
        <div class="doctor-sidebar-logo">
            <svg width="20" height="20" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M22 12h-4l-3 9L9 3l-3 9H2" />
            </svg>
        </div>

        <nav class="doctor-sidebar-nav">
            @php
                $currentRoute = request()->route()->getName();
            @endphp

            <!-- Dashboard -->
            <a href="{{ route('doctor.dashboard') }}"
               class="doctor-sidebar-item {{ $currentRoute === 'doctor.dashboard' ? 'active' : '' }}"
               title="Tableau de bord">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:20px;height:20px;">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75" />
                </svg>
            </a>

            <!-- My Patients -->
            <a href="{{ route('doctor.my-patients') }}"
               class="doctor-sidebar-item {{ $currentRoute === 'doctor.my-patients' ? 'active' : '' }}"
               title="Mes Patients">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:20px;height:20px;">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15 19l-7-7 7-7" />
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </a>

            <!-- Patient Search -->
            <a href="{{ route('doctor.patient-search') }}"
               class="doctor-sidebar-item {{ $currentRoute === 'doctor.patient-search' ? 'active' : '' }}"
               title="Rechercher Patient">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:20px;height:20px;">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </a>

            <!-- Exam Groups -->
            <a href="{{ route('doctor.exam-groups.index') }}"
               class="doctor-sidebar-item {{ str_starts_with($currentRoute, 'doctor.exam-groups') ? 'active' : '' }}"
               title="Groupes d'Examens">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:20px;height:20px;">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </a>
        </nav>
    </aside>

    <!-- ══ TOP NAV ══ -->
    <nav class="doctor-topnav">
        <a href="{{ route('doctor.dashboard') }}" class="brand">Medix <span>eSanté</span></a>

        <div class="topnav-right">
            <button class="mobile-menu-btn" onclick="toggleDoctorSidebar()" aria-label="{{ __('layout.menu') }}">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                </svg>
            </button>
            <x-language-switcher />
            <div>
                <div class="nav-user-name">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</div>
                <div class="nav-user-role">{{ __('layout.role_doctor') }}</div>
            </div>
            <a href="{{ route('profile.show') }}" class="nav-avatar" title="{{ __('auth.profile') }}">
                {{ strtoupper(substr(auth()->user()->first_name, 0, 1)) }}{{ strtoupper(substr(auth()->user()->last_name, 0, 1)) }}
            </a>
            <form action="{{ route('doctor.logout') }}" method="POST" style="margin:0;padding:0;">
                @csrf
                <button type="submit" class="btn-logout" title="{{ __('auth.logout') }}">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                    </svg>
                </button>
            </form>
        </div>
    </nav>

    <!-- ══ MAIN CONTENT ══ -->
    <main class="doctor-main">
        <div class="doctor-content-wrapper">

            <!-- Session Alerts -->
            @if (session('success'))
                <div class="flex items-center justify-between bg-[#f0fdf4] border border-[#bbf7d0] rounded-xl p-4 mb-6 text-sm text-[#166534] font-medium"
                    id="doctor-success-alert">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-[#16a34a]" fill="none" stroke="currentColor" stroke-width="2.5"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button onclick="document.getElementById('doctor-success-alert').remove()"
                        class="text-[#94a3b8] hover:text-[#475569] transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            @endif

            @if (session('error'))
                <div class="flex items-center justify-between bg-[#fff1f2] border border-[#fecaca] rounded-xl p-4 mb-6 text-sm text-[#dc2626] font-medium"
                    id="doctor-error-alert">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-[#ef4444]" fill="none" stroke="currentColor" stroke-width="2.5"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button onclick="document.getElementById('doctor-error-alert').remove()"
                        class="text-[#cbd5e1] hover:text-[#94a3b8] transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            @endif

            @php $doctorRoute = request()->route()->getName(); @endphp
            <div class="flex items-center gap-2 text-[10px] uppercase tracking-wider font-extrabold text-[#64748b] bg-[#f8fafc]/40 px-4 py-2.5 rounded-xl border border-[#e2e8f0]/40 mb-6 select-none">
                <a href="{{ route('doctor.dashboard') }}" class="hover:text-[#0066FF] transition">Espace Médecin</a>
                <svg class="w-3 h-3 text-[#94a3b8]" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                @if($doctorRoute === 'doctor.dashboard')
                    <span class="text-[#1e293b]">Tableau de Bord</span>
                @elseif(str_starts_with($doctorRoute, 'doctor.exam-groups'))
                    <span class="text-[#1e293b]">Groupes d'Examens</span>
                @elseif($doctorRoute === 'doctor.my-patients')
                    <span class="text-[#1e293b]">Mes Patients</span>
                @elseif($doctorRoute === 'doctor.patient-search')
                    <a href="{{ route('doctor.my-patients') }}" class="hover:text-[#0066FF] transition">Mes Patients</a>
                    <svg class="w-3 h-3 text-[#94a3b8]" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                    <span class="text-[#1e293b]">Rechercher Patient</span>
                @elseif($doctorRoute === 'profile.show')
                    <span class="text-[#1e293b]">Mon Profil</span>
                @else
                    <span class="text-[#1e293b]">{{ ucfirst(str_replace(['doctor.', '-'], ['', ' '], $doctorRoute)) }}</span>
                @endif
            </div>

            @yield('content')
        </div>
    </main>

    <script>
        function toggleDoctorSidebar() {
            document.getElementById('doctorSidebar').classList.toggle('open');
            document.getElementById('doctorSidebarOverlay').classList.toggle('open');
        }
    </script>
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
    @include('components.accessibility-widget')
</body>

</html>
