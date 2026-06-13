<x-app-layout>
    <x-slot name="header">
        <h1>Historial de asistencia</h1>
    </x-slot>

    <style>
        .hist-top { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 20px; }
        .btn {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 9px 16px; border-radius: 10px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 13.5px; font-weight: 600;
            cursor: pointer; text-decoration: none; border: none; transition: all 0.18s;
        }
        .btn svg { width: 15px; height: 15px; }
        .btn-ghost { background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.09); color: #d4d4d8; }
        .btn-ghost:hover { background: rgba(255,255,255,0.08); }

        .table-card {
            background: rgba(18,18,20,0.8); border: 1px solid rgba(255,255,255,0.07);
            border-radius: 16px; overflow: hidden; backdrop-filter: blur(12px);
            box-shadow: 0 8px 32px rgba(0,0,0,0.3);
        }
        table { width: 100%; border-collapse: collapse; }
        thead th { text-align: left; padding: 13px 20px; font-size: 11.5px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #52525b; border-bottom: 1px solid rgba(255,255,255,0.06); }
        tbody td { padding: 13px 20px; font-size: 13.5px; color: #d4d4d8; border-bottom: 1px solid rgba(255,255,255,0.04); }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr { transition: background 0.15s; }
        tbody tr:hover { background: rgba(255,255,255,0.02); }
        .td-date { color: #f4f4f5; font-weight: 600; text-transform: capitalize; }
        .pill { display: inline-block; padding: 3px 11px; border-radius: 999px; font-size: 12px; font-weight: 600; }
        .pill-green { background: rgba(34,197,94,0.12); border: 1px solid rgba(34,197,94,0.22); color: #86efac; }
        .pill-gray { background: rgba(113,113,122,0.12); border: 1px solid rgba(113,113,122,0.2); color: #a1a1aa; }
        .row-actions { display: flex; gap: 6px; justify-content: flex-end; }
        .btn-sm { padding: 6px 12px; border-radius: 8px; font-size: 12.5px; font-weight: 600; text-decoration: none; transition: all 0.15s; }
        .btn-view { background: rgba(99,102,241,0.1); border: 1px solid rgba(99,102,241,0.2); color: #a5b4fc; }
        .btn-view:hover { background: rgba(99,102,241,0.18); }
        .btn-pdf { background: rgba(245,158,11,0.1); border: 1px solid rgba(245,158,11,0.22); color: #fcd34d; }
        .btn-pdf:hover { background: rgba(245,158,11,0.18); }
        .empty-state { text-align: center; padding: 60px 20px; color: #52525b; font-size: 14px; }
        .pagination-wrap { padding: 16px; border-top: 1px solid rgba(255,255,255,0.06); display: flex; justify-content: center; }
        .th-right, .td-right { text-align: right; }

        [data-theme="light"] .table-card { background: rgba(255,255,255,0.9); border-color: rgba(0,0,0,0.07); }
        [data-theme="light"] thead th { color: #71717a; border-bottom-color: rgba(0,0,0,0.07); }
        [data-theme="light"] tbody td { color: #3f3f46; border-bottom-color: rgba(0,0,0,0.05); }
        [data-theme="light"] .td-date { color: #18181b; }
        [data-theme="light"] tbody tr:hover { background: rgba(0,0,0,0.02); }
        [data-theme="light"] .btn-ghost { background: rgba(0,0,0,0.04); border-color: rgba(0,0,0,0.1); color: #3f3f46; }
    </style>

    <div class="hist-top">
        <p style="color:#71717a; font-size:13.5px;">Asistencias registradas por día.</p>
        <a href="{{ route('asistencia.index') }}" class="btn btn-ghost">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.9">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/>
            </svg>
            Tomar asistencia
        </a>
    </div>

    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Presentes</th>
                    <th>Registros</th>
                    <th class="th-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($fechas as $f)
                    @php $fc = \Illuminate\Support\Carbon::parse($f->fecha); @endphp
                    <tr>
                        <td class="td-date">{{ $fc->locale('es')->isoFormat('dddd, D [de] MMMM YYYY') }}</td>
                        <td><span class="pill {{ $f->presentes > 0 ? 'pill-green' : 'pill-gray' }}">{{ $f->presentes }} presentes</span></td>
                        <td>{{ $f->total }}</td>
                        <td class="td-right">
                            <div class="row-actions">
                                <a href="{{ route('asistencia.index', ['fecha' => $fc->format('Y-m-d')]) }}" class="btn-sm btn-view">Ver / editar</a>
                                <a href="{{ route('asistencia.pdf', ['fecha' => $fc->format('Y-m-d')]) }}" class="btn-sm btn-pdf">PDF</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4"><div class="empty-state">Aún no hay asistencias registradas.</div></td></tr>
                @endforelse
            </tbody>
        </table>

        @if ($fechas->hasPages())
            <div class="pagination-wrap">{{ $fechas->links() }}</div>
        @endif
    </div>
</x-app-layout>
