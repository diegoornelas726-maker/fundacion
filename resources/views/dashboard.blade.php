<x-app-layout>
    <x-slot name="header">
        <h1>Dashboard</h1>
    </x-slot>

    <style>
        /* ── Bienvenida ── */
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
            animation: fadeSlideUp 0.5s ease both;
        }

        .welcome-icon {
            width: 48px; height: 48px;
            border-radius: 12px;
            background: rgba(99,102,241,0.12);
            border: 1px solid rgba(99,102,241,0.2);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }

        .welcome-icon svg { width: 22px; height: 22px; color: #818cf8; }

        .welcome-text h2 {
            font-size: 16px; font-weight: 700;
            color: #f4f4f5; margin-bottom: 4px; letter-spacing: -0.3px;
        }

        .welcome-text p { font-size: 13.5px; color: #52525b; line-height: 1.5; }
        .welcome-text span { color: #818cf8; font-weight: 600; }

        /* ── Grid de tarjetas ── */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-top: 20px;
        }

        @media (max-width: 768px) {
            .stats-grid { grid-template-columns: 1fr; }
        }

        /* ── Tarjeta de estadística ── */
        .stat-card {
            background: rgba(18,18,20,0.8);
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 16px;
            padding: 24px 26px;
            backdrop-filter: blur(12px);
            box-shadow: 0 8px 32px rgba(0,0,0,0.3);
            transition: border-color 0.2s, transform 0.2s, box-shadow 0.2s;
            opacity: 0;
            animation: fadeSlideUp 0.5s ease forwards;
            cursor: default;
        }

        .stats-grid { perspective: 1000px; }

        .stat-card {
            position: relative;
            transform-style: preserve-3d;
            will-change: transform;
        }

        /* Brillo que sigue al cursor */
        .stat-card::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 16px;
            pointer-events: none;
            background: radial-gradient(360px circle at var(--mx, 50%) var(--my, 50%),
                        rgba(99,102,241,0.16), transparent 45%);
            opacity: 0;
            transition: opacity 0.25s ease;
            z-index: 0;
        }

        .stat-card:hover::before { opacity: 1; }
        .stat-card > * { position: relative; z-index: 1; }

        .stat-card:hover {
            border-color: rgba(99,102,241,0.25);
            transform: translateY(-3px);
            box-shadow: 0 14px 40px rgba(0,0,0,0.4), 0 0 0 1px rgba(99,102,241,0.1);
        }

        .stat-card:nth-child(1) { animation-delay: 0.1s; }
        .stat-card:nth-child(2) { animation-delay: 0.2s; }
        .stat-card:nth-child(3) { animation-delay: 0.3s; }

        @keyframes fadeSlideUp {
            from { opacity: 0; transform: translateY(18px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .stat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .stat-label {
            font-size: 12.5px;
            font-weight: 600;
            color: #52525b;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        .stat-icon {
            width: 36px; height: 36px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
        }

        .stat-icon svg { width: 18px; height: 18px; }

        .stat-icon.purple {
            background: rgba(99,102,241,0.12);
            border: 1px solid rgba(99,102,241,0.2);
            color: #818cf8;
        }

        .stat-icon.blue {
            background: rgba(59,130,246,0.12);
            border: 1px solid rgba(59,130,246,0.2);
            color: #60a5fa;
        }

        .stat-icon.green {
            background: rgba(34,197,94,0.10);
            border: 1px solid rgba(34,197,94,0.18);
            color: #4ade80;
        }

        .stat-number {
            font-size: 36px;
            font-weight: 800;
            color: #f4f4f5;
            letter-spacing: -1.5px;
            line-height: 1;
            margin-bottom: 6px;
        }

        .stat-desc {
            font-size: 12.5px;
            color: #3f3f46;
        }

        /* Barra de progreso decorativa */
        .stat-bar {
            margin-top: 16px;
            height: 3px;
            background: rgba(255,255,255,0.05);
            border-radius: 2px;
            overflow: hidden;
        }

        .stat-bar-fill {
            height: 100%;
            border-radius: 2px;
            width: 0%;
            transition: width 1.2s cubic-bezier(.22,.68,0,1.1);
        }

        .stat-bar-fill.purple { background: linear-gradient(90deg, #4f46e5, #818cf8); }
        .stat-bar-fill.blue   { background: linear-gradient(90deg, #3b82f6, #60a5fa); }
        .stat-bar-fill.green  { background: linear-gradient(90deg, #16a34a, #4ade80); }
    </style>

    <!-- Bienvenida -->
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

    <!-- Tarjetas de estadísticas -->
    <div class="stats-grid">

        <!-- Beneficiarios -->
        <a href="{{ route('beneficiarios.index') }}" class="stat-card" style="text-decoration:none;">
            <div class="stat-header">
                <span class="stat-label">Beneficiarios</span>
                <div class="stat-icon purple">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/>
                    </svg>
                </div>
            </div>
            <div class="stat-number" data-target="{{ $totalBeneficiarios }}">0</div>
            <div class="stat-desc">registrados en total</div>
            <div class="stat-bar">
                <div class="stat-bar-fill purple" data-width="75"></div>
            </div>
        </a>

        <!-- Apoyos -->
        <a href="{{ route('apoyos.index') }}" class="stat-card" style="text-decoration:none;">
            <div class="stat-header">
                <span class="stat-label">Apoyos</span>
                <div class="stat-icon blue">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 11.25v8.25a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5v-8.25M12 4.875A2.625 2.625 0 1 0 9.375 7.5H12m0-2.625V7.5m0-2.625A2.625 2.625 0 1 1 14.625 7.5H12m0 0V21m-8.625-9.75h18c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125h-18c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z"/>
                    </svg>
                </div>
            </div>
            <div class="stat-number" data-target="{{ $totalApoyos }}">0</div>
            <div class="stat-desc">entregados en total</div>
            <div class="stat-bar">
                <div class="stat-bar-fill blue" data-width="60"></div>
            </div>
        </a>

        <!-- Actividades -->
        <a href="{{ route('actividades.index') }}" class="stat-card" style="text-decoration:none;">
            <div class="stat-header">
                <span class="stat-label">Actividades</span>
                <div class="stat-icon green">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/>
                    </svg>
                </div>
            </div>
            <div class="stat-number" data-target="{{ $totalActividades }}">0</div>
            <div class="stat-desc">actividades registradas</div>
            <div class="stat-bar">
                <div class="stat-bar-fill green" data-width="45"></div>
            </div>
        </a>

    </div>

    <script>
        // Contador animado
        document.querySelectorAll('.stat-number').forEach(function(el) {
            const target = parseInt(el.getAttribute('data-target')) || 0;
            if (target === 0) { el.textContent = '0'; return; }

            const duration = 1000;
            const step     = Math.ceil(target / (duration / 16));
            let current    = 0;

            const timer = setInterval(function() {
                current += step;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                el.textContent = current.toLocaleString('es-MX');
            }, 16);
        });

        // Barras de progreso
        setTimeout(function() {
            document.querySelectorAll('.stat-bar-fill').forEach(function(bar) {
                bar.style.width = bar.getAttribute('data-width') + '%';
            });
        }, 300);

        // Tilt 3D + brillo que sigue al cursor
        const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        if (!reduceMotion) {
            document.querySelectorAll('.stat-card').forEach(function(card) {
                card.addEventListener('mousemove', function(e) {
                    const r  = card.getBoundingClientRect();
                    const px = (e.clientX - r.left) / r.width;
                    const py = (e.clientY - r.top)  / r.height;
                    card.style.setProperty('--mx', (px * 100) + '%');
                    card.style.setProperty('--my', (py * 100) + '%');
                    card.style.transition = 'transform 0.05s linear';
                    card.style.transform =
                        `perspective(900px) rotateX(${(0.5 - py) * 9}deg) rotateY(${(px - 0.5) * 9}deg) translateY(-4px)`;
                });
                card.addEventListener('mouseleave', function() {
                    card.style.transition = 'transform 0.4s cubic-bezier(.22,.68,0,1.2)';
                    card.style.transform  = '';
                });
            });
        }
    </script>
</x-app-layout>