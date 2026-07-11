<x-app-layout>
    <x-slot name="header">
        <h1>Actividades</h1>
    </x-slot>

    <style>
        .toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 20px;
        }

        .search-group { display: flex; gap: 8px; flex-wrap: wrap; align-items: center; }

        .search-input, .filter-select, .period-input {
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

        .search-input { min-width: 220px; }
        .search-input::placeholder { color: #3f3f46; }
        .search-input:focus, .filter-select:focus, .period-input:focus { border-color: rgba(99,102,241,0.5); }
        .filter-select option { background: #18181a; }

        .period-input::-webkit-calendar-picker-indicator {
            filter: invert(1);
            cursor: pointer;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 18px;
            border-radius: var(--glass-radius-sm, 14px);
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 13.5px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            border: none;
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

        .btn-search {
            background: rgba(255,255,255,0.07);
            border: var(--glass-border, 1px solid rgba(255,255,255,0.14));
            backdrop-filter: blur(10px);
            color: #a1a1aa;
        }
        .btn-search:hover { background: rgba(255,255,255,0.12); color: #e4e4e7; }

        .btn-export {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 15px;
            border-radius: var(--glass-radius-sm, 14px);
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 13.5px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.18s;
            border: none;
            backdrop-filter: blur(10px);
        }
        .btn-export svg { width: 15px; height: 15px; }
        .btn-export.pdf { background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.22); color: #f87171; }
        .btn-export.pdf:hover { background: rgba(239,68,68,0.18); }
        .btn-export.excel { background: rgba(34,197,94,0.1); border: 1px solid rgba(34,197,94,0.22); color: #86efac; }
        .btn-export.excel:hover { background: rgba(34,197,94,0.18); }

        .alert {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 16px;
            border-radius: var(--glass-radius-sm, 14px);
            font-size: 13.5px;
            margin-bottom: 20px;
            backdrop-filter: blur(10px);
        }
        .alert-success {
            background: rgba(34,197,94,0.1);
            border: 1px solid rgba(34,197,94,0.22);
            color: #86efac;
        }

        .table-card {
            position: relative;
            background: rgba(255,255,255,0.05);
            border: var(--glass-border, 1px solid rgba(255,255,255,0.14));
            border-radius: var(--glass-radius-lg, 28px);
            overflow: hidden;
            backdrop-filter: blur(var(--glass-blur, 30px)) saturate(180%);
            -webkit-backdrop-filter: blur(var(--glass-blur, 30px)) saturate(180%);
            box-shadow: var(--glass-shadow, 0 8px 32px rgba(0,0,0,0.3));
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
        .table-wrap { overflow-x: auto; }

        table { width: 100%; border-collapse: collapse; }
        thead tr { border-bottom: 1px solid rgba(255,255,255,0.07); }
        thead th {
            padding: 13px 16px;
            text-align: left;
            font-size: 11.5px;
            font-weight: 600;
            color: #52525b;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            white-space: nowrap;
        }

        tbody tr {
            border-bottom: 1px solid rgba(255,255,255,0.04);
            transition: background 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
        }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover {
            background: rgba(255,255,255,0.04);
            transform: translateX(3px);
            box-shadow: -3px 0 0 #6366f1;
        }
        tbody td { padding: 13px 16px; font-size: 13.5px; color: #a1a1aa; }

        .td-title { color: #e4e4e7; font-weight: 600; }
        
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            backdrop-filter: blur(6px);
        }
        .badge-prog { background: rgba(59,130,246,0.1); border: 1px solid rgba(59,130,246,0.22); color: #93c5fd; }
        .badge-cur { background: rgba(234,179,8,0.1); border: 1px solid rgba(234,179,8,0.22); color: #fde047; }
        .badge-fin { background: rgba(34,197,94,0.1); border: 1px solid rgba(34,197,94,0.22); color: #86efac; }
        .badge-can { background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.22); color: #f87171; }

        .actions { display: flex; gap: 6px; }
        .btn-edit {
            padding: 6px 12px;
            border-radius: var(--glass-radius-sm, 10px);
            font-size: 12.5px;
            font-weight: 600;
            background: rgba(99,102,241,0.1);
            border: 1px solid rgba(99,102,241,0.22);
            color: #a5b4fc;
            text-decoration: none;
            transition: all 0.15s;
            backdrop-filter: blur(6px);
        }
        .btn-edit:hover { background: rgba(99,102,241,0.18); color: #c7d2fe; }

        .btn-del {
            padding: 6px 12px;
            border-radius: var(--glass-radius-sm, 10px);
            font-size: 12.5px;
            font-weight: 600;
            background: rgba(239,68,68,0.08);
            border: 1px solid rgba(239,68,68,0.2);
            color: #f87171;
            cursor: pointer;
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: all 0.15s;
            backdrop-filter: blur(6px);
        }
        .btn-del:hover { background: rgba(239,68,68,0.15); }

        .empty-state {
            text-align: center;
            padding: 30px 16px;
        }
        .empty-state p { color: #71717a; font-size: 13.5px; margin: 0; font-weight: 500; }

        .pagination-wrap {
            padding: 16px;
            border-top: 1px solid rgba(255,255,255,0.06);
            display: flex;
            justify-content: center;
        }

        [data-theme="light"] .search-input,
        [data-theme="light"] .filter-select,
        [data-theme="light"] .period-input {
            background: rgba(255, 255, 255, 0.5);
            border-color: rgba(255, 255, 255, 0.8);
            color: #18181b;
        }
        [data-theme="light"] .search-input::placeholder { color: #71717a; }
        [data-theme="light"] .filter-select option { background: #ffffff; color: #18181b; }
        [data-theme="light"] .period-input::-webkit-calendar-picker-indicator { filter: invert(0); }

        [data-theme="light"] .btn-search {
            background: rgba(255,255,255,0.5);
            border-color: rgba(255,255,255,0.8);
            color: #52525b;
        }
        [data-theme="light"] .btn-search:hover { background: rgba(255,255,255,0.75); color: #18181b; }
    </style>

    @if (session('success'))
        <div class="alert alert-success">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="toolbar">
        <form class="search-group" method="GET" action="{{ route('actividades.index') }}" id="searchForm">
            <input class="search-input" type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar por título, lugar, responsable…">
            
            <select class="filter-select" name="estado" onchange="document.getElementById('searchForm').submit()">
                <option value="">Todos los estados</option>
                <option value="Programada" {{ request('estado') === 'Programada' ? 'selected' : '' }}>Programada</option>
                <option value="En curso" {{ request('estado') === 'En curso' ? 'selected' : '' }}>En curso</option>
                <option value="Finalizada" {{ request('estado') === 'Finalizada' ? 'selected' : '' }}>Finalizada</option>
                <option value="Cancelada" {{ request('estado') === 'Cancelada' ? 'selected' : '' }}>Cancelada</option>
            </select>

            <select class="filter-select" name="tipo_periodo" id="tipo_periodo">
                <option value="dia" {{ request('tipo_periodo') === 'dia' ? 'selected' : '' }}>Por Día</option>
                <option value="mes" {{ request('tipo_periodo', 'mes') === 'mes' ? 'selected' : '' }}>Por Mes</option>
            </select>

            <div id="contenedor-periodo"></div>

            <button type="submit" class="btn btn-search">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                </svg>
                Buscar
            </button>
        </form>

        <div style="display:flex; gap:12px; flex-wrap:wrap; align-items:center;">
            <form action="{{ route('actividades.export') }}" method="GET" style="display:flex; gap:8px; align-items:center; margin:0;" id="exportForm">
                <input type="hidden" name="buscar" value="{{ request('buscar') }}">
                <input type="hidden" name="estado" value="{{ request('estado') }}">
                <input type="hidden" name="tipo_periodo" value="{{ request('tipo_periodo', 'mes') }}">
                <input type="hidden" name="periodo" value="{{ request('periodo') }}">
                
                <button type="submit" name="formato" value="pdf" class="btn-export pdf">PDF</button>
                <button type="submit" name="formato" value="excel" class="btn-export excel">Excel</button>
            </form>

            <a href="{{ route('actividades.create') }}" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                Nueva actividad
            </a>
        </div>
    </div>

    <div class="table-card">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Título</th>
                        <th>Lugar</th>
                        <th>Responsable</th>
                        <th>Fecha Inicio</th>
                        <th>Fecha Fin</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($actividades as $act)
                        <tr>
                            <td>{{ $act->id }}</td>
                            <td class="td-title">{{ $act->titulo }}</td>
                            <td>{{ $act->lugar ?? '—' }}</td>
                            <td>{{ $act->responsable ?? '—' }}</td>
                            <td>{{ $act->fecha_inicio ? \Carbon\Carbon::parse($act->fecha_inicio)->format('d/m/Y') : '—' }}</td>
                            <td>{{ $act->fecha_fin ? \Carbon\Carbon::parse($act->fecha_fin)->format('d/m/Y') : '—' }}</td>
                            <td>
                                @if($act->estado === 'Programada')
                                    <span class="badge badge-prog">Programada</span>
                                @elseif($act->estado === 'En curso')
                                    <span class="badge badge-cur">En curso</span>
                                @elseif($act->estado === 'Finalizada')
                                    <span class="badge badge-fin">Finalizada</span>
                                @else
                                    <span class="badge badge-can">Cancelada</span>
                                @endif
                            </td>
                            <td>
                                <div class="actions">
                                    <a href="{{ route('actividades.edit', $act) }}" class="btn-edit">Editar</a>
                                    <form method="POST" action="{{ route('actividades.destroy', $act) }}" onsubmit="return confirm('¿Eliminar esta actividad?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-del">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="empty-state">
                                    <p>No hay actividades programadas o registradas en este rango temporal.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($actividades->hasPages())
            <div class="pagination-wrap">
                {{ $actividades->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

    <script>
        function cambiarTipoPeriodo(event) {
            const selectorTipo = document.getElementById('tipo_periodo');
            const tipo = selectorTipo.value;
            const contenedor = document.getElementById('contenedor-periodo');
            const form = document.getElementById('searchForm');
            let inputHtml = '';

            if (tipo === 'dia') {
                let val = "{{ request('tipo_periodo') === 'dia' ? request('periodo') : date('Y-m-d') }}";
                inputHtml = `<input type="date" name="periodo" id="input_periodo_dinamico" value="${val}" class="period-input" onchange="sincronizarYEnviar()">`;
            } else {
                let val = "{{ request('tipo_periodo') === 'mes' || !request('tipo_periodo') ? request('periodo', date('Y-m')) : date('Y-m') }}";
                inputHtml = `<input type="month" name="periodo" id="input_periodo_dinamico" value="${val}" class="period-input" onchange="sincronizarYEnviar()">`;
            }

            contenedor.innerHTML = inputHtml;
            sincronizarFiltrosOcultos();

            if (event && event.type === 'change') {
                form.submit();
            }
        }

        function sincronizarFiltrosOcultos() {
            const elPeriodo = document.getElementById('input_periodo_dinamico');
            const elTipoPeriodo = document.getElementById('tipo_periodo');
            
            const inputOcultoPeriodo = document.querySelector('#exportForm input[name="periodo"]');
            const inputOcultoTipo = document.querySelector('#exportForm input[name="tipo_periodo"]');
            
            if(inputOcultoPeriodo && elPeriodo) inputOcultoPeriodo.value = elPeriodo.value;
            if(inputOcultoTipo && elTipoPeriodo) inputOcultoTipo.value = elTipoPeriodo.value;
        }

        function sincronizarYEnviar() {
            sincronizarFiltrosOcultos();
            document.getElementById('searchForm').submit();
        }

        document.addEventListener("DOMContentLoaded", function() {
            cambiarTipoPeriodo();
            document.getElementById('tipo_periodo').addEventListener('change', cambiarTipoPeriodo);
        });
    </script>
</x-app-layout>