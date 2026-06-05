<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Fundación Don Benjamín') }}</title>

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
        }

        .navbar {
            background: rgba(12, 12, 14, 0.95);
            border-bottom: 1px solid rgba(255,255,255,0.06);
            backdrop-filter: blur(12px);
            position: sticky;
            top: 0;
            z-index: 50;
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
        }

        .nav-logo-icon {
            width: 34px;
            height: 34px;
            border-radius: 9px;
            background: linear-gradient(145deg, #4f46e5, #3b82f6);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 0 1px rgba(99,102,241,0.3);
        }

        .nav-logo-icon svg { width: 18px; height: 18px; color: #fff; }

        .nav-logo-text {
            font-size: 14px;
            font-weight: 700;
            color: #e4e4e7;
            letter-spacing: -0.3px;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .nav-link {
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 13.5px;
            font-weight: 500;
            color: #71717a;
            text-decoration: none;
            transition: all 0.15s;
        }

        .nav-link:hover { color: #e4e4e7; background: rgba(255,255,255,0.05); }
        .nav-link.active { color: #e4e4e7; background: rgba(99,102,241,0.12); }

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

        .nav-user-btn svg { width: 14px; height: 14px; transition: transform 0.2s; }
        .nav-user-btn.open svg { transform: rotate(180deg); }

        .dropdown-menu {
            display: none;
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
        }

        .dropdown-menu.open { display: block; }

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

        .page-header {
            border-bottom: 1px solid rgba(255,255,255,0.06);
            background: rgba(12,12,14,0.5);
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

        .page-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 32px 24px;
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <div class="navbar-inner">

            <a href="{{ route('dashboard') }}" class="nav-logo">
                <div class="nav-logo-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 21V9l9-6 9 6v12M9 21V12h6v9"/>
                    </svg>
                </div>
                <span class="nav-logo-text">Fundación Don Benjamín</span>
            </a>

            <!-- Links de navegación -->
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
            </div>

            <div class="nav-user">
                <button class="nav-user-btn" id="user-menu-btn" onclick="toggleDropdown()">
                    <div class="nav-user-avatar">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    {{ Auth::user()->name }}
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
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
    </script>
</body>
</html>