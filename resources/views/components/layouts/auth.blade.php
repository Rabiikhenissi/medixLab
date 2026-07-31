<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Medix eSanté' }}</title>

    <!-- Google Fonts: Outfit & Instrument Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400..700;1,400..700&family=Outfit:wght@100..900&display=swap"
        rel="stylesheet">

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* ── Unified Auth Shell ────────────────────────────── */
        body.auth-shell {
            margin: 0;
            padding: 0;
            display: flex;
            min-height: 100vh;
            background: #F8FAFC;
        }

        /* ── SIDEBAR ──────────────────────────────────────── */
        .auth-sidebar {
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

        .auth-sidebar-logo {
            width: 38px;
            height: 38px;
            background: linear-gradient(135deg, #0066ff, #00aaff);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 14px;
            flex-shrink: 0;
        }

        .auth-sidebar-nav {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            flex: 1;
            width: 100%;
        }

        .auth-sidebar-item {
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

        .auth-sidebar-item:hover {
            background: #f1f5f9;
            color: #475569;
        }

        .auth-sidebar-item.active {
            background: linear-gradient(135deg, #0066ff15, #0066ff08);
            color: #0066ff;
            border-left: 2px solid #0066ff;
        }

        .auth-sidebar-item svg {
            width: 20px;
            height: 20px;
        }

        .auth-sidebar-bottom {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            margin-top: auto;
            padding-bottom: 8px;
        }

        /* ── TOP NAV ──────────────────────────────────────── */
        .auth-topnav {
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

        .auth-topnav .brand {
            font-size: 16px;
            font-weight: 800;
            color: #0f172a;
            text-decoration: none;
            letter-spacing: -0.3px;
        }

        .auth-topnav .brand span {
            color: #0066ff;
        }

        .auth-topnav .topnav-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .auth-topnav .nav-user-name {
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.2;
        }

        .auth-topnav .nav-user-role {
            font-size: 10px;
            color: #0066ff;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .auth-topnav .nav-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0066ff, #00aaff);
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

        .auth-topnav .btn-logout {
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

        .auth-topnav .btn-logout:hover {
            background: #fee2e2;
            color: #ef4444;
            border-color: #fca5a5;
        }

        .auth-topnav .btn-logout svg {
            width: 16px;
            height: 16px;
        }

        /* ── MAIN CONTENT ──────────────────────────────────── */
        .auth-main {
            margin-left: 64px;
            padding-top: 58px;
            min-height: 100vh;
            width: calc(100% - 64px);
            display: flex;
            flex-direction: column;
        }

        .auth-content {
            flex: 1;
            padding: 0;
        }

        @media (max-width: 640px) {
            .auth-topnav .nav-user-name,
            .auth-topnav .nav-user-role {
                display: none;
            }
        }
    </style>
</head>

<body class="auth-shell antialiased text-[#1e293b] font-sans">
    <!-- ══ SIDEBAR ══ -->
    @auth
    <aside class="auth-sidebar">
        <div class="auth-sidebar-logo">
            <svg width="20" height="20" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.315 48.315 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z" />
            </svg>
        </div>

        <nav class="auth-sidebar-nav">
            @foreach ($sidebarFeatures ?? [] as $feature)
                @if (!$feature->view_permission || auth()->user()->hasPermission($feature->view_permission))
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
                       class="auth-sidebar-item {{ $isActive ? 'active' : '' }}"
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

        <div class="auth-sidebar-bottom">
            <!-- Profile -->
            <a href="{{ route('profile.show') }}" class="auth-sidebar-item" title="Mon Profil">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:20px;height:20px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </a>
        </div>
    </aside>
    @endauth

    <!-- ══ TOP NAV ══ -->
    @auth
    <nav class="auth-topnav">
        @php
            $dashboardRoute = 'home';
            if (auth()->user()->doctor) $dashboardRoute = 'doctor.dashboard';
            elseif (auth()->user()->patient) $dashboardRoute = 'patient.dashboard';
            elseif (auth()->user()->staff) $dashboardRoute = 'center.dashboard';
            elseif (auth()->user()->admin) $dashboardRoute = 'admin.dashboard';

            $logoutRoute = 'admin.logout';
            if (auth()->user()->doctor) $logoutRoute = 'doctor.logout';
            elseif (auth()->user()->patient) $logoutRoute = 'patient.logout';
            elseif (auth()->user()->staff) $logoutRoute = 'center.logout';
        @endphp
        <a href="{{ route($dashboardRoute) }}" class="brand">Medix <span>eSanté</span></a>

        <div class="topnav-right">
            <div>
                <div class="nav-user-name">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</div>
                <div class="nav-user-role">
                    @if(auth()->user()->doctor) Médecin
                    @elseif(auth()->user()->patient) Patient
                    @elseif(auth()->user()->staff) Établissement
                    @else {{ auth()->user()->group?->name ?? 'Utilisateur' }}
                    @endif
                </div>
            </div>
            <a href="{{ route('profile.show') }}" class="nav-avatar" title="Mon Profil">
                {{ strtoupper(substr(auth()->user()->first_name, 0, 1)) }}{{ strtoupper(substr(auth()->user()->last_name, 0, 1)) }}
            </a>
            <form action="{{ route($logoutRoute) }}" method="POST" style="margin:0;padding:0;">
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
    @endauth

    <!-- ══ MAIN CONTENT ══ -->
    @auth
    <main class="auth-main">
        <div class="auth-content">
            {{ $slot }}
        </div>
    </main>
    @else
    {{-- Guest layout (login/register pages) - no sidebar --}}
    <x-background-animation />
    <div class="relative z-10 min-h-screen flex flex-col justify-center items-center p-4 md:p-8 w-full">
        {{ $slot }}
    </div>
    @endauth
    <x-loading-overlay />
</body>

</html>