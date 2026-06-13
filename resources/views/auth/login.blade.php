<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar sesión — {{ config('app.name') }}</title>

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

        /* ── Orbes animados de fondo ── */
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

        /* ── Grid sutil ── */
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

        /* ── Wrapper ── */
        .wrapper {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 400px;
            padding: 0 24px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* ── Badge: baja desde arriba ── */
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
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 0 1px rgba(99,102,241,0.35), 0 8px 32px rgba(79,70,229,0.35), 0 0 50px rgba(79,70,229,0.12); }
            50%       { box-shadow: 0 0 0 1px rgba(99,102,241,0.55), 0 8px 40px rgba(79,70,229,0.55), 0 0 80px rgba(79,70,229,0.22); }
        }

        .badge svg { width: 30px; height: 30px; color: #fff; }

        /* ── Título: entra desde la izquierda ── */
        .brand-name {
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.6px;
            color: #f4f4f5;
            text-align: center;
            margin-bottom: 6px;
            opacity: 0;
            animation: slideFromLeft 0.65s cubic-bezier(.22,.68,0,1.2) 0.3s forwards;
        }

        @keyframes slideFromLeft {
            from { opacity: 0; transform: translateX(-50px); }
            to   { opacity: 1; transform: translateX(0); }
        }

        /* ── Subtítulo: entra desde la derecha ── */
        .brand-sub {
            font-size: 12.5px;
            color: #52525b;
            text-align: center;
            margin-bottom: 28px;
            opacity: 0;
            animation: slideFromRight 0.65s cubic-bezier(.22,.68,0,1.2) 0.45s forwards;
        }

        @keyframes slideFromRight {
            from { opacity: 0; transform: translateX(50px); }
            to   { opacity: 1; transform: translateX(0); }
        }

        /* ── Card: sube desde abajo ── */
        .card {
            width: 100%;
            background: rgba(18,18,20,0.85);
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 20px;
            padding: 32px 28px;
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

        /* Vibración al haber error */
        @keyframes shake {
            0%,100% { transform: translateX(0); }
            15%     { transform: translateX(-8px); }
            30%     { transform: translateX(8px); }
            45%     { transform: translateX(-6px); }
            60%     { transform: translateX(6px); }
            75%     { transform: translateX(-3px); }
            90%     { transform: translateX(3px); }
        }

        .has-error .card {
            animation: slideUp 0s forwards, shake 0.45s cubic-bezier(.36,.07,.19,.97) both;
        }

        .card-title {
            font-size: 17px;
            font-weight: 700;
            color: #f4f4f5;
            margin-bottom: 3px;
            letter-spacing: -0.4px;
        }

        .card-desc {
            font-size: 12.5px;
            color: #52525b;
            margin-bottom: 22px;
        }

        .alert-status {
            background: rgba(99,102,241,0.1);
            border: 1px solid rgba(99,102,241,0.25);
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 13px;
            color: #a5b4fc;
            margin-bottom: 18px;
        }

        /* ── Campos ── */
        .field { margin-bottom: 15px; }

        .field label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #71717a;
            margin-bottom: 6px;
            transition: color 0.18s;
        }

        .field:focus-within label { color: #a5b4fc; }

        .field input {
            width: 100%;
            padding: 10px 14px;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.09);
            border-radius: 10px;
            color: #f4f4f5;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 14px;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
        }

        .field input:focus {
            border-color: rgba(99,102,241,0.55);
            box-shadow: 0 0 0 3px rgba(99,102,241,0.13);
            background: rgba(99,102,241,0.04);
        }

        .field input::placeholder { color: #3f3f46; }

        .field-error {
            font-size: 12px;
            color: #f87171;
            margin-top: 5px;
        }

        /* ── Recordarme ── */
        .row-options {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            margin-top: 4px;
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: 12.5px;
            color: #71717a;
            cursor: pointer;
        }

        .remember input[type="checkbox"] {
            width: 15px;
            height: 15px;
            accent-color: #6366f1;
            cursor: pointer;
        }

        /* ── Botón submit: entra desde la derecha ── */
        .btn-submit {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 12px 20px;
            border: none;
            border-radius: 11px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
            color: #fff;
            box-shadow: 0 4px 18px rgba(79,70,229,0.4), 0 1px 0 rgba(255,255,255,0.12) inset;
            transition: all 0.2s ease;
            opacity: 0;
            animation: slideFromRight 0.6s cubic-bezier(.22,.68,0,1.2) 0.8s forwards;
        }

        .btn-submit:hover {
            background: linear-gradient(135deg, #6366f1 0%, #60a5fa 100%);
            box-shadow: 0 8px 28px rgba(99,102,241,0.55), 0 1px 0 rgba(255,255,255,0.18) inset;
            transform: translateY(-2px);
        }

        .btn-submit:active { transform: translateY(0); }

        .btn-submit:disabled { opacity: 0.7; cursor: not-allowed; transform: none; }

        /* Spinner */
        .spinner {
            display: none;
            width: 15px; height: 15px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.65s linear infinite;
        }

        @keyframes spin { to { transform: rotate(360deg); } }

        .btn-submit.loading .spinner { display: block; }
        .btn-submit.loading .btn-icon,
        .btn-submit.loading .btn-text { opacity: 0.5; }

        /* ── Link de registro: entra desde la izquierda ── */
        .card-footer {
            margin-top: 18px;
            text-align: center;
            font-size: 12.5px;
            color: #52525b;
            opacity: 0;
            animation: slideFromLeft 0.6s cubic-bezier(.22,.68,0,1.2) 0.95s forwards;
        }

        .card-footer a {
            color: #6366f1;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.15s;
        }

        .card-footer a:hover { color: #818cf8; }

        /* ── Footer de página ── */
        .page-footer {
            margin-top: 22px;
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

        /* ════════════════════════════════════════════
           MONITO INTERACTIVO (robot)
           ════════════════════════════════════════════ */
        .mascot {
            position: relative;
            width: 132px;
            height: 124px;
            margin-bottom: 18px;
            opacity: 0;
            animation: slideDown 0.7s cubic-bezier(.22,.68,0,1.3) 0.1s forwards;
            z-index: 11;
        }

        /* Flota suavemente */
        .mascot-body {
            position: absolute;
            inset: 0;
            animation: mascotFloat 4s ease-in-out infinite;
            transform-origin: 50% 90%;
        }

        @keyframes mascotFloat {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50%      { transform: translateY(-6px) rotate(0.5deg); }
        }

        /* Antena */
        .mascot-antenna {
            position: absolute;
            top: -2px;
            left: 50%;
            width: 3px;
            height: 16px;
            background: rgba(255,255,255,0.25);
            transform: translateX(-50%);
            border-radius: 3px;
        }
        .mascot-antenna::before {
            content: '';
            position: absolute;
            top: -7px;
            left: 50%;
            width: 11px;
            height: 11px;
            border-radius: 50%;
            background: radial-gradient(circle at 35% 30%, #818cf8, #4f46e5);
            transform: translateX(-50%);
            box-shadow: 0 0 12px rgba(99,102,241,0.9);
            animation: antennaPulse 1.6s ease-in-out infinite;
        }
        @keyframes antennaPulse {
            0%, 100% { box-shadow: 0 0 6px rgba(99,102,241,0.6); }
            50%      { box-shadow: 0 0 18px rgba(99,102,241,1); }
        }

        /* Orejas / audífonos */
        .mascot-ear {
            position: absolute;
            top: 46px;
            width: 14px;
            height: 36px;
            border-radius: 8px;
            background: linear-gradient(145deg, #3730a3, #1e3a8a);
            z-index: 1;
        }
        .mascot-ear.left  { left: 4px; }
        .mascot-ear.right { right: 4px; }

        /* Cabeza */
        .mascot-head {
            position: absolute;
            top: 14px;
            left: 50%;
            transform: translateX(-50%);
            width: 110px;
            height: 96px;
            border-radius: 30px;
            background: linear-gradient(150deg, #4f46e5 0%, #3b82f6 100%);
            box-shadow:
                0 10px 30px rgba(79,70,229,0.45),
                inset 0 2px 4px rgba(255,255,255,0.25),
                inset 0 -6px 12px rgba(0,0,0,0.25);
            overflow: hidden;
            z-index: 2;
        }

        /* Visor / cara */
        .mascot-face {
            position: absolute;
            top: 22px;
            left: 50%;
            transform: translateX(-50%);
            width: 84px;
            height: 50px;
            border-radius: 22px;
            background: radial-gradient(circle at 50% 40%, #1e1b4b 0%, #0b1020 100%);
            box-shadow: inset 0 2px 6px rgba(0,0,0,0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
        }

        .mascot-eye {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: #f8fafc;
            position: relative;
            box-shadow: 0 0 10px rgba(165,180,252,0.7);
            transition: height 0.18s ease, transform 0.18s ease;
        }

        .mascot-pupil {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #1e293b;
            transform: translate(-50%, -50%);
            transition: transform 0.12s ease-out;
        }
        .mascot-pupil::after {
            content: '';
            position: absolute;
            top: 1.5px;
            right: 1.5px;
            width: 3.5px;
            height: 3.5px;
            border-radius: 50%;
            background: #fff;
        }

        /* Boca */
        .mascot-mouth {
            position: absolute;
            bottom: 14px;
            left: 50%;
            transform: translateX(-50%);
            width: 26px;
            height: 8px;
            border-radius: 0 0 14px 14px;
            background: #1e1b4b;
            box-shadow: inset 0 -2px 3px rgba(0,0,0,0.5);
            transition: all 0.2s ease;
            z-index: 3;
        }

        /* Mejillas (aparecen al celebrar) */
        .mascot-cheek {
            position: absolute;
            top: 52px;
            width: 12px;
            height: 8px;
            border-radius: 50%;
            background: rgba(244,114,182,0.0);
            transition: background 0.25s ease;
            z-index: 3;
        }
        .mascot-cheek.left  { left: 18px; }
        .mascot-cheek.right { right: 18px; }

        /* Manos */
        .mascot-hand {
            position: absolute;
            top: 56px;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(145deg, #4338ca, #2563eb);
            box-shadow: inset 0 2px 4px rgba(255,255,255,0.25), 0 4px 10px rgba(0,0,0,0.3);
            opacity: 0;
            z-index: 4;
            transition: transform 0.42s cubic-bezier(.34,1.56,.64,1), opacity 0.25s ease;
        }
        .mascot-hand.left  { left: 8px;  transform: translateY(40px) scale(0.5); }
        .mascot-hand.right { right: 8px; transform: translateY(40px) scale(0.5); }

        /* ── Estado: tapándose los ojos (contraseña) ── */
        .mascot.covering .mascot-hand.left  { opacity: 1; transform: translate(20px, -12px) scale(1); }
        .mascot.covering .mascot-hand.right { opacity: 1; transform: translate(-20px, -12px) scale(1); }
        .mascot.covering .mascot-eye        { height: 4px; }
        .mascot.covering .mascot-mouth      { width: 16px; height: 5px; }

        /* Espía un poquito cuando hay error en la contraseña */
        .mascot.peeking .mascot-hand.left  { transform: translate(20px, -4px) scale(1); }
        .mascot.peeking .mascot-hand.right { transform: translate(-20px, -4px) scale(1); }
        .mascot.peeking .mascot-eye        { height: 14px; }

        /* ── Estado: celebración / saludo ── */
        .mascot.celebrate .mascot-body { animation: mascotHop 0.6s ease 2; }
        .mascot.celebrate .mascot-mouth {
            height: 16px;
            width: 30px;
            border-radius: 0 0 16px 16px;
            background: #1e1b4b;
        }
        .mascot.celebrate .mascot-eye { height: 6px; }
        .mascot.celebrate .mascot-cheek { background: rgba(244,114,182,0.55); }
        .mascot.celebrate .mascot-hand.left {
            opacity: 1;
            transform: translate(-6px, -34px) rotate(-25deg) scale(1);
            animation: waveLeft 0.45s ease-in-out 3;
        }
        .mascot.celebrate .mascot-hand.right {
            opacity: 1;
            transform: translate(6px, -34px) rotate(25deg) scale(1);
            animation: waveRight 0.45s ease-in-out 3;
        }

        @keyframes mascotHop {
            0%, 100% { transform: translateY(0); }
            40%      { transform: translateY(-16px); }
            70%      { transform: translateY(0); }
        }
        @keyframes waveLeft {
            0%, 100% { transform: translate(-6px, -34px) rotate(-25deg) scale(1); }
            50%      { transform: translate(-10px, -38px) rotate(-45deg) scale(1.05); }
        }
        @keyframes waveRight {
            0%, 100% { transform: translate(6px, -34px) rotate(25deg) scale(1); }
            50%      { transform: translate(10px, -38px) rotate(45deg) scale(1.05); }
        }

        @media (prefers-reduced-motion: reduce) {
            .mascot { animation: none !important; opacity: 1 !important; transform: none !important; }
            .mascot-body, .mascot-antenna::before { animation: none !important; }
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

    <div class="wrapper {{ $errors->any() ? 'has-error' : '' }}">

        <!-- Monito interactivo (robot) -->
        <div class="mascot" id="mascot">
            <div class="mascot-body">
                <div class="mascot-antenna"></div>
                <div class="mascot-ear left"></div>
                <div class="mascot-ear right"></div>
                <div class="mascot-head">
                    <div class="mascot-face">
                        <div class="mascot-eye left"><div class="mascot-pupil"></div></div>
                        <div class="mascot-eye right"><div class="mascot-pupil"></div></div>
                    </div>
                    <div class="mascot-cheek left"></div>
                    <div class="mascot-cheek right"></div>
                    <div class="mascot-mouth"></div>
                </div>
                <div class="mascot-hand left"></div>
                <div class="mascot-hand right"></div>
            </div>
        </div>

        <!-- Título: desde la izquierda -->
        <span class="brand-name">Fundación Don Benjamín</span>

        <!-- Subtítulo: desde la derecha -->
        <span class="brand-sub">Sistema Administrativo Interno</span>

        <!-- Card: sube desde abajo -->
        <div class="card">
            <h1 class="card-title">Bienvenido de vuelta</h1>
            <p class="card-desc">Ingresa tus credenciales para continuar</p>

            @if (session('status'))
                <div class="alert-status">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('login') }}" id="login-form">
                @csrf

                <div class="field">
                    <label for="email">Correo electrónico</label>
                    <input id="email" type="email" name="email"
                           value="{{ old('email') }}"
                           placeholder="tu@correo.com"
                           required autofocus autocomplete="username">
                    @error('email')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="field">
                    <label for="password">Contraseña</label>
                    <input id="password" type="password" name="password"
                           placeholder="••••••••"
                           required autocomplete="current-password">
                    @error('password')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="row-options">
                    <label class="remember">
                        <input type="checkbox" name="remember" id="remember_me" {{ old('remember') ? 'checked' : '' }}>
                        Recordarme
                    </label>
                </div>

                <!-- Botón: entra desde la derecha -->
                <button type="submit" class="btn-submit" id="submit-btn">
                    <div class="spinner"></div>
                    <svg class="btn-icon" xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                        <polyline points="10 17 15 12 10 7"/>
                        <line x1="15" y1="12" x2="3" y2="12"/>
                    </svg>
                    <span class="btn-text">Iniciar sesión</span>
                </button>
            </form>

            @if (Route::has('register'))
                <!-- Link registro: entra desde la izquierda -->
                <div class="card-footer">
                    ¿No tienes cuenta? <a href="{{ route('register') }}">Crear cuenta</a>
                </div>
            @endif
        </div>

        <p class="page-footer">© {{ date('Y') }} Fundación Don Benjamín · Uso interno</p>
    </div>

    <script>
        // ════════════════════════════════════════════
        //  MONITO INTERACTIVO
        // ════════════════════════════════════════════
        (function () {
            const mascot   = document.getElementById('mascot');
            if (!mascot) return;

            const pupils   = mascot.querySelectorAll('.mascot-pupil');
            const emailEl  = document.getElementById('email');
            const passEl   = document.getElementById('password');

            // 'cursor' = sigue el mouse | 'input' = mira el campo | 'hidden' = tapado
            let mode = 'cursor';
            const MAX = 5; // desplazamiento máximo de las pupilas (px)

            function setPupils(dx, dy) {
                pupils.forEach(p => {
                    p.style.transform = `translate(calc(-50% + ${dx}px), calc(-50% + ${dy}px))`;
                });
            }

            function pointAt(x, y) {
                const r  = mascot.getBoundingClientRect();
                const cx = r.left + r.width / 2;
                const cy = r.top + r.height / 2.4;
                let dx = x - cx, dy = y - cy;
                const dist = Math.hypot(dx, dy) || 1;
                const k = Math.min(MAX, dist / 16) / dist;
                setPupils(dx * k, dy * k);
            }

            function lookAt(el) {
                const r = el.getBoundingClientRect();
                pointAt(r.left + r.width / 2, r.top + r.height / 2);
            }

            // 1) Los ojos siguen el cursor
            document.addEventListener('mousemove', e => {
                if (mode === 'cursor') pointAt(e.clientX, e.clientY);
            });

            // 2) Mira fijamente el campo de correo mientras escribes
            if (emailEl) {
                emailEl.addEventListener('focus', () => { mode = 'input'; lookAt(emailEl); });
                emailEl.addEventListener('input', () => { if (mode === 'input') lookAt(emailEl); });
                emailEl.addEventListener('blur',  () => { if (mode === 'input') mode = 'cursor'; });
            }

            // 3) Se tapa los ojos al escribir la contraseña
            if (passEl) {
                passEl.addEventListener('focus', () => { mode = 'hidden'; mascot.classList.add('covering'); });
                passEl.addEventListener('blur',  () => { mascot.classList.remove('covering', 'peeking'); mode = 'cursor'; });
            }

            // 4) Saludo / celebración al iniciar sesión
            window.mascotCelebrate = function () {
                mascot.classList.remove('covering', 'peeking');
                mode = 'celebrate';
                setPupils(0, 0);
                mascot.classList.add('celebrate');
            };

            // Si hubo error de credenciales, el monito "espía" entre los dedos
            @if ($errors->any())
                mascot.classList.add('covering', 'peeking');
                setTimeout(() => mascot.classList.remove('covering', 'peeking'), 1600);
            @endif
        })();

        // Spinner al enviar formulario
        document.getElementById('login-form').addEventListener('submit', function() {
            const btn = document.getElementById('submit-btn');
            btn.classList.add('loading');
            btn.disabled = true;
            if (typeof window.mascotCelebrate === 'function') window.mascotCelebrate();
        });

        // Generar partículas flotantes (igual que welcome)
        const container = document.getElementById('particles');
        const colors = [
            'rgba(99,102,241,',
            'rgba(59,130,246,',
            'rgba(139,92,246,',
        ];

        for (let i = 0; i < 22; i++) {
            const p = document.createElement('div');
            p.className = 'particle';
            const size  = Math.random() * 4 + 1.5;
            const color = colors[Math.floor(Math.random() * colors.length)];
            const opacity = (Math.random() * 0.4 + 0.15).toFixed(2);
            p.style.cssText = `
                width: ${size}px;
                height: ${size}px;
                left: ${Math.random() * 100}%;
                background: ${color}${opacity});
                animation-duration: ${Math.random() * 14 + 10}s;
                animation-delay: -${Math.random() * 14}s;
            `;
            container.appendChild(p);
        }
    </script>
</body>
</html>