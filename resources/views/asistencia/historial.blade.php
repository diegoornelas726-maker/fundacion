<x-app-layout>
    <x-slot name="header">
        <h1>Historial de asistencia</h1>
    </x-slot>

    <style>
        .hist-top { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            flex-wrap: wrap; 
            gap: 12px; 
            margin-bottom: 20px; 
        }
        
        .filter-group {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .filter-label {
            font-size: 12px;
            font-weight: 600;
            color: #a1a1aa;
            text-transform: uppercase;
        }

        .filter-select, .period-input {
            padding: 9px 14px;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.09);
            border-radius: 10px;
            color: #f4f4f5;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 13.5px;
            outline: none;
            transition: border-color 0.18s;
        }
        .filter-select option { background: #18181a; }
        .filter-select:focus, .period-input:focus { border-color: rgba(99,102,241,0.5); }
        
        .period-input::-webkit-calendar-picker-indicator {
            filter: invert(1);
            cursor: pointer;
        }

        .hist-card {
            background: rgba(18, 18, 20, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.07);
            border-radius: 16px;
            overflow: hidden;
            backdrop-filter: blur(12px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
        }

        .hist-table { width: 100%; border-collapse: collapse; text-align: left; }
        .hist-table th {
            padding: 14px 20px;
            background: rgba(255, 255, 255, 0.02);
            border-bottom: 1px solid rgba(255, 255, 255, 0.07);
            color: #a1a1aa;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .hist-table td {
            padding: 14px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
            color: #e4e4e7;
            font-size: 14px;
        }
        .hist-table tr:last-child td { border-bottom: none; }
        .hist-table tr:hover td { background: rgba(255, 255, 255, 0.01); }

        .btn-view {
            color: #6366f1;
            text-decoration: none;
            font-weight: 600;
            font-size: 13.5px;
            transition: color 0.15s;
        }
        .btn-view:hover { color: #818cf8; text-decoration: underline; }

        .btn-ex {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 16px;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.09);
            border-radius: 10px;
            color: #d4d4d8;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 13.5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.18s;
            text-decoration: none;
        }
        .btn-ex:hover {
            background: #ffffff;
            border-color: #ffffff;
            color: #09090b;
        }
        .btn-ex svg { width: 15px; height: 15px; }

        .btn-ex-pdf {
            background: rgba(239,68,68,0.1);
            border-color: rgba(239,68,68,0.2);
            color: #f87171;
        }
        .btn-ex-pdf:hover {
            background: rgba(239,68,68,0.18);
            border-color: rgba(239,68,68,0.3);
            color: #fca5a5;
        }

        .empty { padding: 40px 20px !important; text-align: center; color: #71717a !important; font-size: 14px; }
        .pagi { padding: 16px 20px; border-top: 1px solid rgba(255, 255, 255, 0.07); }

        /* ── Modo claro ── */
        [data-theme="light"] .hist-card {
            background: rgba(255,255,255,0.9);
            border-color: rgba(0,0,0,0.07);
            box-shadow: 0 8px 32px rgba(0,0,0,0.06);
        }
        [data-theme="light"] .hist-table th {
            background: rgba(0,0,0,0.02);
            border-bottom-color: rgba(0,0,0,0.08);
            color: #71717a;
        }
        [data-theme="light"] .hist-table td {
            color: #52525b;
            border-bottom-color: rgba(0,0,0,0.05);
        }
        [data-theme="light"] .hist-table td strong {
            color: #18181b;
        }
        [data-theme="light"] .hist-table tr:hover td {
            background: rgba(0,0,0,0.03);
        }
        [data-theme="light"] .filter-label { color: #71717a; }
        [data-theme="light"] .filter-select,
        [data-theme="light"] .period-input {
            background: rgba(0,0,0,0.03);
            border-color: rgba(0,0,0,0.1);
            color: #18181b;
        }
        [data-theme="light"] .filter-select option { background: #fff; }
        [data-theme="light"] .btn-ex {
            background: rgba(0,0,0,0.04);
            border-color: rgba(0,0,0,0.1);
            color: #52525b;
        }
        [data-theme="light"] .btn-ex:hover {
            background: #18181b;
            border-color: #18181b;
            color: #fff;
        }
        [data-theme="light"] .btn-ex-pdf {
            background: rgba(239,68,68,0.08);
            border-color: rgba(239,68,68,0.18);
            color: #dc2626;
        }
        [data-theme="light"] .btn-ex-pdf:hover {
            background: rgba(239,68,68,0.16);
            border-color: rgba(239,68,68,0.3);
            color: #b91c1c;
        }
        [data-theme="light"] .empty { color: #71717a; }
        [data-theme="light"] .pagi { border-top-color: rgba(0,0,0,0.07); }
        [data-theme="light"] .period-input::-webkit-calendar-picker-indicator {
            filter: none;
        }
    </style>

    <div class="hist-top">
        <form method="GET" action="{{ route('asistencia.historial') }}" id="searchForm" class="filter-group">
            <div class="filter-group">
                <span class="filter-label">Mes:</span>
                <input type="month" name="filtro_mes" value="{{ $filtroMes }}" class="period-input" onchange="document.getElementById('searchForm').submit()">
            </div>

            <div class="filter-group">
                <span class="filter-label">Día:</span>
                <select name="filtro_dia" class="filter-select" onchange="document.getElementById('searchForm').submit()">
                    <option value="todos" {{ $filtroDia == 'todos' || !$filtroDia ? 'selected' : '' }}>Todos los días</option>
                    @for ($i = 1; $i <= 31; $i++)
                        @php $dVal = sprintf('%02d', $i); @endphp
                        <option value="{{ $dVal }}" {{ $filtroDia == $dVal ? 'selected' : '' }}>{{ $dVal }}</option>
                    @endfor
                </select>
            </div>
        </form>

        <form action="{{ route('asistencia.export') }}" method="GET" id="exportForm" style="display:flex; gap:8px;">
            <input type="hidden" name="tipo_periodo" id="export_tipo_periodo" value="mes">
            <input type="hidden" name="periodo" id="export_periodo" value="{{ $filtroMes }}">
            <button type="submit" name="formato" value="pdf" class="btn-ex btn-ex-pdf">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>
                PDF
            </button>
            <button type="submit" name="formato" value="excel" class="btn-ex">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v4M7 10l5 5 5-5M12 15V3"/></svg>
                Exportar Excel
            </button>
        </form>
    </div>

    <div class="hist-card">
        <table class="hist-table">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Asistencias</th>
                    <th style="width: 100px;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($fechas as $f)
                    @php
                        $cFecha = \Illuminate\Support\Carbon::parse($f->fecha);
                    @endphp
                    <tr>
                        <td>
                            <strong>{{ $cFecha->locale('es')->isoFormat('dddd') }}</strong>, 
                            {{ $cFecha->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }}
                        </td>
                        <td>
                            <span style="color:#34d399; font-weight:600;">{{ $f->presentes }}</span> 
                            <span style="color:#71717a;">/ {{ $f->total }} presentes</span>
                        </td>
                        <td>
                            <a href="{{ route('asistencia.index', ['fecha' => $f->fecha]) }}" class="btn-view">Ver/Editar</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="empty">No se encontraron registros de asistencia para el período seleccionado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($fechas->hasPages())
            <div class="pagi">
                {{ $fechas->links() }}
            </div>
        @endif
    </div>

    <script>
        // Sincroniza los filtros activos del buscador hacia el formulario del botón de exportar excel
        function sincronizarFormularioExportacion() {
            const mesVal = document.querySelector('input[name="filtro_mes"]').value;
            const diaVal = document.querySelector('select[name="filtro_dia"]').value;
            
            // Si elige un día específico, le indicamos al ExportController el tipo 'dia' con formato completo
            if(diaVal && diaVal !== 'todos') {
                document.getElementById('export_tipo_periodo').value = 'dia';
                document.getElementById('export_periodo').value = mesVal + '-' + diaVal;
            } else {
                document.getElementById('export_tipo_periodo').value = 'mes';
                document.getElementById('export_periodo').value = mesVal;
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            sincronizarFormularioExportacion();
            
            document.getElementById('exportForm').addEventListener('submit', function() {
                sincronizarFormularioExportacion();
            });
        });
    </script>
</x-app-layout>