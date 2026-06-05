<x-app-layout>
    <x-slot name="header">
        <h1>Dashboard</h1>
    </x-slot>

    <style>
        .welcome-card {
            background: rgba(18,18,20,0.8);
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 16px;
            padding: 28px 32px;
            display: flex;
            align-items: center;
            gap: 16px;
            backdrop-filter: blur(12px);
            box-shadow: 0 8px 32px rgba(0,0,0,0.3);
        }

        .welcome-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: rgba(99,102,241,0.12);
            border: 1px solid rgba(99,102,241,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .welcome-icon svg { width: 22px; height: 22px; color: #818cf8; }

        .welcome-text h2 {
            font-size: 16px;
            font-weight: 700;
            color: #f4f4f5;
            margin-bottom: 4px;
            letter-spacing: -0.3px;
        }

        .welcome-text p {
            font-size: 13.5px;
            color: #52525b;
            line-height: 1.5;
        }

        .welcome-text span {
            color: #818cf8;
            font-weight: 600;
        }
    </style>

    <div class="welcome-card">
        <div class="welcome-icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
            </svg>
        </div>
        <div class="welcome-text">
            <h2>¡Bienvenido, {{ Auth::user()->name }}!</h2>
            <p>Has iniciado sesión correctamente en el <span>Sistema Administrativo Interno</span> de la Fundación Don Benjamín.</p>
        </div>
    </div>
</x-app-layout>