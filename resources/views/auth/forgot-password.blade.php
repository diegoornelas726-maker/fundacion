<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Recuperar contraseña — {{ config('app.name') }}</title>

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
                radial-gradient(ellipse 70% 50% at 50% -5%, rgba(99,102,241,0.14) 0%, transparent 65%),
                radial-gradient(ellipse 40% 30% at 90% 100%, rgba(59,130,246,0.07) 0%, transparent 55%);
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

        .wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 400px;
            padding: 0 24px;
            animation: fadeUp 0.6s cubic-bezier(.22,.68,0,1.15) both;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Marca */
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

        /* Tarjeta */
        .card {
            background: rgba(18,18,20,0.88);
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 20px;
            padding: 32px 28px;
            backdrop-filter: blur(16px);
            box-shadow: 0 20px 60px rgba(0,0,0,0.55), 0 1px 0 rgba(255,255,255,0.05) inset;
        }

        /* Ícono decorativo dentro de la tarjeta */
        .card-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: rgba(99,102,241,0.12);
            border: 1px solid rgba(99,102,241,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
        }

        .card-icon svg { width: 20px; height: 20px; color: #818cf8; }

        .card-title {
            font-size: 18px;
            font-weight: 700;
            color: #f4f4f5;
            margin-bottom: 8px;
            letter-spacing: -0.4px;
        }

        .card-desc {
            font-size: 13px;
            color: #52525b;
            margin-bottom: 24px;
            line-height: 1.6;
        }

        /* Alerta de éxito */
        .alert-success {
            background: rgba(99,102,241,0.1);
            border: 1px solid rgba(99,102,241,0.25);
            border-radius: 10px;
            padding: 12px 14px;
            font-size: 13px;
            color: #a5b4fc;
            margin-bottom: 20px;
            line-height: 1.5;
        }

        /* Campo */
        .field { margin-bottom: 20px; }

        .field label {
            display: block;
            font-size: 12.5px;
            font-weight: 600;
            color: #71717a;
            margin-bottom: 6px;
        }

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
            transition: border-color 0.18s, box-shadow 0.18s;
        }

        .field input:focus {
            border-color: rgba(99,102,241,0.5);
            box-shadow: 0 0 0 3px rgba(99,102,241,0.12);
        }

        .field input::placeholder { color: #3f3f46; }

        .field-error {
            font-size: 12px;
            color: #f87171;
            margin-top: 5px;
        }

        /* Botón */
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
            transition: all 0.18s ease;
        }

        .btn-submit:hover {
            background: linear-gradient(135deg, #6366f1 0%, #60a5fa 100%);
            box-shadow: 0 1px 0 rgba(255,255,255,0.18) inset, 0 6px 28px rgba(99,102,241,0.5);
            transform: translateY(-1px);
        }

        .btn-submit:active { transform: translateY(0); }

        /* Link volver */
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
            display: inline-flex;
            align-items: center;
            gap: 5px;
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
<div class="wrapper">

    <!-- Marca -->
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

        <!-- Ícono de llave -->
        <div class="card-icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 0 1 21.75 8.25Z"/>
            </svg>
        </div>

        <h1 class="card-title">¿Olvidaste tu contraseña?</h1>
        <p class="card-desc">
            Sin problema. Escribe tu correo y te enviaremos un enlace para restablecerla.
        </p>

        <!-- Mensaje de éxito -->
        @if (session('status'))
            <div class="alert-success">
                ✓ {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
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

            <button type="submit" class="btn-submit">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/>
                </svg>
                Enviar enlace de recuperación
            </button>
        </form>

        <div class="card-footer">
            <a href="{{ route('login') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
                </svg>
                Volver al inicio de sesión
            </a>
        </div>
    </div>

    <p class="page-footer">© {{ date('Y') }} Fundación Don Benjamín · Uso interno</p>
</div>
</body>
</html>