<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Fundación Don Benjamín') }}</title>

    <script>
        // Aplica el tema guardado antes de pintar (evita el parpadeo)
        (function () {
            const t = localStorage.getItem('theme') || 'dark';
            document.documentElement.setAttribute('data-theme', t);
        })();
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet"/>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif;
            background-color: #09090b;
            color: #e4e4e7;
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
            position: relative;
            overflow-x: hidden;
        }

        /* ── Orbes animados de fondo ── */
        .orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(90px);
            pointer-events: none;
            z-index: 0;
            animation: drift ease-in-out infinite alternate;
        }

        .orb-1 {
            width: 580px; height: 580px;
            background: radial-gradient(circle, rgba(79,70,229,0.12) 0%, transparent 70%);
            top: -220px; left: -160px;
            animation-duration: 14s;
        }

        .orb-2 {
            width: 480px; height: 480px;
            background: radial-gradient(circle, rgba(59,130,246,0.09) 0%, transparent 70%);
            bottom: -180px; right: -120px;
            animation-duration: 18s;
            animation-delay: -6s;
        }

        .orb-3 {
            width: 280px; height: 280px;
            background: radial-gradient(circle, rgba(99,102,241,0.07) 0%, transparent 70%);
            top: 55%; left: 65%;
            animation-duration: 10s;
            animation-delay: -3s;
        }

        @keyframes drift {
            0%   { transform: translate(0, 0) scale(1); }
            50%  { transform: translate(25px, -18px) scale(1.04); }
            100% { transform: translate(-18px, 28px) scale(0.96); }
        }

        /* ── Grid sutil ── */
        body::after {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.015) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.015) 1px, transparent 1px);
            background-size: 52px 52px;
            pointer-events: none;
            z-index: 0;
        }

        /* ── Partículas flotantes ── */
        .particles {
            position: fixed;
            inset: 0;
            pointer-events: none;
            overflow: hidden;
            z-index: 1;
        }

        .particle {
            position: absolute;
            border-radius: 50%;
            animation: float-up linear infinite;
        }

        @keyframes float-up {
            0%   { transform: translateY(110vh) rotate(0deg);   opacity: 0; }
            8%   { opacity: 1; }
            92%  { opacity: 1; }
            100% { transform: translateY(-10vh) rotate(540deg); opacity: 0; }
        }

        /* ── Navbar ── */
        .navbar {
            background: rgba(12, 12, 14, 0.85);
            border-bottom: 1px solid rgba(255,255,255,0.06);
            backdrop-filter: blur(14px);
            position: sticky;
            top: 0;
            z-index: 50;
            transition: box-shadow 0.3s, background 0.3s;
        }

        .navbar.scrolled {
            background: rgba(10, 10, 12, 0.97);
            box-shadow: 0 4px 24px rgba(0,0,0,0.4);
            border-bottom-color: rgba(255,255,255,0.09);
        }

        .navbar-inner {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .nav-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            transition: opacity 0.15s;
        }

        .nav-logo:hover { opacity: 0.85; }

        .nav-logo-icon {
            width: 34px;
            height: 34px;
            border-radius: 9px;
            background: linear-gradient(145deg, #4f46e5, #3b82f6);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 0 1px rgba(99,102,241,0.3);
            transition: box-shadow 0.2s;
        }

        .nav-logo:hover .nav-logo-icon {
            box-shadow: 0 0 0 1px rgba(99,102,241,0.5), 0 4px 14px rgba(79,70,229,0.35);
        }

        .nav-logo-icon svg { width: 18px; height: 18px; color: #fff; }

        .nav-logo-text {
            font-size: 14px;
            font-weight: 700;
            color: #e4e4e7;
            letter-spacing: -0.3px;
        }

        /* ── Nav links ── */
        .nav-links {
            display: flex;
            align-items: center;
            gap: 2px;
            position: relative;
        }

        .nav-link {
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 13.5px;
            font-weight: 500;
            color: #71717a;
            text-decoration: none;
            transition: color 0.15s, background 0.15s;
            position: relative;
        }

        .nav-link:hover { color: #e4e4e7; background: rgba(255,255,255,0.05); }

        .nav-link.active {
            color: #e4e4e7;
            background: rgba(99,102,241,0.12);
        }

        /* ── Barra deslizante de la pestaña activa ── */
        .nav-indicator {
            position: absolute;
            bottom: -1px;
            left: 0;
            width: 0;
            height: 2px;
            background: #6366f1;
            border-radius: 2px;
            opacity: 0;
            box-shadow: 0 0 8px rgba(99,102,241,0.6);
            transition: left 0.32s cubic-bezier(.22,.68,0,1.2),
                        width 0.32s cubic-bezier(.22,.68,0,1.2),
                        opacity 0.2s ease;
            pointer-events: none;
        }

        /* ── User dropdown ── */
        .nav-user { position: relative; }

        .nav-user-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 6px 12px;
            border-radius: 8px;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.07);
            cursor: pointer;
            font-size: 13.5px;
            font-weight: 500;
            color: #a1a1aa;
            transition: all 0.15s;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .nav-user-btn:hover { background: rgba(255,255,255,0.08); color: #e4e4e7; }

        .nav-user-avatar {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background: linear-gradient(135deg, #4f46e5, #3b82f6);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
            color: #fff;
        }

        .nav-user-btn .chevron {
            width: 14px;
            height: 14px;
            transition: transform 0.22s cubic-bezier(.22,.68,0,1.2);
        }

        .nav-user-btn.open .chevron { transform: rotate(180deg); }

        .dropdown-menu {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            min-width: 180px;
            background: rgba(18,18,20,0.98);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 12px;
            padding: 6px;
            box-shadow: 0 16px 48px rgba(0,0,0,0.5);
            backdrop-filter: blur(12px);
            transform-origin: top right;
            transform: scale(0.94) translateY(-6px);
            opacity: 0;
            pointer-events: none;
            transition: transform 0.2s cubic-bezier(.22,.68,0,1.2), opacity 0.18s ease;
        }

        .dropdown-menu.open {
            opacity: 1;
            pointer-events: auto;
            animation: dropdownBounce 0.5s cubic-bezier(.18,.89,.32,1.28) forwards;
        }

        /* Rebote elástico al desplegar el menú de usuario */
        @keyframes dropdownBounce {
            0%   { opacity: 0; transform: scale(0.85) translateY(-10px); }
            45%  { opacity: 1; transform: scale(1.04) translateY(3px); }
            70%  { transform: scale(0.98) translateY(-1px); }
            85%  { transform: scale(1.012) translateY(1px); }
            100% { opacity: 1; transform: scale(1) translateY(0); }
        }

        .dropdown-header {
            padding: 8px 10px 10px;
            border-bottom: 1px solid rgba(255,255,255,0.06);
            margin-bottom: 4px;
        }

        .dropdown-header-name { font-size: 13px; font-weight: 600; color: #e4e4e7; }

        .dropdown-header-email {
            font-size: 11.5px;
            color: #52525b;
            margin-top: 1px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 160px;
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 10px;
            border-radius: 8px;
            font-size: 13.5px;
            font-weight: 500;
            color: #a1a1aa;
            text-decoration: none;
            transition: all 0.15s;
            cursor: pointer;
            width: 100%;
            background: none;
            border: none;
            font-family: 'Plus Jakarta Sans', sans-serif;
            text-align: left;
        }

        .dropdown-item:hover { background: rgba(255,255,255,0.06); color: #e4e4e7; }
        .dropdown-item svg { width: 15px; height: 15px; }
        .dropdown-item.danger { color: #f87171; }
        .dropdown-item.danger:hover { background: rgba(239,68,68,0.08); color: #fca5a5; }

        /* ── Page header ── */
        .page-header {
            border-bottom: 1px solid rgba(255,255,255,0.06);
            background: rgba(12,12,14,0.5);
            position: relative;
            z-index: 10;
        }

        .page-header-inner {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px 24px;
        }

        .page-header h1 {
            font-size: 18px;
            font-weight: 700;
            color: #f4f4f5;
            letter-spacing: -0.4px;
        }

        /* ── Page content con fade-in ── */
        .page-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 32px 24px;
            position: relative;
            z-index: 10;
            animation: pageFadeIn 0.45s ease both;
        }

        @keyframes pageFadeIn {
            from { opacity: 0; transform: translateY(12px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── Botón de tema (sol/luna) ── */
        .theme-toggle {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.07);
            color: #a1a1aa;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            transition: background 0.15s, color 0.15s, border-color 0.15s;
            position: relative;
            overflow: hidden;
        }

        .theme-toggle:hover { background: rgba(255,255,255,0.08); color: #e4e4e7; }
        .theme-toggle svg {
            width: 18px; height: 18px;
            position: absolute;
            transition: transform 0.4s cubic-bezier(.22,.68,0,1.3), opacity 0.3s ease;
        }
        .theme-toggle .icon-sun  { transform: rotate(90deg) scale(0); opacity: 0; }
        .theme-toggle .icon-moon { transform: rotate(0deg) scale(1);  opacity: 1; }
        [data-theme="light"] .theme-toggle .icon-sun  { transform: rotate(0deg) scale(1);  opacity: 1; }
        [data-theme="light"] .theme-toggle .icon-moon { transform: rotate(-90deg) scale(0); opacity: 0; }

        .nav-user { display: flex; align-items: center; }

        /* ════════════════════════════════════════════
           TEMA CLARO
           ════════════════════════════════════════════ */
        [data-theme="light"] body { background-color: #f4f5f7; color: #27272a; }

        [data-theme="light"] .orb-1 { background: radial-gradient(circle, rgba(79,70,229,0.10) 0%, transparent 70%); }
        [data-theme="light"] .orb-2 { background: radial-gradient(circle, rgba(59,130,246,0.08) 0%, transparent 70%); }
        [data-theme="light"] .orb-3 { background: radial-gradient(circle, rgba(99,102,241,0.07) 0%, transparent 70%); }
        [data-theme="light"] body::after {
            background-image:
                linear-gradient(rgba(0,0,0,0.025) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0,0,0,0.025) 1px, transparent 1px);
        }

        [data-theme="light"] .navbar {
            background: rgba(255,255,255,0.85);
            border-bottom-color: rgba(0,0,0,0.07);
        }
        [data-theme="light"] .navbar.scrolled {
            background: rgba(255,255,255,0.97);
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
        }
        [data-theme="light"] .nav-logo-text { color: #18181b; }
        [data-theme="light"] .nav-link { color: #71717a; }
        [data-theme="light"] .nav-link:hover { color: #18181b; background: rgba(0,0,0,0.05); }
        [data-theme="light"] .nav-link.active { color: #4f46e5; background: rgba(99,102,241,0.1); }

        [data-theme="light"] .nav-user-btn,
        [data-theme="light"] .theme-toggle {
            background: rgba(0,0,0,0.03);
            border-color: rgba(0,0,0,0.08);
            color: #52525b;
        }
        [data-theme="light"] .nav-user-btn:hover,
        [data-theme="light"] .theme-toggle:hover { background: rgba(0,0,0,0.06); color: #18181b; }

        [data-theme="light"] .dropdown-menu {
            background: rgba(255,255,255,0.98);
            border-color: rgba(0,0,0,0.08);
            box-shadow: 0 16px 48px rgba(0,0,0,0.14);
        }
        [data-theme="light"] .dropdown-header { border-bottom-color: rgba(0,0,0,0.07); }
        [data-theme="light"] .dropdown-header-name { color: #18181b; }
        [data-theme="light"] .dropdown-item { color: #52525b; }
        [data-theme="light"] .dropdown-item:hover { background: rgba(0,0,0,0.05); color: #18181b; }

        [data-theme="light"] .page-header {
            background: rgba(255,255,255,0.6);
            border-bottom-color: rgba(0,0,0,0.07);
        }
        [data-theme="light"] .page-header h1 { color: #18181b; }

        /* Superficies compartidas (dashboard, tablas) */
        [data-theme="light"] .welcome-card,
        [data-theme="light"] .stat-card,
        [data-theme="light"] .table-card {
            background: rgba(255,255,255,0.9);
            border-color: rgba(0,0,0,0.07);
            box-shadow: 0 8px 32px rgba(0,0,0,0.06);
        }
        [data-theme="light"] .welcome-text h2,
        [data-theme="light"] .stat-number,
        [data-theme="light"] .td-name { color: #18181b; }
        [data-theme="light"] .welcome-text p,
        [data-theme="light"] .stat-desc,
        [data-theme="light"] tbody td { color: #52525b; }
        [data-theme="light"] .stat-label,
        [data-theme="light"] thead th { color: #71717a; }
        [data-theme="light"] thead tr { border-bottom-color: rgba(0,0,0,0.08); }
        [data-theme="light"] tbody tr { border-bottom-color: rgba(0,0,0,0.05); }
        [data-theme="light"] tbody tr:hover { background: rgba(0,0,0,0.03); }
        [data-theme="light"] .stat-bar { background: rgba(0,0,0,0.06); }

        [data-theme="light"] .search-input,
        [data-theme="light"] .filter-select {
            background: rgba(0,0,0,0.03);
            border-color: rgba(0,0,0,0.1);
            color: #18181b;
        }
        [data-theme="light"] .search-input::placeholder { color: #a1a1aa; }
        [data-theme="light"] .filter-select option { background: #fff; }
        [data-theme="light"] .btn-search {
            background: rgba(0,0,0,0.04);
            border-color: rgba(0,0,0,0.1);
            color: #52525b;
        }
        [data-theme="light"] .btn-search:hover { background: rgba(0,0,0,0.07); color: #18181b; }
        [data-theme="light"] .empty-state p { color: #71717a; }
        [data-theme="light"] .pagination-wrap { border-top-color: rgba(0,0,0,0.07); }

        /* Formularios (crear/editar) y perfil */
        [data-theme="light"] .form-card,
        [data-theme="light"] .profile-card {
            background: rgba(255,255,255,0.9);
            border-color: rgba(0,0,0,0.07);
            box-shadow: 0 8px 32px rgba(0,0,0,0.06);
        }
        [data-theme="light"] .card-title { color: #18181b; }
        [data-theme="light"] .card-desc,
        [data-theme="light"] .field-note { color: #71717a; }
        [data-theme="light"] .divider { background: rgba(0,0,0,0.08); }
        [data-theme="light"] .section-label {
            color: #71717a;
            border-bottom-color: rgba(0,0,0,0.08);
        }
        [data-theme="light"] .field label { color: #52525b; }
        [data-theme="light"] .field input,
        [data-theme="light"] .field select,
        [data-theme="light"] .field textarea {
            background: rgba(0,0,0,0.03);
            border-color: rgba(0,0,0,0.1);
            color: #18181b;
        }
        [data-theme="light"] .field input::placeholder,
        [data-theme="light"] .field textarea::placeholder { color: #a1a1aa; }
        [data-theme="light"] .field select option { background: #fff; }
        [data-theme="light"] .field input:focus,
        [data-theme="light"] .field select:focus,
        [data-theme="light"] .field textarea:focus {
            background: #fff;
            border-color: #6366f1;
        }
        [data-theme="light"] .tipo-chip {
            background: rgba(99,102,241,0.08);
            border-color: rgba(99,102,241,0.2);
            color: #4f46e5;
        }
        [data-theme="light"] .tipo-chip:hover { background: rgba(99,102,241,0.16); color: #4338ca; }
        [data-theme="light"] .btn-cancel {
            background: rgba(0,0,0,0.04);
            border-color: rgba(0,0,0,0.1);
            color: #52525b;
        }
        [data-theme="light"] .btn-cancel:hover { background: rgba(0,0,0,0.07); color: #18181b; }
        [data-theme="light"] .form-actions { border-top-color: rgba(0,0,0,0.07); }
    </style>
</head>
<body>

    <!-- Orbes de fondo -->
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <!-- Partículas flotantes -->
    <div class="particles" id="particles"></div>

    <nav class="navbar" id="main-navbar">
        <div class="navbar-inner">

            <a href="{{ route('dashboard') }}" class="nav-logo">
                <div class="nav-logo-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 21V9l9-6 9 6v12M9 21V12h6v9"/>
                    </svg>
                </div>
                <span class="nav-logo-text">Fundación Don Benjamín</span>
            </a>

            <div class="nav-links">
                <a href="{{ route('dashboard') }}"
                   class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    Dashboard
                </a>
                <a href="{{ route('beneficiarios.index') }}"
                   class="nav-link {{ request()->routeIs('beneficiarios.*') ? 'active' : '' }}">
                    Beneficiarios
                </a>
                <a href="{{ route('apoyos.index') }}"
                   class="nav-link {{ request()->routeIs('apoyos.*') ? 'active' : '' }}">
                    Apoyos
                </a>
                <a href="{{ route('actividades.index') }}"
                   class="nav-link {{ request()->routeIs('actividades.*') ? 'active' : '' }}">
                    Actividades
                </a>

                <span class="nav-indicator" id="nav-indicator"></span>
            </div>

            <div class="nav-user">
                <button class="theme-toggle" id="theme-toggle" onclick="toggleTheme()" aria-label="Cambiar tema" title="Cambiar tema">
                    <svg class="icon-moon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z"/>
                    </svg>
                    <svg class="icon-sun" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z"/>
                    </svg>
                </button>

                <button class="nav-user-btn" id="user-menu-btn" onclick="toggleDropdown()">
                    <div class="nav-user-avatar">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    {{ Auth::user()->name }}
                    <svg class="chevron" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                    </svg>
                </button>

                <div class="dropdown-menu" id="user-dropdown">
                    <div class="dropdown-header">
                        <div class="dropdown-header-name">{{ Auth::user()->name }}</div>
                        <div class="dropdown-header-email">{{ Auth::user()->email }}</div>
                    </div>

                    <a href="{{ route('profile.edit') }}" class="dropdown-item">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/>
                        </svg>
                        Mi perfil
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item danger">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75"/>
                            </svg>
                            Cerrar sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    @isset($header)
        <header class="page-header">
            <div class="page-header-inner">{{ $header }}</div>
        </header>
    @endisset

    <main class="page-content">
        {{ $slot }}
    </main>

    <script>
        // Cambio de tema claro/oscuro (persistente)
        function toggleTheme() {
            const root = document.documentElement;
            const next = root.getAttribute('data-theme') === 'light' ? 'dark' : 'light';
            root.setAttribute('data-theme', next);
            localStorage.setItem('theme', next);
        }

        // Salida animada al eliminar una fila de la tabla
        document.addEventListener('submit', function (e) {
            const form = e.target;
            const row  = form.closest && form.closest('tr');
            if (!row || !form.querySelector('.btn-del')) return;
            if (e.defaultPrevented) return; // el usuario canceló el confirm()
            e.preventDefault();
            row.classList.add('row-removing');
            setTimeout(() => form.submit(), 300);
        });

        // Dropdown toggle
        function toggleDropdown() {
            const btn  = document.getElementById('user-menu-btn');
            const menu = document.getElementById('user-dropdown');
            btn.classList.toggle('open');
            menu.classList.toggle('open');
        }

        document.addEventListener('click', function(e) {
            const btn  = document.getElementById('user-menu-btn');
            const menu = document.getElementById('user-dropdown');
            if (!btn.contains(e.target) && !menu.contains(e.target)) {
                btn.classList.remove('open');
                menu.classList.remove('open');
            }
        });

        // Navbar con sombra al hacer scroll
        window.addEventListener('scroll', function() {
            document.getElementById('main-navbar').classList.toggle('scrolled', window.scrollY > 10);
        });

        // Barra deslizante de la pestaña activa
        (function () {
            const navLinks  = document.querySelector('.nav-links');
            const indicator = document.getElementById('nav-indicator');
            if (!navLinks || !indicator) return;

            const links      = navLinks.querySelectorAll('.nav-link');
            const activeLink = navLinks.querySelector('.nav-link.active');

            function moveTo(el, animate = true) {
                indicator.style.transition = animate ? '' : 'none';
                if (!el) { indicator.style.opacity = '0'; return; }
                const navRect  = navLinks.getBoundingClientRect();
                const rect     = el.getBoundingClientRect();
                const width    = Math.max(18, rect.width * 0.55);
                indicator.style.width   = width + 'px';
                indicator.style.left    = (rect.left - navRect.left + (rect.width - width) / 2) + 'px';
                indicator.style.opacity = '1';
            }

            // Posición inicial sin animar (evita el "salto" al cargar)
            requestAnimationFrame(() => moveTo(activeLink, false));

            links.forEach(link => link.addEventListener('mouseenter', () => moveTo(link)));
            navLinks.addEventListener('mouseleave', () => moveTo(activeLink));
            window.addEventListener('resize', () => moveTo(activeLink, false));
        })();

        // Partículas flotantes (sutiles, menos densas que en el login)
        const container = document.getElementById('particles');
        const colors = ['rgba(99,102,241,', 'rgba(59,130,246,', 'rgba(139,92,246,'];

        for (let i = 0; i < 14; i++) {
            const p = document.createElement('div');
            p.className = 'particle';
            const size    = Math.random() * 3 + 1;
            const color   = colors[Math.floor(Math.random() * colors.length)];
            const opacity = (Math.random() * 0.25 + 0.08).toFixed(2);
            p.style.cssText = `
                width: ${size}px;
                height: ${size}px;
                left: ${Math.random() * 100}%;
                background: ${color}${opacity});
                animation-duration: ${Math.random() * 16 + 12}s;
                animation-delay: -${Math.random() * 16}s;
            `;
            container.appendChild(p);
        }
    </script>

    <!-- Toast Global Component -->
    @if (session('success') || session('error') || session('info'))
        @php
            $toastType = 'success';
            $toastMessage = '';
            if (session('success')) {
                $toastType = 'success';
                $toastMessage = session('success');
            } elseif (session('error')) {
                $toastType = 'error';
                $toastMessage = session('error');
            } elseif (session('info')) {
                $toastType = 'info';
                $toastMessage = session('info');
            }
        @endphp

        <div x-data="{ 
                show: false, 
                progress: 100,
                init() {
                    setTimeout(() => { this.show = true; }, 100);
                    const duration = 4000;
                    const intervalTime = 40;
                    const step = (intervalTime / duration) * 100;
                    const timer = setInterval(() => {
                        this.progress -= step;
                        if (this.progress <= 0) {
                            this.progress = 0;
                            clearInterval(timer);
                            this.close();
                        }
                    }, intervalTime);
                },
                close() {
                    this.show = false;
                }
            }"
            x-show="show"
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="translate-y-2 opacity-0 translate-x-8"
            x-transition:enter-end="translate-y-0 opacity-100 translate-x-0"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="translate-y-0 opacity-100 translate-x-0"
            x-transition:leave-end="translate-y-2 opacity-0 translate-x-8"
            class="toast-container {{ $toastType }}"
            style="display: none;"
        >
            <div class="toast-content">
                @if ($toastType === 'success')
                    <div class="toast-icon success">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                        </svg>
                    </div>
                @elseif ($toastType === 'error')
                    <div class="toast-icon error">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/>
                        </svg>
                    </div>
                @else
                    <div class="toast-icon info">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 1 1 1.085 1.085l-.04.02m0 0a1.5 1.5 0 1 0 1.5 1.5h-1.5V11.25z"/>
                        </svg>
                    </div>
                @endif

                <div class="toast-message">{{ $toastMessage }}</div>

                <button @click="close()" class="toast-close">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="toast-progress-bar">
                <div class="toast-progress-fill" :style="'width: ' + progress + '%'"></div>
            </div>
        </div>
    @endif
</body>
</html>