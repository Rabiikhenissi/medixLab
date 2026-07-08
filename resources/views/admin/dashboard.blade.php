<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Espace Administrateur - Medix eSanté</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * { box-sizing: border-box; }
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

        .sidebar-item:hover { background: #f1f5f9; color: #475569; }
        .sidebar-item.active {
            background: linear-gradient(135deg, #0066ff15, #0066ff08);
            color: #0066ff;
            border-left: 2px solid #0066ff;
        }

        .sidebar-item svg { width: 20px; height: 20px; }

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
        }
        .brand span { color: #0066ff; }

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

        .nav-user-info { text-align: right; line-height: 1.2; }
        .nav-user-name { font-size: 13px; font-weight: 700; color: #0f172a; }
        .nav-user-role { font-size: 11px; color: #0066ff; font-weight: 600; }

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
        .btn-logout:hover { border-color: #f87171; color: #ef4444; background: #fff5f5; }
        .btn-logout svg { width: 16px; height: 16px; }

        /* ── PAGE CONTENT ─────────────────────────────────────── */
        .page-content {
            padding: 36px 36px 48px;
            flex: 1;
            max-width: 1300px;
            width: 100%;
        }

        .page-header {
            margin-bottom: 28px;
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
        .stat-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,0.07); transform: translateY(-1px); }

        .stat-icon {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .stat-icon svg { width: 22px; height: 22px; }
        .stat-icon.blue   { background: #eff6ff; color: #0066ff; }
        .stat-icon.green  { background: #f0fdf4; color: #16a34a; }
        .stat-icon.orange { background: #fff7ed; color: #ea580c; }
        .stat-icon.purple { background: #faf5ff; color: #9333ea; }

        .stat-info {}
        .stat-label { font-size: 12px; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
        .stat-value { font-size: 28px; font-weight: 800; color: #0f172a; line-height: 1; }

        /* ── DATA SECTION ─────────────────────────────────────── */
        .data-section {
            background: #ffffff;
            border: 1px solid #e8eef4;
            border-radius: 16px;
            overflow: hidden;
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

        .filter-group { position: relative; }

        .filter-label {
            font-size: 11px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: block;
            margin-bottom: 4px;
        }

        .filter-input, .filter-select {
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
        .filter-input:focus, .filter-select:focus { border-color: #0066ff; box-shadow: 0 0 0 3px rgba(0,102,255,0.1); }
        .filter-input { padding-left: 36px; width: 280px; }
        .filter-select { appearance: none; padding-right: 30px; cursor: pointer; }

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
        .filter-checkbox-wrap:hover { border-color: #0066ff; }
        .filter-checkbox-wrap input[type="checkbox"] { accent-color: #0066ff; cursor: pointer; }

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
        .btn-filter:hover { background: #1e293b; }

        /* ── TABLE ────────────────────────────────────────────── */
        .data-table { width: 100%; border-collapse: collapse; }

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

        .data-table tbody tr:last-child td { border-bottom: none; }

        .data-table tbody tr:hover td { background: #fafcff; }
        .data-table tbody tr.archived td { opacity: 0.55; }

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

        .exam-name { font-weight: 600; color: #0f172a; font-size: 14px; }
        .exam-desc { font-size: 12px; color: #94a3b8; margin-top: 2px; max-width: 260px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

        .category-badge {
            display: inline-flex;
            align-items: center;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            white-space: nowrap;
        }
        .cat-biochemistry  { background: #eff6ff; color: #2563eb; }
        .cat-hematology    { background: #fff1f2; color: #e11d48; }
        .cat-microbiology  { background: #f0fdf4; color: #16a34a; }
        .cat-immunology    { background: #faf5ff; color: #9333ea; }
        .cat-urinalysis    { background: #fff7ed; color: #ea580c; }
        .cat-other         { background: #f8fafc; color: #475569; }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }
        .status-badge .dot { width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }
        .status-active   { background: #f0fdf4; color: #16a34a; }
        .status-active .dot   { background: #16a34a; }
        .status-archived { background: #f8fafc; color: #94a3b8; }
        .status-archived .dot { background: #cbd5e1; }

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
        }
        .table-action-btn svg { width: 14px; height: 14px; }
        .table-action-btn:hover { border-color: #0066ff; color: #0066ff; background: #eff6ff; }
        .table-action-btn.archive-btn:hover { border-color: #f59e0b; color: #f59e0b; background: #fffbeb; }
        .table-action-btn.restore-btn:hover { border-color: #16a34a; color: #16a34a; background: #f0fdf4; }

        /* ── ADD EXAM BUTTON (center of section) ────────────── */
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
            box-shadow: 0 4px 12px rgba(0,102,255,0.25);
        }
        .btn-add-exam:hover { background: linear-gradient(135deg, #0052d4, #0041af); box-shadow: 0 6px 18px rgba(0,102,255,0.35); transform: translateY(-1px); }
        .btn-add-exam:active { transform: translateY(0); }
        .btn-add-exam svg { width: 16px; height: 16px; }

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
        .empty-state-icon svg { width: 26px; height: 26px; color: #cbd5e1; }
        .empty-state h3 { font-size: 15px; font-weight: 600; color: #475569; margin: 0 0 4px; }
        .empty-state p { font-size: 13px; margin: 0; }

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
        .alert-success-icon { width: 18px; height: 18px; color: #16a34a; flex-shrink: 0; }

        /* ── MODAL ────────────────────────────────────────────── */
        .modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.45);
            backdrop-filter: blur(6px);
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal-box {
            background: #ffffff;
            border-radius: 20px;
            width: 100%;
            max-width: 580px;
            box-shadow: 0 30px 70px rgba(0,0,0,0.2), 0 0 0 1px rgba(0,0,0,0.05);
            overflow: hidden;
            transform: scale(0.95);
            opacity: 0;
            transition: all 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .modal-header {
            background: linear-gradient(135deg, #0066ff, #0052d4);
            padding: 24px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .modal-title-wrap { color: white; }
        .modal-title { font-size: 18px; font-weight: 700; margin: 0 0 2px; }
        .modal-subtitle { font-size: 13px; opacity: 0.75; }

        .modal-close {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: rgba(255,255,255,0.15);
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            transition: background 0.15s;
        }
        .modal-close:hover { background: rgba(255,255,255,0.25); }
        .modal-close svg { width: 16px; height: 16px; }

        .modal-body { padding: 28px; }

        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .form-group { display: flex; flex-direction: column; gap: 6px; margin-bottom: 16px; }
        .form-group:last-child { margin-bottom: 0; }

        .form-label {
            font-size: 12px;
            font-weight: 600;
            color: #374151;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .required-star { color: #ef4444; margin-left: 2px; }

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
        .form-control:focus { border-color: #0066ff; box-shadow: 0 0 0 3px rgba(0,102,255,0.1); background: white; }
        .form-control::placeholder { color: #94a3b8; }
        textarea.form-control { resize: none; }
        select.form-control { appearance: none; cursor: pointer; }

        .form-errors {
            background: #fff1f2;
            border: 1px solid #fecaca;
            border-radius: 10px;
            padding: 12px 16px;
            margin-bottom: 18px;
        }
        .form-errors ul { margin: 0; padding-left: 18px; }
        .form-errors li { font-size: 13px; color: #dc2626; font-weight: 500; margin-bottom: 2px; }

        .modal-footer {
            padding: 20px 28px;
            border-top: 1px solid #f1f5f9;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            background: #fafcff;
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
        }
        .btn-cancel:hover { border-color: #94a3b8; color: #374151; }

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
            box-shadow: 0 3px 10px rgba(0,102,255,0.3);
        }
        .btn-submit:hover { background: linear-gradient(135deg, #0052d4, #0041af); box-shadow: 0 5px 16px rgba(0,102,255,0.4); }
        .btn-submit:active { transform: scale(0.98); }

        /* ── RESPONSIVE ───────────────────────────────────────── */
        @media (max-width: 768px) {
            .stats-grid { grid-template-columns: 1fr; }
            .filter-input { width: 180px; }
            .form-row { grid-template-columns: 1fr; }
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .anim { animation: fadeInUp 0.5s ease both; }
        .anim-1 { animation-delay: 0.05s; }
        .anim-2 { animation-delay: 0.1s; }
        .anim-3 { animation-delay: 0.15s; }
        .anim-4 { animation-delay: 0.2s; }
    </style>
</head>
<body>

<!-- ══════════════════════════════════════════ SIDEBAR -->
<aside class="sidebar">
    <div class="sidebar-logo">
        <svg width="20" height="20" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.315 48.315 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z" />
        </svg>
    </div>

    <nav class="sidebar-nav">
        <!-- Dashboard -->
        <a href="#" class="sidebar-item active" title="Tableau de bord">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
        </a>

        <!-- Patients -->
        <a href="#" class="sidebar-item" title="Patients">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
        </a>

        <!-- Examens -->
        <a href="#" class="sidebar-item" title="Examens">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23.693L5 14.5m14.8.8l1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0112 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.61L5 14.5" />
            </svg>
        </a>

        <!-- Médecins -->
        <a href="#" class="sidebar-item" title="Médecins">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 2v6m0 0a4 4 0 00-4 4v2a4 4 0 008 0v-2a4 4 0 00-4-4zm0 0V4" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 10h.01M18 10h.01" />
            </svg>
        </a>

        <!-- Activité -->
        <a href="#" class="sidebar-item" title="Activité">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0020.25 18V6A2.25 2.25 0 0018 3.75H6A2.25 2.25 0 003.75 6v12A2.25 2.25 0 006 20.25z" />
            </svg>
        </a>

        <!-- Historique -->
        <a href="#" class="sidebar-item" title="Historique">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </a>
    </nav>

    <!-- Settings at bottom -->
    <a href="#" class="sidebar-item" title="Paramètres" style="margin-top: auto;">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
        </svg>
    </a>
</aside>

<!-- ══════════════════════════════════════════ MAIN -->
<div class="layout">

    <!-- TOP NAV -->
    <header class="topnav">
        <div class="brand">Medix <span>eSanté</span></div>
        <div class="topnav-right">
            <div class="nav-user-info">
                <div class="nav-user-name">{{ $user->first_name }} {{ $user->last_name }}</div>
                <div class="nav-user-role">Admin</div>
            </div>
            <div class="nav-avatar">{{ strtoupper(substr($user->first_name, 0, 1)) }}{{ strtoupper(substr($user->last_name, 0, 1)) }}</div>
            <form action="{{ route('admin.logout') }}" method="POST" class="m-0 p-0">
                @csrf
                <button type="submit" class="btn-logout" title="Se déconnecter">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                    </svg>
                </button>
            </form>
        </div>
    </header>

    <!-- PAGE CONTENT -->
    <div class="page-content">

        <!-- Alert -->
        @if(session('success'))
            <div class="alert-success" id="success-alert">
                <div style="display:flex;align-items:center;gap:10px;">
                    <svg class="alert-success-icon" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                    {{ session('success') }}
                </div>
                <button onclick="document.getElementById('success-alert').remove()" style="background:none;border:none;cursor:pointer;color:#94a3b8;padding:0;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif

        <!-- Page Header -->
        <div class="page-header anim">
            <h1 class="page-title">Espace <span style="color:#0066ff;">Administrateur</span></h1>
            <p class="page-subtitle">Gérez la plateforme et supervisez les examens médicaux disponibles.</p>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card anim anim-1">
                <div class="stat-icon blue">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23.693L5 14.5m14.8.8l1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0112 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.61L5 14.5" />
                    </svg>
                </div>
                <div class="stat-info">
                    <div class="stat-label">En Attente (Actifs)</div>
                    <div class="stat-value">{{ $stats['total_exams'] }}</div>
                </div>
            </div>

            <div class="stat-card anim anim-2">
                <div class="stat-icon green">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Patients Inscrits</div>
                    <div class="stat-value">{{ $stats['total_patients'] }}</div>
                </div>
            </div>

            <div class="stat-card anim anim-3">
                <div class="stat-icon orange">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                    </svg>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Total Examens</div>
                    <div class="stat-value">{{ $stats['total_exams'] + $stats['archived_exams'] }}</div>
                </div>
            </div>
        </div>

        <!-- Exam Management Table -->
        <div class="data-section anim anim-4">

            <!-- Table Header -->
            <div class="data-header">
                <div>
                    <div class="data-title">Catalogue des Examens</div>
                </div>
            </div>

            <!-- Filters + Add Button -->
            <form method="GET" action="{{ route('admin.dashboard') }}" id="filter-form">
                <div class="filters-bar">
                    <!-- Category -->
                    <div>
                        <span class="filter-label">Catégorie</span>
                        <div class="filter-group" style="position:relative;display:inline-block;">
                            <select name="category" class="filter-select" onchange="document.getElementById('filter-form').submit()">
                                <option value="">Toutes les catégories</option>
                                <option value="biochemistry" {{ $selectedCategory === 'biochemistry' ? 'selected' : '' }}>Biochimie</option>
                                <option value="hematology"   {{ $selectedCategory === 'hematology'   ? 'selected' : '' }}>Hématologie</option>
                                <option value="microbiology" {{ $selectedCategory === 'microbiology' ? 'selected' : '' }}>Microbiologie</option>
                                <option value="immunology"   {{ $selectedCategory === 'immunology'   ? 'selected' : '' }}>Immunologie</option>
                                <option value="urinalysis"   {{ $selectedCategory === 'urinalysis'   ? 'selected' : '' }}>Urinalyse</option>
                                <option value="other"        {{ $selectedCategory === 'other'        ? 'selected' : '' }}>Autre</option>
                            </select>
                            <svg class="select-arrow" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                        </div>
                    </div>

                    <!-- Search -->
                    <div>
                        <span class="filter-label">Recherche rapide</span>
                        <div class="filter-group" style="position:relative;display:inline-block;">
                            <svg class="search-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);width:16px;height:16px;color:#94a3b8;pointer-events:none;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                            </svg>
                            <input type="text" name="search" value="{{ $search }}" placeholder="Rechercher par nom, code..." class="filter-input" style="padding-left:36px;">
                        </div>
                    </div>

                    <!-- Archived toggle -->
                    <div style="align-self:flex-end;">
                        <label class="filter-checkbox-wrap">
                            <input type="checkbox" name="show_archived" value="1" {{ $showArchived ? 'checked' : '' }} onchange="document.getElementById('filter-form').submit()">
                            Afficher archivés
                        </label>
                    </div>

                    <!-- Filter Button -->
                    <div style="align-self:flex-end;">
                        <button type="submit" class="btn-filter">Options de filtrage</button>
                    </div>

                    <!-- Spacer -->
                    <div style="flex:1;"></div>

                    <!-- ADD EXAM BUTTON — center of filters row -->
                    <div style="align-self:flex-end;">
                        <button type="button" onclick="openModal('create')" class="btn-add-exam">
                            <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                            </svg>
                            Ajouter un Examen
                        </button>
                    </div>
                </div>
            </form>

            <!-- Table -->
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Examen</th>
                        <th>Catégorie</th>
                        <th>Plage Normale</th>
                        <th>Date Ajout</th>
                        <th>Statut</th>
                        <th style="text-align:right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($exams as $exam)
                        @php
                            $catLabel = [
                                'biochemistry' => 'Biochimie',
                                'hematology'   => 'Hématologie',
                                'microbiology' => 'Microbiologie',
                                'immunology'   => 'Immunologie',
                                'urinalysis'   => 'Urinalyse',
                                'other'        => 'Autre',
                            ][$exam->category] ?? $exam->category;
                        @endphp
                        <tr class="{{ $exam->is_archive ? 'archived' : '' }}">
                            <td>
                                <span class="exam-code">{{ $exam->code }}</span>
                            </td>
                            <td>
                                <div class="exam-name">{{ $exam->name }}</div>
                                @if($exam->description)
                                    <div class="exam-desc">{{ $exam->description }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="category-badge cat-{{ $exam->category }}">{{ $catLabel }}</span>
                            </td>
                            <td style="color:#475569;font-size:13px;">{{ $exam->default_normal_range ?? '—' }}</td>
                            <td style="color:#94a3b8;font-size:12px;white-space:nowrap;">{{ $exam->created_at ? $exam->created_at->format('d/m/Y') : '—' }}</td>
                            <td>
                                @if($exam->is_archive)
                                    <span class="status-badge status-archived"><span class="dot"></span>Archivé</span>
                                @else
                                    <span class="status-badge status-active"><span class="dot"></span>Actif</span>
                                @endif
                            </td>
                            <td style="text-align:right;">
                                <div style="display:flex;align-items:center;justify-content:flex-end;gap:6px;">
                                    <button
                                        class="table-action-btn"
                                        onclick="openModal('edit', {
                                            id: {{ $exam->id }},
                                            code: '{{ addslashes($exam->code) }}',
                                            name: '{{ addslashes($exam->name) }}',
                                            category: '{{ $exam->category }}',
                                            description: `{{ addslashes($exam->description ?? '') }}`,
                                            default_normal_range: '{{ addslashes($exam->default_normal_range ?? '') }}',
                                            preparation_instructions: `{{ addslashes($exam->preparation_instructions ?? '') }}`
                                        })"
                                        title="Modifier">
                                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                                    </button>

                                    <form action="{{ route('admin.exams.archive', $exam) }}" method="POST" style="display:inline;margin:0;"
                                          onsubmit="return confirm('{{ $exam->is_archive ? 'Restaurer cet examen ?' : 'Archiver cet examen ?' }}')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="table-action-btn {{ $exam->is_archive ? 'restore-btn' : 'archive-btn' }}" title="{{ $exam->is_archive ? 'Restaurer' : 'Archiver' }}">
                                            @if($exam->is_archive)
                                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"/></svg>
                                            @else
                                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0l-3-3m3 3l3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/></svg>
                                            @endif
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                                    </div>
                                    <h3>Aucun examen trouvé</h3>
                                    <p>Utilisez le bouton "Ajouter un Examen" pour commencer.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination -->
            @if($exams->hasPages())
                <div class="pagination-wrap">
                    <div style="display:flex;gap:4px;align-items:center;">
                        @if($exams->onFirstPage())
                            <span style="padding:6px 12px;background:#f1f5f9;color:#94a3b8;border-radius:6px;font-size:13px;cursor:not-allowed;">« Précédent</span>
                        @else
                            <a href="{{ $exams->previousPageUrl() }}" style="padding:6px 12px;background:white;border:1px solid #e2e8f0;color:#374151;border-radius:6px;font-size:13px;font-weight:500;text-decoration:none;">« Précédent</a>
                        @endif

                        @foreach($exams->getUrlRange(max(1, $exams->currentPage()-2), min($exams->lastPage(), $exams->currentPage()+2)) as $page => $url)
                            @if($page == $exams->currentPage())
                                <span style="padding:6px 12px;background:#0066ff;color:white;border-radius:6px;font-size:13px;font-weight:700;">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" style="padding:6px 12px;background:white;border:1px solid #e2e8f0;color:#374151;border-radius:6px;font-size:13px;font-weight:500;text-decoration:none;">{{ $page }}</a>
                            @endif
                        @endforeach

                        @if($exams->hasMorePages())
                            <a href="{{ $exams->nextPageUrl() }}" style="padding:6px 12px;background:white;border:1px solid #e2e8f0;color:#374151;border-radius:6px;font-size:13px;font-weight:500;text-decoration:none;">Suivant »</a>
                        @else
                            <span style="padding:6px 12px;background:#f1f5f9;color:#94a3b8;border-radius:6px;font-size:13px;cursor:not-allowed;">Suivant »</span>
                        @endif
                    </div>
                </div>
            @endif
        </div>

    </div><!-- /page-content -->
</div><!-- /layout -->

<!-- ══════════════════════════════════════════ MODAL -->
<div id="exam-modal" style="display:none;position:fixed;inset:0;z-index:1000;">
    <div class="modal-backdrop" onclick="closeModal()">
        <div class="modal-box" id="exam-modal-card" onclick="event.stopPropagation()">

            <!-- Modal Header -->
            <div class="modal-header">
                <div class="modal-title-wrap">
                    <div class="modal-title" id="modal-title">Nouvel Examen</div>
                    <div class="modal-subtitle">Complétez les informations de l'examen</div>
                </div>
                <button class="modal-close" onclick="closeModal()">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">

                @if($errors->any())
                    <div class="form-errors">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form id="exam-form" method="POST" action="{{ route('admin.exams.store') }}">
                    @csrf
                    <input type="hidden" id="form-method" name="_method" value="POST">

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Code Examen<span class="required-star">*</span></label>
                            <input type="text" name="code" required placeholder="Ex: HBA1C" class="form-control" style="font-family:'SF Mono','Consolas',monospace;font-weight:600;letter-spacing:0.5px;">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Nom de l'Examen<span class="required-star">*</span></label>
                            <input type="text" name="name" required placeholder="Ex: Hémoglobine Glyquée" class="form-control">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Catégorie<span class="required-star">*</span></label>
                            <div style="position:relative;">
                                <select name="category" required class="form-control">
                                    <option value="">Sélectionner...</option>
                                    <option value="biochemistry">Biochimie</option>
                                    <option value="hematology">Hématologie</option>
                                    <option value="microbiology">Microbiologie</option>
                                    <option value="immunology">Immunologie</option>
                                    <option value="urinalysis">Urinalyse</option>
                                    <option value="other">Autre</option>
                                </select>
                                <svg style="position:absolute;right:10px;top:50%;transform:translateY(-50%);width:14px;height:14px;color:#94a3b8;pointer-events:none;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Plage Normale</label>
                            <input type="text" name="default_normal_range" placeholder="Ex: 4.0 - 5.6 %" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea name="description" rows="2" placeholder="Description de l'examen..." class="form-control"></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Instructions de Préparation</label>
                        <textarea name="preparation_instructions" rows="2" placeholder="Ex: Être à jeun depuis 12h..." class="form-control"></textarea>
                    </div>
                </form>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" onclick="closeModal()" class="btn-cancel">Annuler</button>
                <button type="submit" form="exam-form" class="btn-submit">
                    <span id="modal-submit-text">Créer l'examen</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function openModal(mode, examData = null) {
        const modal   = document.getElementById('exam-modal');
        const card    = document.getElementById('exam-modal-card');
        const form    = document.getElementById('exam-form');
        const title   = document.getElementById('modal-title');
        const submit  = document.getElementById('modal-submit-text');
        const method  = document.getElementById('form-method');

        form.reset();

        if (mode === 'create') {
            title.textContent  = 'Nouvel Examen';
            submit.textContent = "Créer l'examen";
            form.action        = '{{ route("admin.exams.store") }}';
            method.value       = 'POST';
        } else if (mode === 'edit' && examData) {
            title.textContent  = "Modifier l'Examen";
            submit.textContent = 'Enregistrer les modifications';
            form.action        = '/admin/exams/' + examData.id;
            method.value       = 'PUT';

            ['code','name','category','description','default_normal_range','preparation_instructions'].forEach(f => {
                const el = form.querySelector(`[name="${f}"]`);
                if (el) el.value = examData[f] || '';
            });
        }

        modal.style.display = 'block';
        requestAnimationFrame(() => {
            card.style.transform = 'scale(1)';
            card.style.opacity   = '1';
        });
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        const modal = document.getElementById('exam-modal');
        const card  = document.getElementById('exam-modal-card');

        card.style.transform = 'scale(0.95)';
        card.style.opacity   = '0';

        setTimeout(() => {
            modal.style.display  = 'none';
            document.body.style.overflow = '';
        }, 250);
    }

    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

    @if($errors->any())
        document.addEventListener('DOMContentLoaded', () => {
            openModal('{{ old("_method") === "PUT" ? "edit" : "create" }}');
        });
    @endif
</script>

</body>
</html>
