<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar sesión — {{ config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet"/>

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
            align-items: center;
            justify-content: center;
            -webkit-font-smoothing: antialiased;
            position: relative;
            overflow: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background:
                radial-gradient(ellipse 70% 50% at 50% -5%, rgba(99,102,241,0.18) 0%, transparent 65%),
                radial-gradient(ellipse 40% 30% at 90% 100%, rgba(59,130,246,0.09) 0%, transparent 55%);
            pointer-events: none;
        }

        body::after {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.018) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.018) 1px, transparent 1px);
            background-size: 52px 52px;
            pointer-events: none;
        }

        /* ── Partículas flotantes ── */
        .particles {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
        }

        .particle {
            position: absolute;
            border-radius: 50%;
            background: rgba(99,102,241,0.6);
            animation: floatParticle linear infinite;
        }

        @keyframes floatParticle {
            0%   { transform: translateY(100vh) scale(0); opacity: 0; }
            10%  { opacity: 1; }
            90%  { opacity: 0.6; }
            100% { transform: translateY(-10vh) scale(1); opacity: 0; }
        }

        /* ── Wrapper ── */
        .wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 400px;
            padding: 0 24px;
            animation: fadeUp 0.7s cubic-bezier(.22,.68,0,1.15) both;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(28px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        @keyframes shake {
            0%,100% { transform: translateX(0); }
            15%     { transform: translateX(-8px); }
            30%     { transform: translateX(8px); }
            45%     { transform: translateX(-6px); }
            60%     { transform: translateX(6px); }
            75%     { transform: translateX(-3px); }
            90%     { transform: translateX(3px); }
        }

        .wrapper.has-error .card {
            animation: shake 0.45s cubic-bezier(.36,.07,.19,.97) both;
        }

        /* ── Brand ── */
        .brand {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 28px;
        }

        .badge {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            background: linear-gradient(145deg, #4f46e5 0%, #3b82f6 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 14px;
            box-shadow: 0 0 0 1px rgba(99,102,241,0.35), 0 6px 24px rgba(79,70,229,0.28);
            animation: badgePulse 3s ease-in-out infinite;
        }

        @keyframes badgePulse {
            0%, 100% { box-shadow: 0 0 0 1px rgba(99,102,241,0.35), 0 6px 24px rgba(79,70,229,0.28); }
            50%       { box-shadow: 0 0 0 6px rgba(99,102,241,0.12), 0 6px 32px rgba(79,70,229,0.45); }
        }

        .badge svg { width: 24px; height: 24px; color: #fff; }

        .brand-name {
            font-size: 15px;
            font-weight: 700;
            color: #e4e4e7;
            letter-spacing: -0.3px;
        }

        .brand-sub {
            font-size: 11.5px;
            color: #52525b;
            margin-top: 3px;
        }

        /* ── Card ── */
        .card {
            background: rgba(18,18,20,0.88);
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 20px;
            padding: 32px 28px;
            backdrop-filter: blur(16px);
            box-shadow: 0 20px 60px rgba(0,0,0,0.55), 0 1px 0 rgba(255,255,255,0.05) inset;
            transition: box-shadow 0.3s;
        }

        .card:hover {
            box-shadow: 0 24px 70px rgba(0,0,0,0.65), 0 1px 0 rgba(255,255,255,0.07) inset;
        }

        .card-title {
            font-size: 18px;
            font-weight: 700;
            color: #f4f4f5;
            margin-bottom: 4px;
            letter-spacing: -0.4px;
        }

        .card-desc {
            font-size: 13px;
            color: #52525b;
            margin-bottom: 24px;
        }

        .alert-status {
            background: rgba(99,102,241,0.1);
            border: 1px solid rgba(99,102,241,0.25);
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 13px;
            color: #a5b4fc;
            margin-bottom: 18px;
            animation: fadeUp 0.4s ease both;
        }

        /* ── Fields ── */
        .field { margin-bottom: 16px; position: relative; }

        .field label {
            display: block;
            font-size: 12.5px;
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
            animation: fadeUp 0.3s ease both;
        }

        /* ── Options row ── */
        .row-options {
            display: flex;
            align-items: center;
            margin-bottom: 22px;
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

        /* ── Submit button ── */
        .btn-submit {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 11.5px 20px;
            border: none;
            border-radius: 11px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
            color: #fff;
            box-shadow: 0 1px 0 rgba(255,255,255,0.12) inset, 0 4px 18px rgba(79,70,229,0.35);
            transition: all 0.2s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-submit::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.08), transparent);
            opacity: 0;
            transition: opacity 0.2s;
        }

        .btn-submit:hover::after { opacity: 1; }

        .btn-submit:hover {
            box-shadow: 0 1px 0 rgba(255,255,255,0.18) inset, 0 8px 30px rgba(99,102,241,0.55);
            transform: translateY(-1px);
        }

        .btn-submit:active { transform: translateY(0); }

        .btn-submit:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        /* Spinner de carga */
        .spinner {
            display: none;
            width: 15px;
            height: 15px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.65s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .btn-submit.loading .spinner { display: block; }
        .btn-submit.loading .btn-icon,
        .btn-submit.loading .btn-text { opacity: 0.5; }

        /* ── Footer ── */
        .card-footer {
            margin-top: 20px;
            text-align: center;
            font-size: 12.5px;
            color: #52525b;
        }

        .card-footer a {
            color: #6366f1;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.15s;
        }

        .card-footer a:hover { color: #818cf8; }

        .page-footer {
            margin-top: 24px;
            text-align: center;
            font-size: 11.5px;
            color: #27272a;
        }
    </style>
</head>
<body>

<!-- Partículas de fondo -->
<div class="particles" id="particles"></div>

<div class="wrapper {{ $errors->any() ? 'has-error' : '' }}">

    <div class="brand">
        <div class="badge">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 21V9l9-6 9 6v12M9 21V12h6v9"/>
            </svg>
        </div>
        <span class="brand-name">Fundación Don Benjamín</span>
        <span class="brand-sub">Sistema Administrativo Interno</span>
    </div>

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
            <div class="card-footer">
                ¿No tienes cuenta? <a href="{{ route('register') }}">Crear cuenta</a>
            </div>
        @endif
    </div>

    <p class="page-footer">© {{ date('Y') }} Fundación Don Benjamín · Uso interno</p>
</div>

<script>
    // Spinner al hacer submit
    document.getElementById('login-form').addEventListener('submit', function() {
        const btn = document.getElementById('submit-btn');
        btn.classList.add('loading');
        btn.disabled = true;
    });

    // Generar partículas flotantes
    const container = document.getElementById('particles');
    const count = 18;

    for (let i = 0; i < count; i++) {
        const p = document.createElement('div');
        p.className = 'particle';
        const size = Math.random() * 3 + 1.5;
        p.style.cssText = `
            width: ${size}px;
            height: ${size}px;
            left: ${Math.random() * 100}%;
            opacity: ${Math.random() * 0.5 + 0.1};
            animation-duration: ${Math.random() * 12 + 10}s;
            animation-delay: ${Math.random() * 10}s;
        `;
        container.appendChild(p);
    }
</script>
</body>
</html>