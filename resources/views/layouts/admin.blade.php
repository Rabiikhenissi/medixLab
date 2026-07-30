<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Espace Administrateur') - Medix eSanté</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f0f4f8;
            color: #1e293b;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
        }

        /* ── SIDEBAR ─────────────────────────────────────────── */
        .sidebar {
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
            z-index: 100;
            flex-shrink: 0;
        }

        .sidebar-logo {
            width: 38px;
            height: 38px;
            background: linear-gradient(135deg, #0066ff, #00aaff);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 14px;
        }

        .sidebar-nav {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            flex: 1;
        }

        .sidebar-item {
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

        .sidebar-item:hover {
            background: #f1f5f9;
            color: #475569;
        }

        .sidebar-item.active {
            background: linear-gradient(135deg, #0066ff15, #0066ff08);
            color: #0066ff;
            border-left: 2px solid #0066ff;
        }

        .sidebar-item svg {
            width: 20px;
            height: 20px;
        }


        /* ── MAIN LAYOUT ─────────────────────────────────────── */
        .layout {
            margin-left: 64px;
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* ── TOP NAV ─────────────────────────────────────────── */
        .topnav {
            background: #ffffff;
            border-bottom: 1px solid #e8eef4;
            padding: 0 32px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 90;
        }

        .brand {
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
            letter-spacing: -0.3px;
            text-decoration: none;
        }

        .brand span {
            color: #0066ff;
        }

        .topnav-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .nav-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0066ff, #00aaff);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
            color: white;
            flex-shrink: 0;
        }

        .nav-user-info {
            text-align: right;
            line-height: 1.2;
        }

        .nav-user-name {
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
        }

        .nav-user-role {
            font-size: 11px;
            color: #0066ff;
            font-weight: 600;
        }

        .btn-logout {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #64748b;
            transition: all 0.2s;
            text-decoration: none;
        }

        .btn-logout:hover {
            border-color: #f87171;
            color: #ef4444;
            background: #fff5f5;
        }

        .btn-logout svg {
            width: 16px;
            height: 16px;
        }

        /* ── PAGE CONTENT ─────────────────────────────────────── */
        .page-content {
            padding: 36px 36px 48px;
            flex: 1;
            width: 100%;
        }

        .page-header {
            margin-bottom: 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .page-title {
            font-size: 24px;
            font-weight: 800;
            color: #0f172a;
            margin: 0 0 6px 0;
        }

        .page-subtitle {
            font-size: 14px;
            color: #64748b;
            font-weight: 400;
            margin: 0;
        }

        /* ── STAT CARDS ──────────────────────────────────────── */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: #ffffff;
            border: 1px solid #e8eef4;
            border-radius: 14px;
            padding: 20px 24px;
            display: flex;
            align-items: center;
            gap: 16px;
            transition: box-shadow 0.2s, transform 0.2s;
        }

        .stat-card:hover {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.07);
            transform: translateY(-1px);
        }

        .stat-icon {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .stat-icon svg {
            width: 22px;
            height: 22px;
        }

        .stat-icon.blue {
            background: #eff6ff;
            color: #0066ff;
        }

        .stat-icon.green {
            background: #f0fdf4;
            color: #16a34a;
        }

        .stat-icon.orange {
            background: #fff7ed;
            color: #ea580c;
        }

        .stat-icon.purple {
            background: #faf5ff;
            color: #9333ea;
        }

        .stat-label {
            font-size: 12px;
            color: #94a3b8;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .stat-value {
            font-size: 28px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1;
        }

        /* ── DATA SECTION ─────────────────────────────────────── */
        .data-section {
            background: #ffffff;
            border: 1px solid #e8eef4;
            border-radius: 16px;
            overflow: hidden;
            margin-bottom: 24px;
        }

        .data-header {
            padding: 20px 24px;
            border-bottom: 1px solid #e8eef4;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }

        .data-title {
            font-size: 15px;
            font-weight: 700;
            color: #0f172a;
        }

        /* ── FILTERS ──────────────────────────────────────────── */
        .filters-bar {
            padding: 16px 24px;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
            background: #fafcff;
        }

        .filter-group {
            position: relative;
        }

        .filter-label {
            font-size: 11px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: block;
            margin-bottom: 4px;
        }

        .filter-input,
        .filter-select {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 13px;
            color: #1e293b;
            font-family: 'Inter', sans-serif;
            transition: border-color 0.2s;
            outline: none;
        }

        .filter-input:focus,
        .filter-select:focus {
            border-color: #0066ff;
            box-shadow: 0 0 0 3px rgba(0, 102, 255, 0.1);
        }

        .filter-input {
            padding-left: 36px;
            width: 280px;
        }

        .filter-select {
            appearance: none;
            padding-right: 30px;
            cursor: pointer;
        }

        .search-icon {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            color: #94a3b8;
            pointer-events: none;
        }

        .select-arrow {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            width: 14px;
            height: 14px;
            color: #94a3b8;
            pointer-events: none;
        }

        .filter-checkbox-wrap {
            display: flex;
            align-items: center;
            gap: 7px;
            padding: 8px 14px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            cursor: pointer;
            font-size: 13px;
            color: #475569;
            font-weight: 500;
            transition: border-color 0.2s;
        }

        .filter-checkbox-wrap:hover {
            border-color: #0066ff;
        }

        .filter-checkbox-wrap input[type="checkbox"] {
            accent-color: #0066ff;
            cursor: pointer;
        }

        .btn-filter {
            padding: 8px 20px;
            background: #0f172a;
            color: white;
            font-size: 13px;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.2s;
            font-family: 'Inter', sans-serif;
        }

        .btn-filter:hover {
            background: #1e293b;
        }

        /* ── TABLE ────────────────────────────────────────────── */
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table thead tr {
            background: #f8fafc;
            border-bottom: 1px solid #e8eef4;
        }

        .data-table th {
            padding: 11px 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #64748b;
            text-align: left;
            white-space: nowrap;
        }

        .data-table td {
            padding: 14px 20px;
            font-size: 13px;
            color: #374151;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        .data-table tbody tr:last-child td {
            border-bottom: none;
        }

        .data-table tbody tr:hover td {
            background: #fafcff;
        }

        .data-table tbody tr.archived td {
            opacity: 0.55;
        }

        .exam-code {
            font-family: 'SF Mono', 'Consolas', monospace;
            font-size: 12px;
            font-weight: 700;
            color: #0066ff;
            background: #eff6ff;
            padding: 3px 8px;
            border-radius: 6px;
            display: inline-block;
        }

        .exam-name {
            font-weight: 600;
            color: #0f172a;
            font-size: 14px;
        }

        .exam-desc {
            font-size: 12px;
            color: #94a3b8;
            margin-top: 2px;
            max-width: 260px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .category-badge {
            display: inline-flex;
            align-items: center;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            white-space: nowrap;
        }

        .cat-biochemistry {
            background: #eff6ff;
            color: #2563eb;
        }

        .cat-hematology {
            background: #fff1f2;
            color: #e11d48;
        }

        .cat-microbiology {
            background: #f0fdf4;
            color: #16a34a;
        }

        .cat-immunology {
            background: #faf5ff;
            color: #9333ea;
        }

        .cat-urinalysis {
            background: #fff7ed;
            color: #ea580c;
        }

        .cat-other {
            background: #f8fafc;
            color: #475569;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }

        .status-badge .dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .status-active {
            background: #f0fdf4;
            color: #16a34a;
        }

        .status-active .dot {
            background: #16a34a;
        }

        .status-archived {
            background: #f8fafc;
            color: #94a3b8;
        }

        .status-archived .dot {
            background: #cbd5e1;
        }

        .table-action-btn {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background: white;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.15s;
            color: #64748b;
            text-decoration: none;
        }

        .table-action-btn svg {
            width: 14px;
            height: 14px;
        }

        .table-action-btn:hover {
            border-color: #0066ff;
            color: #0066ff;
            background: #eff6ff;
        }

        .table-action-btn.archive-btn:hover {
            border-color: #f59e0b;
            color: #f59e0b;
            background: #fffbeb;
        }

        .table-action-btn.restore-btn:hover {
            border-color: #16a34a;
            color: #16a34a;
            background: #f0fdf4;
        }

        .table-action-btn.delete-btn:hover {
            border-color: #ef4444;
            color: #ef4444;
            background: #fff5f5;
        }

        /* ── BUTTONS ────────────────────────────────────────── */
        .btn-add-exam {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 18px;
            background: linear-gradient(135deg, #0066ff, #0052d4);
            color: white;
            font-size: 13px;
            font-weight: 600;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s;
            font-family: 'Inter', sans-serif;
            box-shadow: 0 4px 12px rgba(0, 102, 255, 0.25);
            text-decoration: none;
        }

        .btn-add-exam:hover {
            background: linear-gradient(135deg, #0052d4, #0041af);
            box-shadow: 0 6px 18px rgba(0, 102, 255, 0.35);
            transform: translateY(-1px);
        }

        .btn-add-exam:active {
            transform: translateY(0);
        }

        .btn-add-exam svg {
            width: 16px;
            height: 16px;
        }

        /* ── EMPTY STATE ──────────────────────────────────────── */
        .empty-state {
            padding: 60px 20px;
            text-align: center;
            color: #94a3b8;
        }

        .empty-state-icon {
            width: 56px;
            height: 56px;
            background: #f1f5f9;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 14px;
        }

        .empty-state-icon svg {
            width: 26px;
            height: 26px;
            color: #cbd5e1;
        }

        .empty-state h3 {
            font-size: 15px;
            font-weight: 600;
            color: #475569;
            margin: 0 0 4px;
        }

        .empty-state p {
            font-size: 13px;
            margin: 0;
        }

        /* ── PAGINATION ────────────────────────────────────────── */
        .pagination-wrap {
            padding: 16px 24px;
            border-top: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
        }

        /* ── ALERT ────────────────────────────────────────────── */
        .alert-success {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 10px;
            padding: 12px 16px;
            margin-bottom: 20px;
            font-size: 13px;
            color: #166534;
            font-weight: 500;
        }

        .alert-success-icon {
            width: 18px;
            height: 18px;
            color: #16a34a;
            flex-shrink: 0;
        }

        /* ── FORMS ────────────────────────────────────────────── */
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-bottom: 16px;
        }

        .form-group:last-child {
            margin-bottom: 0;
        }

        .form-label {
            font-size: 12px;
            font-weight: 600;
            color: #374151;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .required-star {
            color: #ef4444;
            margin-left: 2px;
        }

        .form-control {
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 13.5px;
            color: #0f172a;
            font-family: 'Inter', sans-serif;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
            width: 100%;
        }

        .form-control:focus {
            border-color: #0066ff;
            box-shadow: 0 0 0 3px rgba(0, 102, 255, 0.1);
            background: white;
        }

        .form-control::placeholder {
            color: #94a3b8;
        }

        textarea.form-control {
            resize: none;
        }

        select.form-control {
            appearance: none;
            cursor: pointer;
        }

        .form-errors {
            background: #fff1f2;
            border: 1px solid #fecaca;
            border-radius: 10px;
            padding: 12px 16px;
            margin-bottom: 18px;
        }

        .form-errors ul {
            margin: 0;
            padding-left: 18px;
        }

        .form-errors li {
            font-size: 13px;
            color: #dc2626;
            font-weight: 500;
            margin-bottom: 2px;
        }

        .btn-cancel {
            padding: 10px 20px;
            background: white;
            border: 1.5px solid #e2e8f0;
            color: #64748b;
            font-size: 13px;
            font-weight: 600;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.15s;
            font-family: 'Inter', sans-serif;
            text-decoration: none;
            text-align: center;
            display: inline-block;
        }

        .btn-cancel:hover {
            border-color: #94a3b8;
            color: #374151;
        }

        .btn-submit {
            padding: 10px 24px;
            background: linear-gradient(135deg, #0066ff, #0052d4);
            color: white;
            font-size: 13px;
            font-weight: 700;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s;
            font-family: 'Inter', sans-serif;
            box-shadow: 0 3px 10px rgba(0, 102, 255, 0.3);
            text-decoration: none;
            text-align: center;
            display: inline-block;
        }

        .btn-submit:hover {
            background: linear-gradient(135deg, #0052d4, #0041af);
            box-shadow: 0 5px 16px rgba(0, 102, 255, 0.4);
        }

        .btn-submit:active {
            transform: scale(0.98);
        }

        /* ── PERMISSION MATRIX ────────────────────────────────── */
        .permissions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin-top: 10px;
            margin-bottom: 24px;
        }

        .feature-card {
            background: #ffffff;
            border: 1px solid #e8eef4;
            border-radius: 14px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .feature-name {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 8px;
            margin-bottom: 4px;
        }

        .action-checkbox {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-size: 13px;
            color: #475569;
            font-weight: 500;
        }

        .action-checkbox input[type="checkbox"] {
            accent-color: #0066ff;
            cursor: pointer;
            width: 16px;
            height: 16px;
        }

        /* ── RESPONSIVE ───────────────────────────────────────── */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
                z-index: 200;
            }
            .sidebar.open {
                transform: translateX(0);
            }
            .sidebar-overlay {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(0,0,0,0.4);
                z-index: 190;
            }
            .sidebar-overlay.open {
                display: block;
            }
            .layout {
                margin-left: 0;
            }
            .topnav {
                left: 0;
                padding: 0 16px;
            }
            .page-content {
                padding: 20px 16px 32px;
            }
            .stats-grid {
                grid-template-columns: 1fr;
            }
            .filter-input {
                width: 100%;
            }
            .filters-bar {
                flex-direction: column;
                align-items: stretch;
            }
            .form-row {
                grid-template-columns: 1fr;
            }
            .nav-user-info {
                display: none;
            }
            .mobile-menu-btn {
                display: flex !important;
            }
        }

        .mobile-menu-btn {
            display: none;
            width: 36px;
            height: 36px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background: white;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #64748b;
            flex-shrink: 0;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(16px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .anim {
            animation: fadeInUp 0.5s ease both;
        }

        .anim-1 {
            animation-delay: 0.05s;
        }

        .anim-2 {
            animation-delay: 0.1s;
        }

        .anim-3 {
            animation-delay: 0.15s;
        }

        .anim-4 {
            animation-delay: 0.2s;
        }
    </style>
</head>

<body>

    <!-- Mobile Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <!-- ══════════════════════════════════════════ SIDEBAR -->
    <aside class="sidebar" id="adminSidebar">
        <div class="sidebar-logo">
            <svg width="20" height="20" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.315 48.315 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z" />
            </svg>
        </div>

        <nav class="sidebar-nav">
            @foreach ($sidebarFeatures as $feature)
                @if (!$feature->view_permission || auth()->user()->hasPermission($feature->view_permission))
                    @php
                        $isActive = false;
                        if ($feature->route_name && Route::has($feature->route_name)) {
                            $routePattern = str_replace('.index', '.*', $feature->route_name);
                            $isActive = request()->routeIs($feature->route_name) || request()->routeIs($routePattern);
                        }
                        $href =
                            $feature->route_name && Route::has($feature->route_name)
                            ? route($feature->route_name)
                            : '#';
                        $style = $feature->code === 'settings' ? 'margin-top: auto;' : '';
                    @endphp
                    <a href="{{ $href }}" class="sidebar-item {{ $isActive ? 'active' : '' }}" title="{{ $feature->name }}"
                        style="{{ $style }}">
                        @if ($feature->icon)
                            <x-dynamic-component :component="'heroicon-o-' . $feature->icon" class="sidebar-icon" />
                        @endif
                    </a>
                @endif
            @endforeach
        </nav>

      
    </aside>

    <!-- ══════════════════════════════════════════ MAIN -->
    <div class="layout">

        <!-- TOP NAV -->
        <header class="topnav">
            @php
                $dashboardRoute = 'home';
                if (auth()->check()) {
                    if (auth()->user()->admin) $dashboardRoute = 'admin.dashboard';
                    elseif (auth()->user()->doctor) $dashboardRoute = 'doctor.dashboard';
                    elseif (auth()->user()->patient) $dashboardRoute = 'patient.dashboard';
                    elseif (auth()->user()->staff) $dashboardRoute = 'center.dashboard';
                }
                $logoutRoute = 'admin.logout';
                if (auth()->check()) {
                    if (auth()->user()->doctor) $logoutRoute = 'doctor.logout';
                    elseif (auth()->user()->patient) $logoutRoute = 'patient.logout';
                    elseif (auth()->user()->staff) $logoutRoute = 'center.logout';
                }
            @endphp
            <a href="{{ route($dashboardRoute) }}" class="brand">Medix <span>eSanté</span></a>
            <button class="mobile-menu-btn" onclick="toggleSidebar()" aria-label="Menu">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                </svg>
            </button>
            <div class="topnav-right">

                <div class="nav-user-info">
                    <div class="nav-user-name">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</div>
                    <div class="nav-user-role">
                        {{ auth()->user()->group ? auth()->user()->group->name : 'Administrateur' }}
                    </div>
                </div>
                <a href="{{ route('profile.show') }}" class="nav-avatar" title="Mon Profil" style="text-decoration: none; display: flex; align-items: center; justify-content: center;">
                    {{ strtoupper(substr(auth()->user()->first_name, 0, 1)) }}{{ strtoupper(substr(auth()->user()->last_name, 0, 1)) }}
                </a>
                <form action="{{ route($logoutRoute) }}" method="POST" class="m-0 p-0">
                    @csrf
                    <button type="submit" class="btn-logout" title="Se déconnecter">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                        </svg>
                    </button>
                </form>
            </div>
        </header>

        <!-- PAGE CONTENT -->
        <div class="page-content">

            <!-- Alert Success -->
            @if (session('success'))
                <div class="alert-success" id="success-alert">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <svg class="alert-success-icon" fill="none" stroke="currentColor" stroke-width="2.5"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        {{ session('success') }}
                    </div>
                    <button onclick="document.getElementById('success-alert').remove()"
                        style="background:none;border:none;cursor:pointer;color:#94a3b8;padding:0;">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            @endif

            <!-- Alert Error -->
            @if (session('error'))
                <div class="alert-success" id="error-alert"
                    style="background:#fff1f2; border-color:#fecaca; color:#dc2626;">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <svg class="alert-success-icon" style="color:#ef4444;" fill="none" stroke="currentColor"
                            stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                        </svg>
                        {{ session('error') }}
                    </div>
                    <button onclick="document.getElementById('error-alert').remove()"
                        style="background:none;border:none;cursor:pointer;color:#cbd5e1;padding:0;">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            @endif

            @php $adminRoute = request()->route()->getName(); @endphp
            <div class="flex items-center gap-2 text-[10px] uppercase tracking-wider font-extrabold text-[#64748b] bg-[#f8fafc]/40 px-4 py-2.5 rounded-xl border border-[#e2e8f0]/40 mb-6 select-none">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-[#0066ff] transition">Espace Admin</a>
                <svg class="w-3 h-3 text-[#94a3b8]" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                @if($adminRoute === 'admin.dashboard')
                    <span class="text-[#1e293b]">Tableau de Bord</span>
                @elseif($adminRoute === 'admin.users.index')
                    <span class="text-[#1e293b]">Utilisateurs</span>
                @elseif($adminRoute === 'admin.laboratories.index')
                    <span class="text-[#1e293b]">Établissements</span>
                @elseif($adminRoute === 'admin.exams.index')
                    <span class="text-[#1e293b]">Examens</span>
                @elseif($adminRoute === 'admin.available-exams.index')
                    <a href="{{ route('admin.exams.index') }}" class="hover:text-[#0066ff] transition">Examens</a>
                    <svg class="w-3 h-3 text-[#94a3b8]" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                    <span class="text-[#1e293b]">Examens Disponibles</span>
                @elseif($adminRoute === 'profile.show')
                    <span class="text-[#1e293b]">Mon Profil</span>
                @else
                    <span class="text-[#1e293b]">{{ ucfirst(str_replace(['admin.', '-'], ['', ' '], $adminRoute)) }}</span>
                @endif
            </div>

            <!-- Page Header -->
            <div class="page-header anim">
                <div>
                    <h1 class="page-title">@yield('page-title')</h1>
                    <p class="page-subtitle">@yield('page-subtitle')</p>
                </div>
                <div>
                    @yield('header-actions')
                </div>
            </div>

            <!-- Content -->
            @yield('content')

        </div><!-- /page-content -->
    </div><!-- /layout -->

    <script>
        function toggleSidebar() {
            document.getElementById('adminSidebar').classList.toggle('open');
            document.getElementById('sidebarOverlay').classList.toggle('open');
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