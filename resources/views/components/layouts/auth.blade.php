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
        /* ── Global Navbar ────────────────────────────────── */
        .global-navbar {
            position: sticky;
            top: 0;
            z-index: 200;
            width: 100%;
            background: rgba(255, 255, 255, 0.82);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            height: 58px;
            box-shadow: 0 1px 8px rgba(0, 0, 0, 0.04);
        }

        .global-navbar .navbar-brand {
            font-size: 17px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.3px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .global-navbar .navbar-brand span {
            color: #0066ff;
        }

        .global-navbar .navbar-brand .brand-icon {
            width: 30px;
            height: 30px;
            background: linear-gradient(135deg, #0066ff, #00aaff);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .global-navbar .navbar-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .global-navbar .navbar-user {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .global-navbar .navbar-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0066ff, #00aaff);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 12px;
            color: white;
            flex-shrink: 0;
        }

        .global-navbar .navbar-user-info {
            line-height: 1.25;
        }

        .global-navbar .navbar-user-name {
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
        }

        .global-navbar .navbar-user-role {
            font-size: 10px;
            color: #0066ff;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        /* Language switcher pill */
        .lang-switcher {
            display: flex;
            align-items: center;
            gap: 3px;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 3px;
        }

        .lang-switcher a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 26px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.15s;
            color: #64748b;
        }

        .lang-switcher a.active {
            background: #0066ff;
            color: white;
            box-shadow: 0 2px 6px rgba(0, 102, 255, 0.25);
        }

        .lang-switcher a:not(.active):hover {
            background: #e2e8f0;
        }

        @media (max-width: 640px) {
            .global-navbar .navbar-user-info {
                display: none;
            }

            .global-navbar {
                padding: 0 16px;
            }
        }
    </style>
</head>

<body class="bg-[#F8FAFC] text-[#1e293b] antialiased min-h-screen relative overflow-x-hidden font-sans">
    <!-- Reusable Background Animation -->
    <x-background-animation />

    <!-- ── Global Navbar ── -->
    <nav class="global-navbar">
        <!-- Brand -->
        <a href="{{ route('home') }}" class="navbar-brand">
            <div class="brand-icon">
                <svg width="16" height="16" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.315 48.315 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z" />
                </svg>
            </div>
            Medix <span>eSanté</span>
        </a>

        <!-- Right side -->
        <div class="navbar-right">


            @auth
                <!-- User Info -->
                <div class="navbar-user">
                    <div class="navbar-avatar">
                        {{ strtoupper(substr(auth()->user()->first_name, 0, 1)) }}{{ strtoupper(substr(auth()->user()->last_name, 0, 1)) }}
                    </div>
                    <div class="navbar-user-info">
                        <div class="navbar-user-name">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</div>
                        <div class="navbar-user-role">
                            @if(auth()->user()->doctor) Médecin
                            @elseif(auth()->user()->patient) Patient
                            @elseif(auth()->user()->staff) Établissement
                            @else {{ auth()->user()->group?->name ?? 'Utilisateur' }}
                            @endif
                        </div>
                    </div>
                </div>
            @endauth
        </div>
    </nav>

    <!-- Main Wrapper -->
    <div class="relative z-10 min-h-screen flex flex-col justify-center items-center p-4 md:p-8">
        {{ $slot }}
    </div>
</body>

</html>