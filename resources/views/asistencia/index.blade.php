<x-app-layout>
    <x-slot name="header">
        <h1>Asistencia</h1>
    </x-slot>

    <style>
        .asis-toolbar {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 20px;
        }

        .asis-field { display: flex; flex-direction: column; gap: 6px; }
        .asis-field label {
            font-size: 11.5px; font-weight: 600;
            color: #71717a; text-transform: uppercase; letter-spacing: 0.6px;
        }

        .asis-input, .asis-date {
            padding: 9px 14px;
            background: rgba(255,255,255,0.05);
            border: var(--glass-border, 1px solid rgba(255,255,255,0.14));
            border-radius: var(--glass-radius-sm, 14px);
            backdrop-filter: blur(10px);
            color: #f4f4f5;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 13.5px;
            outline: none;
            transition: border-color 0.18s;
        }
        .asis-input { min-width: 240px; }
        .asis-input::placeholder { color: #3f3f46; }
        .asis-input:focus, .asis-date:focus { border-color: rgba(99,102,241,0.5); }

        .asis-actions { display: flex; gap: 8px; flex-wrap: wrap; align-items: flex-end; }

        .btn {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 9px 16px; border-radius: var(--glass-radius-sm, 14px);
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 13.5px; font-weight: 600;
            cursor: pointer; text-decoration: none; border: none;
            transition: all 0.18s;
        }
        .btn svg { width: 15px; height: 15px; }
        .btn-primary {
            background: linear-gradient(135deg, #4f46e5, #3b82f6);
            color: #fff;
            box-shadow: 0 4px 14px rgba(79,70,229,0.3), inset 0 1px 0 rgba(255,255,255,0.25);
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #6366f1, #60a5fa);
            transform: translateY(-1px);
        }
        .btn-ghost {
            background: rgba(255,255,255,0.06);
            border: var(--glass-border, 1px solid rgba(255,255,255,0.14));
            backdrop-filter: blur(10px);
            color: #d4d4d8;
        }
        .btn-ghost:hover { background: rgba(255,255,255,0.11); color: #e4e4e7; }
        .btn-amber {
            background: rgba(245,158,11,0.13);
            border: 1px solid rgba(245,158,11,0.28);
            backdrop-filter: blur(10px);
            color: #fbbf24;
        }
        .btn-amber:hover { background: rgba(245,158,11,0.2); }

        /* Resumen */
        .asis-summary {
            display: flex; gap: 18px; flex-wrap: wrap;
            margin-bottom: 18px;
        }
        .summary-pill {
            position: relative;
            background: rgba(255,255,255,0.06);
            border: var(--glass-border, 1px solid rgba(255,255,255,0.14));
            border-radius: var(--glass-radius, 22px);
            padding: 12px 20px;
            display: flex; align-items: center; gap: 10px;
            backdrop-filter: blur(var(--glass-blur, 30px)) saturate(180%);
            -webkit-backdrop-filter: blur(var(--glass-blur, 30px)) saturate(180%);
            box-shadow: var(--glass-shadow, 0 4px 20px rgba(0,0,0,0.15));
            overflow: hidden;
        }
        .summary-pill::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: inherit;
            background: var(--glass-sheen, linear-gradient(128deg, rgba(255,255,255,0.18), rgba(255,255,255,0) 36%));
            opacity: 0.7;
            pointer-events: none;
        }
        .summary-pill > * { position: relative; z-index: 1; }
        .summary-pill .dot { width: 9px; height: 9px; border-radius: 50%; }
        .summary-pill .dot.green { background: #4ade80; }
        .summary-pill .num { font-size: 18px; font-weight: 800; color: #f4f4f5; }
        .summary-pill .lbl { font-size: 12.5px; color: #71717a; }

        /* Tarjeta tabla */
        .table-card {
            position: relative;
            background: rgba(255,255,255,0.05);
            border: var(--glass-border, 1px solid rgba(255,255,255,0.14));
            border-radius: var(--glass-radius-lg, 28px);
            overflow: hidden;
            backdrop-filter: blur(var(--glass-blur, 30px)) saturate(180%);
            -webkit-backdrop-filter: blur(var(--glass-blur, 30px)) saturate(180%);
            box-shadow: var(--glass-shadow, 0 8px 32px rgba(0,0,0,0.3));
            margin-bottom: 22px;
        }
        .table-card::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: inherit;
            background: var(--glass-sheen, linear-gradient(128deg, rgba(255,255,255,0.18), rgba(255,255,255,0) 36%));
            opacity: 0.7;
            pointer-events: none;
            z-index: 0;
        }
        .table-card > * { position: relative; z-index: 1; }
        .table-head {
            display: flex; align-items: center; justify-content: space-between;
            padding: 16px 20px; flex-wrap: wrap; gap: 10px;
            border-bottom: 1px solid rgba(255,255,255,0.06);
        }
        .table-head h3 { font-size: 14.5px; font-weight: 700; color: #f4f4f5; }
        .bulk-actions { display: flex; gap: 8px; }
        .bulk-btn {
            font-size: 12px; font-weight: 600; padding: 6px 12px;
            border-radius: var(--glass-radius-sm, 10px); cursor: pointer;
            background: rgba(99,102,241,0.1);
            border: 1px solid rgba(99,102,241,0.22);
            backdrop-filter: blur(6px);
            color: #a5b4fc; font-family: 'Plus Jakarta Sans', sans-serif;
            transition: all 0.15s;
        }
        .bulk-btn:hover { background: rgba(99,102,241,0.18); }

        table { width: 100%; border-collapse: collapse; }
        thead th {
            text-align: left; padding: 13px 20px;
            font-size: 11.5px; font-weight: 600; text-transform: uppercase;
            letter-spacing: 0.8px; color: #52525b;
            border-bottom: 1px solid rgba(255,255,255,0.06);
        }
        tbody td { padding: 13px 20px; font-size: 13.5px; color: #a1a1aa; border-bottom: 1px solid rgba(255,255,255,0.04); }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr { transition: background 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease; }
        tbody tr:hover {
            background: rgba(255,255,255,0.04);
            transform: translateX(3px);
            box-shadow: -3px 0 0 #6366f1;
        }
        .td-name { color: #e4e4e7; font-weight: 600; }
        .th-right, .td-right { text-align: right; }

        /* Switch presente/ausente */
        .pa-toggle {
            display: inline-flex; border-radius: var(--glass-radius-sm, 12px); overflow: hidden;
            border: var(--glass-border, 1px solid rgba(255,255,255,0.14));
            backdrop-filter: blur(8px);
        }
        .pa-toggle input { display: none; }
        .pa-toggle label {
            padding: 6px 14px; font-size: 12.5px; font-weight: 600;
            cursor: pointer; color: #71717a; transition: all 0.15s; user-select: none;
        }
        .pa-toggle label.pres { border-right: 1px solid rgba(255,255,255,0.09); }
        .pa-toggle input.in-pres:checked + label.pres { background: rgba(34,197,94,0.15); color: #4ade80; }
        .pa-toggle input.in-aus:checked + label.aus { background: rgba(239,68,68,0.13); color: #f87171; }

        .empty-state { text-align: center; padding: 50px 20px; color: #52525b; font-size: 14px; }

        /* Visitantes */
        .visit-form { display: flex; gap: 8px; flex-wrap: wrap; }
        .chip-tag {
            display: inline-flex; align-items: center; gap: 7px;
            background: rgba(245,158,11,0.1);
            border: 1px solid rgba(245,158,11,0.25);
            backdrop-filter: blur(6px);
            color: #fcd34d; padding: 6px 10px 6px 12px; border-radius: 999px;
            font-size: 13px; font-weight: 600;
        }
        .chip-tag button {
            background: none; border: none; color: #fcd34d; cursor: pointer;
            font-size: 15px; line-height: 1; padding: 0 2px; opacity: 0.7;
        }
        .chip-tag button:hover { opacity: 1; }
        .visit-list { display: flex; flex-wrap: wrap; gap: 8px; padding: 16px 20px; }

        .save-bar { display: flex; justify-content: flex-end; gap: 10px; }

        /* Light theme */
        [data-theme="light"] .summary-pill,
        [data-theme="light"] .table-card { background: rgba(255,255,255,0.55); }
        [data-theme="light"] .table-head { border-bottom-color: rgba(0,0,0,0.07); }
        [data-theme="light"] .table-head h3,
        [data-theme="light"] .td-name,
        [data-theme="light"] .summary-pill .num { color: #18181b; }
        [data-theme="light"] thead th { color: #71717a; border-bottom-color: rgba(0,0,0,0.07); }
        [data-theme="light"] tbody td { color: #3f3f46; border-bottom-color: rgba(0,0,0,0.05); }
        [data-theme="light"] tbody tr:hover { background: rgba(0,0,0,0.02); }
        [data-theme="light"] .asis-input,
        [data-theme="light"] .asis-date { background: rgba(255,255,255,0.5); border-color: rgba(255,255,255,0.8); color: #18181b; }
        [data-theme="light"] .pa-toggle { border-color: rgba(0,0,0,0.12); }
        [data-theme="light"] .pa-toggle label.pres { border-right-color: rgba(0,0,0,0.12); }
        [data-theme="light"] .btn-ghost { background: rgba(255,255,255,0.5); border-color: rgba(255,255,255,0.8); color: #3f3f46; }
        [data-theme="light"] .btn-ghost:hover { background: rgba(255,255,255,0.75); }
    </style>

    <!-- Barra: fecha + buscador + acciones -->
    <div class="asis-toolbar">
        <form method="GET" action="{{ route('asistencia.index') }}" style="display:flex; gap:12px; flex-wrap:wrap; align-items:flex-end;">
            <div class="asis-field">
                <label for="fecha">Fecha</label>
                <input class="asis-date" type="date" id="fecha" name="fecha"
                       value="{{ $fecha->format('Y-m-d') }}" onchange="this.form.submit()">
            </div>
            <div class="asis-field">
                <label for="buscar">Buscar persona</label>
                <input class="asis-input" type="text" id="buscar" name="buscar"
                       value="{{ $buscar }}" placeholder="Nombre o apellido…">
            </div>
            <button type="submit" class="btn btn-ghost">Buscar</button>
        </form>

        <div class="asis-actions">
            <a href="{{ route('asistencia.historial') }}" class="btn btn-ghost">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                </svg>
                Historial
            </a>
            <a href="{{ route('asistencia.personas') }}" class="btn btn-ghost">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/>
                </svg>
                Por persona
            </a>
        </div>
    </div>

    <!-- Resumen -->
    <div class="asis-summary">
        <div class="summary-pill">
            <span class="dot green"></span>
            <span class="num" id="count-present">{{ $presentesCount }}</span>
            <span class="lbl">presentes el {{ $fecha->locale('es')->isoFormat('D [de] MMMM, YYYY') }}</span>
        </div>
    </div>

    <!-- Lista de beneficiarios -->
    <form method="POST" action="{{ route('asistencia.store') }}">
        @csrf
        <input type="hidden" name="fecha" value="{{ $fecha->format('Y-m-d') }}">

        <div class="table-card">
            <div class="table-head">
                <h3>Beneficiarios</h3>
                <div class="bulk-actions">
                    <button type="button" class="bulk-btn" onclick="markAll(true)">Marcar todos presentes</button>
                    <button type="button" class="bulk-btn" onclick="markAll(false)">Marcar todos ausentes</button>
                </div>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nombre completo</th>
                            <th class="th-right">Asistencia</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($beneficiarios as $b)
                            @php $reg = $registros->get($b->id); $isPres = $reg ? $reg->presente : false; @endphp
                            <tr>
                                <td>{{ $b->id }}</td>
                                <td class="td-name">{{ $b->nombre_completo }}</td>
                                <td class="td-right">
                                    <span class="pa-toggle">
                                        <input class="in-pres" type="radio" id="p{{ $b->id }}" name="estado[{{ $b->id }}]" value="1" {{ $isPres ? 'checked' : '' }}>
                                        <label class="pres" for="p{{ $b->id }}">Presente</label>
                                        <input class="in-aus" type="radio" id="a{{ $b->id }}" name="estado[{{ $b->id }}]" value="0" {{ $isPres ? '' : 'checked' }}>
                                        <label class="aus" for="a{{ $b->id }}">Ausente</label>
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3"><div class="empty-state">No hay beneficiarios{{ $buscar ? ' que coincidan con la búsqueda' : '' }}.</div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="save-bar">
            <button type="submit" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.9">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                </svg>
                Guardar asistencia
            </button>
        </div>
    </form>

    <!-- Visitantes -->
    <div class="table-card" style="margin-top:22px;">
        <div class="table-head">
            <h3>Visitantes del día</h3>
            <form class="visit-form" method="POST" action="{{ route('asistencia.visitante.store') }}">
                @csrf
                <input type="hidden" name="fecha" value="{{ $fecha->format('Y-m-d') }}">
                <input class="asis-input" type="text" name="nombre_visitante" placeholder="Nombre del visitante…" required>
                <button type="submit" class="btn btn-ghost">+ Agregar visitante</button>
            </form>
        </div>

        @if ($visitantes->isEmpty())
            <div class="empty-state">Aún no hay visitantes registrados este día.</div>
        @else
            <div class="visit-list">
                @foreach ($visitantes as $v)
                    <span class="chip-tag">
                        {{ $v->nombre_visitante }}
                        <form method="POST" action="{{ route('asistencia.visitante.destroy', $v) }}" onsubmit="return confirm('¿Eliminar a {{ $v->nombre_visitante }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" title="Eliminar">&times;</button>
                        </form>
                    </span>
                @endforeach
            </div>
        @endif
    </div>

    <script>
        function markAll(present) {
            document.querySelectorAll('tbody tr').forEach(function (row) {
                const p = row.querySelector('input.in-pres');
                const a = row.querySelector('input.in-aus');
                if (!p || !a) return;
                p.checked = present;
                a.checked = !present;
            });
            updateCount();
        }

        function updateCount() {
            const presentes = document.querySelectorAll('input.in-pres:checked').length;
            const visitantes = {{ $visitantes->where('presente', true)->count() }};
            const el = document.getElementById('count-present');
            if (el) el.textContent = presentes + visitantes;
        }

        document.querySelectorAll('input.in-pres, input.in-aus').forEach(function (el) {
            el.addEventListener('change', updateCount);
        });
    </script>
</x-app-layout>