<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="shortcut icon" href="{{ asset('images/logo.png') }}">
    <title>@hasSection('title')@yield('title') - @endif{{ config('app.name') }}</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts: DM Sans + DM Mono -->
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">

    <style>
        :root {
            --green-50:  #f0fdf4;
            --green-100: #dcfce7;
            --green-400: #4ade80;
            --green-500: #22c55e;
            --green-600: #16a34a;
            --green-700: #15803d;
            --green-900: #14532d;

            --sidebar-bg:     #0f172a;
            --sidebar-border: rgba(255,255,255,0.06);
            --sidebar-hover:  rgba(255,255,255,0.05);
            --sidebar-active-bg: rgba(34,197,94,0.12);
            --sidebar-active-border: #22c55e;

            --surface:        #ffffff;
            --surface-2:      #f8fafc;
            --surface-3:      #f1f5f9;
            --border:         #e2e8f0;
            --border-subtle:  #f1f5f9;

            --text-primary:   #0f172a;
            --text-secondary: #64748b;
            --text-tertiary:  #94a3b8;

            --accent:         #22c55e;
            --accent-dark:    #16a34a;
            --accent-light:   rgba(34,197,94,0.1);

            --sidebar-width:     256px;
            --sidebar-collapsed: 68px;
            --topbar-height:     64px;

            --shadow-sm:  0 1px 2px rgba(0,0,0,0.05);
            --shadow-md:  0 4px 6px -1px rgba(0,0,0,0.07), 0 2px 4px -2px rgba(0,0,0,0.05);
            --shadow-lg:  0 10px 15px -3px rgba(0,0,0,0.08), 0 4px 6px -4px rgba(0,0,0,0.05);
            --shadow-xl:  0 20px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.05);

            --radius-sm:  6px;
            --radius-md:  10px;
            --radius-lg:  14px;
            --radius-xl:  20px;
            --radius-full: 9999px;

            --transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--surface-2);
            color: var(--text-primary);
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
            font-size: 18px;
        }

        /* ─── SIDEBAR ──────────────────────────────────────── */
        #sidebar {
            position: fixed;
            top: 0; left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--sidebar-bg);
            display: flex;
            flex-direction: column;
            z-index: 1000;
            transition: width 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
        }

        #sidebar.collapsed { width: var(--sidebar-collapsed); }

        /* Logo area */
        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 20px 18px;
            border-bottom: 1px solid var(--sidebar-border);
            min-height: var(--topbar-height);
            text-decoration: none;
            overflow: hidden;
        }

        .sidebar-logo-img {
            width: 32px;
            height: 32px;
            object-fit: contain;
            flex-shrink: 0;
            filter: brightness(1.1);
        }

        .sidebar-logo-text {
            font-size: 1.05rem;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: -0.02em;
            white-space: nowrap;
            opacity: 1;
            transition: opacity 0.2s;
        }

        #sidebar.collapsed .sidebar-logo-text { opacity: 0; pointer-events: none; }

        /* Toggle button */
        .sidebar-toggle {
            position: absolute;
            top: 50%;
            right: -13px;
            transform: translateY(-50%);
            width: 26px;
            height: 26px;
            background: var(--sidebar-bg);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 50%;
            color: var(--text-tertiary);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.65rem;
            transition: var(--transition);
            z-index: 10;
        }

        .sidebar-toggle:hover {
            background: var(--accent);
            border-color: var(--accent);
            color: white;
        }

        .sidebar-logo-wrap {
            position: relative;
            display: flex;
            align-items: center;
            border-bottom: 1px solid var(--sidebar-border);
        }

        /* Nav */
        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 12px 10px;
            scrollbar-width: none;
        }
        .sidebar-nav::-webkit-scrollbar { display: none; }

        .nav-section-label {
            font-size: 0.65rem;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: rgba(255,255,255,0.25);
            padding: 12px 10px 6px;
            white-space: nowrap;
            overflow: hidden;
            transition: opacity 0.2s;
        }
        #sidebar.collapsed .nav-section-label { opacity: 0; }

        .nav-item { list-style: none; margin-bottom: 2px; }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 9px 10px;
            border-radius: var(--radius-md);
            color: rgba(255,255,255,0.55);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            white-space: nowrap;
            overflow: hidden;
            transition: var(--transition);
            border-left: 2px solid transparent;
            position: relative;
        }

        .nav-link:hover {
            background: var(--sidebar-hover);
            color: rgba(255,255,255,0.85);
            border-left-color: rgba(34,197,94,0.4);
        }

        .nav-link.active {
            background: var(--sidebar-active-bg);
            color: var(--accent);
            border-left-color: var(--accent);
            font-weight: 600;
        }

        .nav-icon {
            width: 18px;
            text-align: center;
            font-size: 0.9rem;
            flex-shrink: 0;
        }

        .nav-text {
            opacity: 1;
            transition: opacity 0.15s;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        #sidebar.collapsed .nav-text { opacity: 0; }

        /* Tooltip for collapsed state */
        #sidebar.collapsed .nav-link { justify-content: center; }
        #sidebar.collapsed .nav-link[data-tooltip]:hover::after {
            content: attr(data-tooltip);
            position: absolute;
            left: calc(var(--sidebar-collapsed) - 4px);
            background: #1e293b;
            color: white;
            font-size: 0.8rem;
            padding: 5px 10px;
            border-radius: var(--radius-sm);
            white-space: nowrap;
            z-index: 100;
            box-shadow: var(--shadow-lg);
            border: 1px solid rgba(255,255,255,0.08);
        }

        /* Sidebar footer */
        .sidebar-footer {
            padding: 14px 10px;
            border-top: 1px solid var(--sidebar-border);
        }

        .sidebar-footer-text {
            font-size: 0.7rem;
            color: rgba(255,255,255,0.2);
            text-align: center;
            white-space: nowrap;
            overflow: hidden;
            transition: opacity 0.2s;
        }
        #sidebar.collapsed .sidebar-footer-text { opacity: 0; }

        /* ─── MAIN CONTENT ─────────────────────────────────── */
        #content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: margin-left 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        #content.expanded { margin-left: var(--sidebar-collapsed); }

        /* ─── TOPBAR ───────────────────────────────────────── */
        .topbar {
            height: var(--topbar-height);
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: var(--shadow-sm);
        }

        .topbar-left { display: flex; align-items: center; gap: 16px; }

        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 6px;
            list-style: none;
            padding: 0;
            margin: 0;
            font-size: 0.85rem;
        }
        .breadcrumb-item { display: flex; align-items: center; gap: 6px; color: var(--text-secondary); }
        .breadcrumb-item a { color: var(--text-secondary); text-decoration: none; transition: color 0.15s; }
        .breadcrumb-item a:hover { color: var(--accent); }
        .breadcrumb-item.active { color: var(--text-primary); font-weight: 500; }
        .breadcrumb-item + .breadcrumb-item::before {
            content: '/';
            color: var(--text-tertiary);
            font-size: 0.75rem;
        }

        .topbar-right { display: flex; align-items: center; gap: 8px; }

        /* User menu */
        .user-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 6px 12px 6px 6px;
            border: 1px solid var(--border);
            border-radius: var(--radius-full);
            background: var(--surface);
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
        }
        .user-btn:hover { background: var(--surface-3); border-color: var(--border); }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--green-600), var(--green-400));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.02em;
            flex-shrink: 0;
        }

        .user-name {
            font-size: 0.825rem;
            font-weight: 600;
            color: var(--text-primary);
            white-space: nowrap;
        }

        .user-role-badge {
            font-size: 0.7rem;
            font-weight: 500;
            color: var(--accent);
            background: var(--accent-light);
            padding: 2px 8px;
            border-radius: var(--radius-full);
        }

        /* Dropdown */
        .user-dropdown-wrap { position: relative; }

        .user-dropdown {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            width: 300px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-xl);
            overflow: hidden;
            display: none;
            z-index: 200;
            animation: dropIn 0.18s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .user-dropdown.show { display: block; }

        @keyframes dropIn {
            from { opacity: 0; transform: translateY(-6px) scale(0.98); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        .dropdown-profile {
            padding: 20px;
            background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 100%);
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .dropdown-avatar-lg {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--green-600), var(--green-400));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            font-weight: 700;
            flex-shrink: 0;
            border: 2px solid rgba(34,197,94,0.4);
        }

        .dropdown-profile-name {
            font-weight: 600;
            color: white;
            font-size: 0.9rem;
            line-height: 1.3;
        }
        .dropdown-profile-email {
            font-size: 0.78rem;
            color: rgba(255,255,255,0.5);
            margin-top: 2px;
        }
        .dropdown-profile-role {
            display: inline-block;
            font-size: 0.68rem;
            font-weight: 600;
            color: var(--accent);
            background: rgba(34,197,94,0.15);
            padding: 2px 8px;
            border-radius: var(--radius-full);
            margin-top: 5px;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .dropdown-section {
            padding: 8px;
            border-top: 1px solid var(--border-subtle);
        }

        .dropdown-info-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 10px;
            border-radius: var(--radius-sm);
            font-size: 0.82rem;
        }
        .dropdown-info-item i {
            width: 16px;
            color: var(--text-tertiary);
            font-size: 0.8rem;
            flex-shrink: 0;
        }
        .dropdown-info-label { color: var(--text-tertiary); font-size: 0.72rem; }
        .dropdown-info-value { color: var(--text-primary); font-weight: 500; font-size: 0.82rem; }

        .dropdown-actions {
            padding: 8px;
            border-top: 1px solid var(--border-subtle);
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .dropdown-action-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border-radius: var(--radius-sm);
            border: none;
            background: none;
            font-size: 0.83rem;
            font-weight: 500;
            cursor: pointer;
            width: 100%;
            transition: var(--transition);
            color: var(--text-secondary);
            font-family: 'DM Sans', sans-serif;
        }
        .dropdown-action-btn:hover {
            background: var(--surface-3);
            color: var(--text-primary);
        }
        .dropdown-action-btn.danger { color: #ef4444; }
        .dropdown-action-btn.danger:hover { background: #fef2f2; color: #dc2626; }
        .dropdown-action-btn i { width: 16px; font-size: 0.82rem; }

        /* ─── MAIN AREA ────────────────────────────────────── */
        main.main-content { flex: 1; padding: 24px; }

        /* ─── ALERTS ───────────────────────────────────────── */
        .alert {
            border: none;
            border-radius: var(--radius-md);
            padding: 12px 16px;
            font-size: 0.875rem;
            font-weight: 500;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 16px;
            box-shadow: var(--shadow-sm);
        }
        .alert-success { background: #f0fdf4; color: #166534; border-left: 3px solid var(--accent); }
        .alert-danger  { background: #fef2f2; color: #991b1b; border-left: 3px solid #ef4444; }
        .alert-warning { background: #fffbeb; color: #92400e; border-left: 3px solid #f59e0b; }
        .alert-info    { background: #eff6ff; color: #1e40af; border-left: 3px solid #3b82f6; }
        .alert .btn-close { margin-left: auto; }

        /* ─── CARDS ────────────────────────────────────────── */
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            margin-bottom: 20px;
            transition: box-shadow 0.2s;
        }
        .card:hover { box-shadow: var(--shadow-md); }

        .card-header {
            background: transparent;
            border-bottom: 1px solid var(--border-subtle);
            padding: 16px 20px;
            font-weight: 600;
            font-size: 0.9rem;
            border-radius: var(--radius-lg) var(--radius-lg) 0 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-body { padding: 20px; }
        .card-footer {
            background: var(--surface-2);
            border-top: 1px solid var(--border-subtle);
            padding: 14px 20px;
            border-radius: 0 0 var(--radius-lg) var(--radius-lg);
        }

        /* ─── BUTTONS ──────────────────────────────────────── */
        .btn {
            font-family: 'DM Sans', sans-serif;
            font-weight: 500;
            font-size: 0.875rem;
            border-radius: var(--radius-md);
            padding: 8px 16px;
            transition: var(--transition);
            border: 1px solid transparent;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-primary {
            background: var(--accent);
            border-color: var(--accent);
            color: white;
        }
        .btn-primary:hover {
            background: var(--accent-dark);
            border-color: var(--accent-dark);
            color: white;
            box-shadow: 0 4px 12px rgba(34,197,94,0.3);
        }

        .btn-outline-secondary {
            background: transparent;
            border-color: var(--border);
            color: var(--text-secondary);
        }
        .btn-outline-secondary:hover {
            background: var(--surface-3);
            border-color: var(--border);
            color: var(--text-primary);
        }

        .btn-sm {
            font-size: 0.8rem;
            padding: 5px 11px;
            border-radius: var(--radius-sm);
        }

        .btn-danger { background: #ef4444; border-color: #ef4444; color: white; }
        .btn-danger:hover { background: #dc2626; border-color: #dc2626; color: white; }
        .btn-info    { background: #3b82f6; border-color: #3b82f6; color: white; }
        .btn-info:hover { background: #2563eb; border-color: #2563eb; color: white; }
        .btn-warning { background: #f59e0b; border-color: #f59e0b; color: white; }
        .btn-warning:hover { background: #d97706; border-color: #d97706; color: white; }
        .btn-success { background: var(--accent); border-color: var(--accent); color: white; }
        .btn-success:hover { background: var(--accent-dark); border-color: var(--accent-dark); color: white; }

        /* ─── TABLES ───────────────────────────────────────── */
        .table { font-size: 1rem; border-collapse: separate; border-spacing: 0; }
        .table th {
            font-weight: 600;
            font-size: 1rem;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: var(--text-tertiary);
            background: var(--surface-2);
            padding: 10px 14px;
            border-bottom: 1px solid var(--border);
        }
        .table td {
            padding: 12px 14px;
            border-bottom: 1px solid var(--border-subtle);
            vertical-align: middle;
            color: var(--text-primary);
            
        }
        .table tbody tr:hover { background: var(--surface-2); }
        .table tbody tr:last-child td { border-bottom: none; }

        /* ─── BADGES ───────────────────────────────────────── */
        .badge {
            font-size: 0.72rem;
            font-weight: 600;
            padding: 3px 9px;
            border-radius: var(--radius-full);
            letter-spacing: 0.02em;
        }
        .bg-success { background: var(--accent) !important; }
        .bg-danger  { background: #ef4444 !important; }
        .bg-warning { background: #f59e0b !important; color: white !important; }
        .bg-info    { background: #3b82f6 !important; }
        .bg-secondary { background: #64748b !important; }
        .bg-primary { background: var(--green-700) !important; }

        /* ─── FORM CONTROLS ────────────────────────────────── */
        .form-control, .form-select {
            font-family: 'DM Sans', sans-serif;
            font-size: 0.875rem;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 8px 12px;
            color: var(--text-primary);
            background: var(--surface);
            transition: var(--transition);
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(34,197,94,0.1);
            outline: none;
        }

        .form-label {
            font-size: 0.825rem;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 6px;
        }

        /* ─── PAGINATION ───────────────────────────────────── */
        .pagination { gap: 4px; }
        .page-item .page-link {
            border: 1px solid var(--border);
            border-radius: var(--radius-sm) !important;
            color: var(--text-secondary);
            padding: 6px 11px;
            font-size: 0.825rem;
            font-weight: 500;
            transition: var(--transition);
        }
        .page-item.active .page-link { background: var(--accent); border-color: var(--accent); color: white; }
        .page-item .page-link:hover:not(.active) { background: var(--surface-3); color: var(--text-primary); }
        .page-item.disabled .page-link { opacity: 0.4; }

        /* ─── SCROLLBAR ────────────────────────────────────── */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--border); border-radius: 9999px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--text-tertiary); }

        /* ─── MOBILE ───────────────────────────────────────── */
        @media (max-width: 768px) {
            #sidebar { transform: translateX(-100%); width: var(--sidebar-width) !important; }
            #sidebar.mobile-open { transform: translateX(0); }
            #content { margin-left: 0 !important; }
            main.main-content { padding: 16px; }
        }

        /* ─── MODAL ────────────────────────────────────────── */
        .modal-content {
            border: none;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-xl);
        }
        .modal-header {
            border-bottom: 1px solid var(--border-subtle);
            padding: 18px 22px;
        }
        .modal-title { font-weight: 700; font-size: 1rem; }
        .modal-body { padding: 22px; }
        .modal-footer {
            border-top: 1px solid var(--border-subtle);
            padding: 14px 22px;
        }

        /* ─── INPUT GROUPS ─────────────────────────────────── */
        .input-group-text {
            background: var(--surface-2);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            color: var(--text-secondary);
            font-size: 0.875rem;
        }

        /* ─── LIST GROUP ───────────────────────────────────── */
        .list-group-item {
            border: none;
            border-bottom: 1px solid var(--border-subtle);
            padding: 12px 16px;
            font-size: 0.875rem;
        }
        .list-group-item-action:hover { background: var(--surface-2); }
        .list-group-flush .list-group-item:last-child { border-bottom: none; }

        .table-head-dark th {
        background: #1e293b;
        color: rgba(255,255,255,0.85);
        font-size: 0.72rem;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        padding: 11px 14px;
        border-bottom: none;
        font-weight: 600;
    }
    .table-head-dark th:first-child { border-radius: 8px 0 0 0; }
    .table-head-dark th:last-child  { border-radius: 0 8px 0 0; }
    </style>

    @stack('styles')
</head>

@if(session('api_token'))
<meta name="api-token" content="{{ session('api_token') }}">
@endif

<body>

<!-- ─── SIDEBAR ─────────────────────────────────────────────── -->
<aside id="sidebar">

    <div class="sidebar-logo-wrap">
        <a href="{{ route('dashboard') }}" class="sidebar-logo">
            <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" class="sidebar-logo-img">
            <span class="sidebar-logo-text">{{ config('app.name') }}</span>
        </a>
        <button class="sidebar-toggle" id="sidebarToggle" title="Toggle sidebar">
            <i class="fas fa-chevron-left" id="toggleIcon"></i>
        </button>
    </div>

    <nav class="sidebar-nav">
        <ul style="list-style:none; padding:0; margin:0;">

            <li class="nav-item">
                <a href="{{ route('dashboard') }}"
                   class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                   data-tooltip="Dashboard">
                    <i class="fas fa-chart-pie nav-icon"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>

            @if($userRole == 'administrador')
            <li class="nav-section-label">Gestión</li>
            <li class="nav-item">
                <a href="{{ route('usuarios.index') }}"
                   class="nav-link {{ request()->routeIs('usuarios.*') ? 'active' : '' }}"
                   data-tooltip="Usuarios">
                    <i class="fas fa-users nav-icon"></i>
                    <span class="nav-text">Usuarios</span>
                </a>
            </li>
            @endif

            @if(in_array($userRole, ['administrador', 'vendedor']))
            @if($userRole !== 'administrador')
            <li class="nav-section-label">Gestión</li>
            @endif
            <li class="nav-item">
                <a href="{{ route('clientes.index') }}"
                   class="nav-link {{ request()->routeIs('clientes.*') ? 'active' : '' }}"
                   data-tooltip="Clientes">
                    <i class="fas fa-user-tie nav-icon"></i>
                    <span class="nav-text">Clientes</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('proveedores.index') }}"
                   class="nav-link {{ request()->routeIs('proveedores.*') ? 'active' : '' }}"
                   data-tooltip="Proveedores">
                    <i class="fas fa-truck nav-icon"></i>
                    <span class="nav-text">Proveedores</span>
                </a>
            </li>

            <li class="nav-section-label">Inventario</li>
            <li class="nav-item">
                <a href="{{ route('categorias.index') }}"
                   class="nav-link {{ request()->routeIs('categorias.*') ? 'active' : '' }}"
                   data-tooltip="Categorías">
                    <i class="fas fa-tags nav-icon"></i>
                    <span class="nav-text">Categorías</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('productos.index') }}"
                   class="nav-link {{ request()->routeIs('productos.*') ? 'active' : '' }}"
                   data-tooltip="Productos">
                    <i class="fas fa-box nav-icon"></i>
                    <span class="nav-text">Productos</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('servicios.index') }}"
                   class="nav-link {{ request()->routeIs('servicios.*') ? 'active' : '' }}"
                   data-tooltip="Servicios">
                    <i class="fas fa-concierge-bell nav-icon"></i>
                    <span class="nav-text">Servicios</span>
                </a>
            </li>

            <li class="nav-section-label">Operaciones</li>
            <li class="nav-item">
                <a href="{{ route('ventas.index') }}"
                   class="nav-link {{ request()->routeIs('ventas.*') ? 'active' : '' }}"
                   data-tooltip="Ventas">
                    <i class="fas fa-shopping-cart nav-icon"></i>
                    <span class="nav-text">Ventas</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('creditos.index') }}"
                   class="nav-link {{ request()->routeIs('creditos.*') ? 'active' : '' }}"
                   data-tooltip="Créditos">
                    <i class="fas fa-credit-card nav-icon"></i>
                    <span class="nav-text">Créditos</span>
                </a>
            </li>
            @endif

            @if(in_array($userRole, ['administrador', 'analista']))
            <li class="nav-section-label">Análisis</li>
            <li class="nav-item">
                <a href="{{ route('reportes.index') }}"
                   class="nav-link {{ request()->routeIs('reportes.*') ? 'active' : '' }}"
                   data-tooltip="Reportes">
                    <i class="fas fa-chart-bar nav-icon"></i>
                    <span class="nav-text">Reportes</span>
                </a>
            </li>
            @endif

            @if($userRole == 'administrador')
            <li class="nav-item">
                <a href="{{ route('auditoria.index') }}"
                   class="nav-link {{ request()->routeIs('auditoria.*') ? 'active' : '' }}"
                   data-tooltip="Auditoría">
                    <i class="fas fa-clipboard-list nav-icon"></i>
                    <span class="nav-text">Auditoría</span>
                </a>
            </li>
            @endif
        </ul>
    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-footer-text">{{ config('app.name') }} © {{ date('Y') }}</div>
    </div>
</aside>

<!-- ─── MAIN CONTENT ─────────────────────────────────────────── -->
<div id="content">

    <!-- TOPBAR -->
    <nav class="topbar">
        <div class="topbar-left">
            <!-- Mobile toggle -->
            <button class="btn btn-sm btn-outline-secondary d-md-none" id="mobileToggle">
                <i class="fas fa-bars"></i>
            </button>

            <!-- Breadcrumb -->
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}">
                        <i class="fas fa-house" style="font-size:0.8rem;"></i>
                    </a>
                </li>
                @yield('breadcrumb')
            </ol>
        </div>

        <div class="topbar-right">
            <!-- User menu -->
            <div class="user-dropdown-wrap">
                <button class="user-btn" id="userBtn">
                    <div class="user-avatar">
                        {{ substr($user['nombres'] ?? 'U', 0, 1) }}{{ substr($user['apellidos'] ?? 'S', 0, 1) }}
                    </div>
                    <span class="user-name d-none d-md-block">{{ $user['nombres'] ?? 'Usuario' }}</span>
                    <span class="user-role-badge d-none d-md-block">{{ ucfirst($userRole ?? 'user') }}</span>
                    <i class="fas fa-chevron-down" style="font-size:0.65rem; color:var(--text-tertiary); margin-left:2px;"></i>
                </button>

                <div class="user-dropdown" id="userDropdown">
                    <!-- Profile header -->
                    <div class="dropdown-profile">
                        <div class="dropdown-avatar-lg">
                            {{ substr($user['nombres'] ?? 'U', 0, 1) }}{{ substr($user['apellidos'] ?? 'S', 0, 1) }}
                        </div>
                        <div>
                            <div class="dropdown-profile-name">{{ $user['nombre_completo'] ?? 'Usuario' }}</div>
                            <div class="dropdown-profile-email">{{ $user['email'] ?? '' }}</div>
                            <span class="dropdown-profile-role">{{ $userRole ?? 'user' }}</span>
                        </div>
                    </div>

                    <!-- Info -->
                    <div class="dropdown-section">
                        @if($user['telefono'] ?? false)
                        <div class="dropdown-info-item">
                            <i class="fas fa-phone"></i>
                            <div>
                                <div class="dropdown-info-label">Teléfono</div>
                                <div class="dropdown-info-value">{{ $user['telefono'] }}</div>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Actions -->
                    <div class="dropdown-actions">
                        <button class="dropdown-action-btn" data-bs-toggle="modal" data-bs-target="#cambiarPasswordModal">
                            <i class="fas fa-key"></i>
                            Cambiar contraseña
                        </button>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-action-btn danger">
                                <i class="fas fa-sign-out-alt"></i>
                                Cerrar sesión
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- MAIN AREA -->
    <main class="main-content">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
            <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle"></i>
            {{ session('error') }}
            <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show">
            <i class="fas fa-exclamation-triangle"></i>
            {{ session('warning') }}
            <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show">
            <i class="fas fa-info-circle"></i>
            {{ session('info') }}
            <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @yield('content')
    </main>
</div>

<!-- Mobile overlay -->
<div id="sidebarOverlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:999;" onclick="closeMobileSidebar()"></div>

<!-- Modal Cambiar Contraseña -->
@include('layouts.partials.modal-cambiar-password')

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
(function() {
    const sidebar   = document.getElementById('sidebar');
    const content   = document.getElementById('content');
    const toggleBtn = document.getElementById('sidebarToggle');
    const toggleIcon = document.getElementById('toggleIcon');
    const mobileToggle = document.getElementById('mobileToggle');
    const overlay   = document.getElementById('sidebarOverlay');
    const userBtn   = document.getElementById('userBtn');
    const userDropdown = document.getElementById('userDropdown');

    // Restore sidebar state
    const collapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    if (collapsed) {
        sidebar.classList.add('collapsed');
        content.classList.add('expanded');
        toggleIcon.className = 'fas fa-chevron-right';
    }

    // Desktop toggle
    toggleBtn?.addEventListener('click', () => {
        const isCollapsed = sidebar.classList.toggle('collapsed');
        content.classList.toggle('expanded', isCollapsed);
        toggleIcon.className = isCollapsed ? 'fas fa-chevron-right' : 'fas fa-chevron-left';
        localStorage.setItem('sidebarCollapsed', isCollapsed);
    });

    // Mobile toggle
    mobileToggle?.addEventListener('click', () => {
        sidebar.classList.add('mobile-open');
        overlay.style.display = 'block';
    });

    window.closeMobileSidebar = function() {
        sidebar.classList.remove('mobile-open');
        overlay.style.display = 'none';
    };

    // User dropdown
    userBtn?.addEventListener('click', (e) => {
        e.stopPropagation();
        userDropdown.classList.toggle('show');
    });
    document.addEventListener('click', (e) => {
        if (!userBtn?.contains(e.target) && !userDropdown?.contains(e.target)) {
            userDropdown?.classList.remove('show');
        }
    });

    // Auto-dismiss alerts after 5s
    setTimeout(() => {
        document.querySelectorAll('.alert.fade.show').forEach(el => {
            try { bootstrap.Alert.getOrCreateInstance(el).close(); } catch(e) {}
        });
    }, 5000);
})();
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    @if(session('success'))
    Swal.fire({ icon:'success', title:'¡Listo!', text:'{{ session("success") }}',
        timer:3000, showConfirmButton:false, position:'top-end', toast:true,
        background:'#0f1117', color:'#4ade80', iconColor:'#22c55e',
        customClass:{ popup:'rounded-3' } });
    @endif
    @if(session('error'))
    Swal.fire({ icon:'error', title:'Error', text:'{{ session("error") }}',
        timer:4000, showConfirmButton:false, position:'top-end', toast:true,
        background:'#0f1117', color:'#fca5a5', iconColor:'#ef4444',
        customClass:{ popup:'rounded-3' } });
    @endif
    @if(session('warning'))
    Swal.fire({ icon:'warning', title:'Atención', text:'{{ session("warning") }}',
        timer:4000, showConfirmButton:false, position:'top-end', toast:true,
        background:'#0f1117', color:'#fde68a', iconColor:'#f59e0b',
        customClass:{ popup:'rounded-3' } });
    @endif
});
</script>

@stack('scripts')
</body>
</html>