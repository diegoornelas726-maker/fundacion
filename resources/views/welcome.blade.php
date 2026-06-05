<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Fundación Don Benjamín') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif;
            background-color: #09090b;
            color: #fff;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            -webkit-font-smoothing: antialiased;
            overflow: hidden;
            position: relative;
        }

        /* Orbes animados de fondo */
        .orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(90px);
            pointer-events: none;
            animation: drift ease-in-out infinite alternate;
        }

        .orb-1 {
            width: 580px; height: 580px;
            background: radial-gradient(circle, rgba(79,70,229,0.18) 0%, transparent 70%);
            top: -220px; left: -160px;
            animation-duration: 14s;
            animation-delay: 0s;
        }

        .orb-2 {
            width: 480px; height: 480px;
            background: radial-gradient(circle, rgba(59,130,246,0.13) 0%, transparent 70%);
            bottom: -180px; right: -120px;
            animation-duration: 18s;
            animation-delay: -6s;
        }

        .orb-3 {
            width: 280px; height: 280px;
            background: radial-gradient(circle, rgba(99,102,241,0.10) 0%, transparent 70%);
            top: 55%; left: 65%;
            animation-duration: 10s;
            animation-delay: -3s;
        }

        @keyframes drift {
            0%   { transform: translate(0, 0) scale(1); }
            50%  { transform: translate(25px, -18px) scale(1.04); }
            100% { transform: translate(-18px, 28px) scale(0.96); }
        }

        /* Grid sutil */
        body::after {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.017) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.017) 1px, transparent 1px);
            background-size: 52px 52px;
            pointer-events: none;
            z-index: 0;
        }

        /* Partículas */
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

        /* ── Contenedor principal ── */
        .wrapper {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 420px;
            padding: 0 24px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* ── Ícono: entra desde arriba ── */
        .badge {
            width: 62px;
            height: 62px;
            border-radius: 16px;
            background: linear-gradient(145deg, #4f46e5 0%, #3b82f6 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            box-shadow:
                0 0 0 1px rgba(99,102,241,0.35),
                0 8px 32px rgba(79,70,229,0.35);
            opacity: 0;
            animation:
                slideDown 0.7s cubic-bezier(.22,.68,0,1.3) 0.1s forwards,
                pulse-glow 3s ease-in-out 1s infinite;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-45px) scale(0.85); }
            to   { opacity: 1; transform: translateY(0)    scale(1); }
        }

        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 0 1px rgba(99,102,241,0.35), 0 8px 32px rgba(79,70,229,0.35), 0 0 50px rgba(79,70,229,0.12); }
            50%       { box-shadow: 0 0 0 1px rgba(99,102,241,0.55), 0 8px 40px rgba(79,70,229,0.55), 0 0 80px rgba(79,70,229,0.22); }
        }

        .badge svg { width: 30px; height: 30px; color: #fff; }

        /* ── Título: entra desde la izquierda ── */
        .title {
            font-size: 25px;
            font-weight: 800;
            letter-spacing: -0.7px;
            color: #f4f4f5;
            text-align: center;
            margin-bottom: 7px;
            line-height: 1.25;
            opacity: 0;
            animation: slideFromLeft 0.65s cubic-bezier(.22,.68,0,1.2) 0.3s forwards;
        }

        @keyframes slideFromLeft {
            from { opacity: 0; transform: translateX(-50px); }
            to   { opacity: 1; transform: translateX(0); }
        }

        /* ── Subtítulo: entra desde la derecha ── */
        .subtitle {
            font-size: 13px;
            color: #52525b;
            text-align: center;
            margin-bottom: 32px;
            letter-spacing: 0.2px;
            opacity: 0;
            animation: slideFromRight 0.65s cubic-bezier(.22,.68,0,1.2) 0.45s forwards;
        }

        @keyframes slideFromRight {
            from { opacity: 0; transform: translateX(50px); }
            to   { opacity: 1; transform: translateX(0); }
        }

        /* ── Tarjeta: entra desde abajo ── */
        .card {
            width: 100%;
            background: rgba(18,18,20,0.85);
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 20px;
            padding: 30px 28px;
            backdrop-filter: blur(18px);
            box-shadow:
                0 20px 60px rgba(0,0,0,0.6),
                0 1px 0 rgba(255,255,255,0.05) inset;
            opacity: 0;
            animation: slideUp 0.7s cubic-bezier(.22,.68,0,1.2) 0.55s forwards;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(45px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .card-label {
            font-size: 10.5px;
            font-weight: 600;
            letter-spacing: 1.4px;
            text-transform: uppercase;
            color: #3f3f46;
            text-align: center;
            margin-bottom: 20px;
        }

        .divider-line {
            width: 100%;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.07), transparent);
            margin-bottom: 20px;
        }

        /* ── Botones ── */
        .btn-group {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            width: 100%;
            padding: 12px 20px;
            border-radius: 11px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        /* Botón secundario: entra desde la izquierda */
        .btn-secondary {
            background: rgba(255,255,255,0.04);
            color: #71717a;
            border: 1px solid rgba(255,255,255,0.09);
            opacity: 0;
            animation: slideFromLeft 0.6s cubic-bezier(.22,.68,0,1.2) 0.8s forwards;
        }

        .btn-secondary:hover {
            background: rgba(255,255,255,0.08);
            color: #d4d4d8;
            border-color: rgba(255,255,255,0.15);
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.3);
        }

        .btn-secondary:active { transform: translateY(0); }

        /* Botón primario: entra desde la derecha */
        .btn-primary {
            background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
            color: #fff;
            border: none;
            box-shadow: 0 4px 18px rgba(79,70,229,0.4), 0 1px 0 rgba(255,255,255,0.12) inset;
            opacity: 0;
            animation: slideFromRight 0.6s cubic-bezier(.22,.68,0,1.2) 0.95s forwards;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #6366f1 0%, #60a5fa 100%);
            box-shadow: 0 8px 28px rgba(99,102,241,0.55), 0 1px 0 rgba(255,255,255,0.18) inset;
            transform: translateY(-2px);
        }

        .btn-primary:active { transform: translateY(0); }

        /* Botón dashboard (ya autenticado): entra desde abajo */
        .btn-dashboard {
            background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
            color: #fff;
            border: none;
            box-shadow: 0 4px 18px rgba(79,70,229,0.4);
            opacity: 0;
            animation: slideUp 0.6s cubic-bezier(.22,.68,0,1.2) 0.8s forwards;
        }

        .btn-dashboard:hover {
            background: linear-gradient(135deg, #6366f1 0%, #60a5fa 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(99,102,241,0.55);
        }

        /* ── Footer: aparece al final ── */
        .footer {
            margin-top: 24px;
            font-size: 11.5px;
            color: #27272a;
            text-align: center;
            opacity: 0;
            animation: fadeIn 0.6s ease 1.1s forwards;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to   { opacity: 1; }
        }
    </style>
</head>
<body>

    <!-- Orbes de fondo -->
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <!-- Partículas flotantes -->
    <div class="particles" id="particles"></div>

    <div class="wrapper">

        <!-- Ícono: baja desde arriba -->
        <div class="badge">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 21V9l9-6 9 6v12M9 21V12h6v9"/>
            </svg>
        </div>

        <!-- Título: entra desde la izquierda -->
        <h1 class="title">Fundación Don Benjamín</h1>

        <!-- Subtítulo: entra desde la derecha -->
        <p class="subtitle">Sistema de Control Administrativo Interno</p>

        <!-- Tarjeta: sube desde abajo -->
        @if (Route::has('login'))
            <div class="card">
                <p class="card-label">Acceso al sistema</p>
                <div class="divider-line"></div>

                <div class="btn-group">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn btn-dashboard">
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="3" width="7" height="7" rx="1"/>
                                <rect x="14" y="3" width="7" height="7" rx="1"/>
                                <rect x="14" y="14" width="7" height="7" rx="1"/>
                                <rect x="3" y="14" width="7" height="7" rx="1"/>
                            </svg>
                            Ir al Dashboard
                        </a>
                    @else
                        <!-- Iniciar sesión: entra desde la izquierda -->
                        <a href="{{ route('login') }}" class="btn btn-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                                <polyline points="10 17 15 12 10 7"/>
                                <line x1="15" y1="12" x2="3" y2="12"/>
                            </svg>
                            Iniciar sesión
                        </a>

                        @if (Route::has('register'))
                            <!-- Crear cuenta: entra desde la derecha -->
                            <a href="{{ route('register') }}" class="btn btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                    <circle cx="9" cy="7" r="4"/>
                                    <line x1="19" y1="8" x2="19" y2="14"/>
                                    <line x1="22" y1="11" x2="16" y2="11"/>
                                </svg>
                                Crear cuenta
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
        @endif

        <p class="footer">© {{ date('Y') }} Fundación Don Benjamín · Uso interno</p>
    </div>

    <script>
        // Generar partículas flotantes
        const container = document.getElementById('particles');
        for (let i = 0; i < 20; i++) {
            const p = document.createElement('div');
            p.className = 'particle';
            const size  = Math.random() * 3 + 1;
            const left  = Math.random() * 100;
            const delay = Math.random() * 20;
            const dur   = Math.random() * 14 + 12;
            p.style.cssText = `
                width: ${size}px; height: ${size}px;
                left: ${left}%;
                background: rgba(${Math.random() > 0.5 ? '99,102,241' : '59,130,246'}, 0.5);
                animation-duration: ${dur}s;
                animation-delay: -${delay}s;
            `;
            container.appendChild(p);
        }
    </script>
</body>
</html>