<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('components.layouts_patient.default_title')) - Medix eSanté</title>

    <!-- Google Fonts: Outfit & Instrument Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400..700;1,400..700&family=Outfit:wght@100..900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body.patient-shell {
            margin: 0;
            padding: 0;
            display: flex;
            min-height: 100vh;
            background: #F8FAFC;
        }

        /* ── SIDEBAR ──────────────────────────────────────── */
        .patient-sidebar {
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

        .patient-sidebar-logo {
            width: 38px;
            height: 38px;
            background: linear-gradient(135deg, #0D9488, #0a7068);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 14px;
            flex-shrink: 0;
        }

        .patient-sidebar-nav {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            flex: 1;
            width: 100%;
        }

        .patient-sidebar-item {
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

        .patient-sidebar-item:hover {
            background: #f0fdfa;
            color: #0D9488;
        }

        .patient-sidebar-item.active {
            background: rgba(13, 148, 136, 0.08);
            color: #0D9488;
            border-left: 2px solid #0D9488;
        }

        .patient-sidebar-item svg {
            width: 20px;
            height: 20px;
        }

        .patient-sidebar-bottom {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            margin-top: auto;
            padding-bottom: 8px;
        }

        /* ── TOP NAV ──────────────────────────────────────── */
        .patient-topnav {
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

        .patient-topnav .brand {
            font-size: 16px;
            font-weight: 800;
            color: #0f172a;
            text-decoration: none;
            letter-spacing: -0.3px;
        }

        .patient-topnav .brand span {
            color: #0D9488;
        }

        .patient-topnav .topnav-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .patient-topnav .nav-user-name {
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.2;
        }

        .patient-topnav .nav-user-role {
            font-size: 10px;
            color: #0D9488;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .patient-topnav .nav-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0D9488, #0a7068);
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

        .patient-topnav .btn-logout {
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

        .patient-topnav .btn-logout:hover {
            background: #fee2e2;
            color: #ef4444;
            border-color: #fca5a5;
        }

        .patient-topnav .btn-logout svg {
            width: 16px;
            height: 16px;
        }

        /* ── MAIN CONTENT ──────────────────────────────────── */
        .patient-main {
            margin-left: 64px;
            padding-top: 58px;
            min-height: 100vh;
            width: calc(100% - 64px);
        }

        .patient-content-wrapper {
            padding: 24px 32px;
        }

        @media (max-width: 768px) {
            .patient-sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
                z-index: 300;
            }
            .patient-sidebar.open {
                transform: translateX(0);
            }
            .patient-sidebar-overlay {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(0,0,0,0.4);
                z-index: 250;
            }
            .patient-sidebar-overlay.open {
                display: block;
            }
            .patient-topnav {
                left: 0;
            }
            .patient-main {
                margin-left: 0;
                width: 100%;
            }
            .patient-content-wrapper {
                padding: 16px;
            }
            .patient-topnav .nav-user-name,
            .patient-topnav .nav-user-role {
                display: none;
            }
            .mobile-menu-btn {
                display: flex !important;
            }
        }
        @media (max-width: 640px) {
            .patient-content-wrapper {
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

<body class="patient-shell antialiased text-[#1e293b] font-sans">

    <!-- Mobile Sidebar Overlay -->
    <div class="patient-sidebar-overlay" id="patientSidebarOverlay" onclick="togglePatientSidebar()"></div>

    <!-- ══ SIDEBAR ══ -->
    <aside class="patient-sidebar" id="patientSidebar">
        <div class="patient-sidebar-logo">
            <svg width="20" height="20" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M4 20V8l8 7 8-7v12" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v4M10 5h4" stroke-width="2.2" />
            </svg>
        </div>

        <nav class="patient-sidebar-nav">
            @php
                $patientSidebarFeatures = \App\Models\Feature::where('is_archive', false)
                    ->where('is_sidebar', true)
                    ->orderBy('order', 'asc')
                    ->get();
            @endphp

            @foreach ($patientSidebarFeatures as $feature)
                @if ($feature->code !=="profile" && (!$feature->view_permission || auth()->user()->hasPermission($feature->view_permission)))
                    @php
                        $isActive = false;
                        if ($feature->route_name && \Illuminate\Support\Facades\Route::has($feature->route_name) && $route = request()->route()) {
                            $current = $route->getName();
                            $prefix = preg_replace('/\.index$/', '', $feature->route_name);
                            $isActive = $current === $feature->route_name || str_starts_with($current, $prefix . '.');
                        }
                        $href = $feature->route_name && \Illuminate\Support\Facades\Route::has($feature->route_name)
                            ? route($feature->route_name)
                            : '#';
                    @endphp
                    <a href="{{ $href }}"
                       class="patient-sidebar-item {{ $isActive ? 'active' : '' }}"
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
    <nav class="patient-topnav">
        <a href="{{ route('patient.dashboard') }}" class="brand">Medix <span>eSanté</span></a>

        <div class="topnav-right">
            <button class="mobile-menu-btn" onclick="togglePatientSidebar()" aria-label="{{ __('layout.menu') }}">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                </svg>
            </button>
            <x-language-switcher />
            <div>
                <div class="nav-user-name">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</div>
                <div class="nav-user-role">{{ __('layout.role_patient') }}</div>
            </div>
            <a href="{{ route('profile.show') }}" class="nav-avatar" title="{{ __('auth.profile') }}">
                {{ strtoupper(substr(auth()->user()->first_name, 0, 1)) }}{{ strtoupper(substr(auth()->user()->last_name, 0, 1)) }}
            </a>
            <form action="{{ route('patient.logout') }}" method="POST" style="margin:0;padding:0;">
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
    <main class="patient-main">
        <div class="patient-content-wrapper">

            <!-- Session Alerts -->
            @if (session('success'))
                <div class="flex items-center justify-between bg-[#f0fdf4] border border-[#bbf7d0] rounded-xl p-4 mb-6 text-sm text-[#166534] font-medium"
                    id="patient-success-alert">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-[#16a34a]" fill="none" stroke="currentColor" stroke-width="2.5"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button onclick="document.getElementById('patient-success-alert').remove()"
                        class="text-[#94a3b8] hover:text-[#475569] transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            @endif

            @if (session('error'))
                <div class="flex items-center justify-between bg-[#fff1f2] border border-[#fecaca] rounded-xl p-4 mb-6 text-sm text-[#dc2626] font-medium"
                    id="patient-error-alert">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-[#ef4444]" fill="none" stroke="currentColor" stroke-width="2.5"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button onclick="document.getElementById('patient-error-alert').remove()"
                        class="text-[#cbd5e1] hover:text-[#94a3b8] transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            @endif

            @php $patientRoute = request()->route()->getName(); @endphp
            <div class="flex items-center gap-2 text-[10px] uppercase tracking-wider font-extrabold text-[#64748b] bg-[#f8fafc]/40 px-4 py-2.5 rounded-xl border border-[#e2e8f0]/40 mb-6 select-none">
                <a href="{{ route('patient.dashboard') }}" class="hover:text-[#0D9488] transition">{{ __('components.layouts_patient.patient_space') }}</a>
                <svg class="w-3 h-3 text-[#94a3b8]" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                @if($patientRoute === 'patient.dashboard')
                    <span class="text-[#1e293b]">{{ __('components.layouts_patient.dashboard') }}</span>
                @elseif($patientRoute === 'patient.analytics')
                    <span class="text-[#1e293b]">{{ __('components.layouts_patient.my_statistics') }}</span>
                @elseif($patientRoute === 'patient.medical-history')
                    <span class="text-[#1e293b]">{{ __('components.layouts_patient.medical_history') }}</span>
                @elseif($patientRoute === 'profile.show')
                    <span class="text-[#1e293b]">{{ __('auth.profile') }}</span>
                @else
                    <span class="text-[#1e293b]">{{ ucfirst(str_replace(['patient.', '-'], ['', ' '], $patientRoute)) }}</span>
                @endif
            </div>

            @yield('content')
        </div>
    </main>

    <script>
        function togglePatientSidebar() {
            document.getElementById('patientSidebar').classList.toggle('open');
            document.getElementById('patientSidebarOverlay').classList.toggle('open');
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function swalConfirmSubmit(form, message) {
            Swal.fire({
                title: @json(__('common.confirm')),
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0066FF',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: @json(__('common.confirm')),
                cancelButtonText: @json(__('common.cancel'))
            }).then((result) => {
                if (result.isConfirmed) form.submit();
            });
            return false;
        }
    </script>
    @yield('scripts')
    <x-loading-overlay />
    @include('components.accessibility-widget')
 <x-tour />
</body>

</html>
