<x-app-layout>
    <x-slot name="header">
        <h1>Asistencia por persona</h1>
    </x-slot>

    <style>
        .top { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 20px; }
        .btn { display: inline-flex; align-items: center; gap: 7px; padding: 9px 16px; border-radius: 10px; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 13.5px; font-weight: 600; cursor: pointer; text-decoration: none; border: none; transition: all 0.18s; }
        .btn svg { width: 15px; height: 15px; }
        .btn-ghost { background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.09); color: #d4d4d8; }
        .btn-ghost:hover { background: rgba(255,255,255,0.08); }

        .table-card { background: rgba(18,18,20,0.8); border: 1px solid rgba(255,255,255,0.07); border-radius: 16px; overflow: hidden; backdrop-filter: blur(12px); box-shadow: 0 8px 32px rgba(0,0,0,0.3); }
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        thead th { text-align: left; padding: 13px 20px; font-size: 11.5px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #52525b; border-bottom: 1px solid rgba(255,255,255,0.06); white-space: nowrap; }
        tbody td { padding: 12px 20px; font-size: 13.5px; color: #d4d4d8; border-bottom: 1px solid rgba(255,255,255,0.04); }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr { transition: background 0.15s; }
        tbody tr:hover { background: rgba(255,255,255,0.02); }
        .td-name { color: #f4f4f5; font-weight: 600; }

        .pct-cell { display: flex; align-items: center; gap: 10px; min-width: 160px; }
        .pct-bar { flex: 1; height: 7px; border-radius: 99px; background: rgba(255,255,255,0.07); overflow: hidden; }
        .pct-fill { height: 100%; border-radius: 99px; transition: width 0.6s ease; }
        .pct-num { font-weight: 700; font-size: 13px; width: 42px; text-align: right; }
        .pct-high { background: linear-gradient(90deg, #16a34a, #4ade80); }
        .pct-mid  { background: linear-gradient(90deg, #d97706, #fbbf24); }
        .pct-low  { background: linear-gradient(90deg, #dc2626, #f87171); }
        .th-right, .td-right { text-align: right; }
        .empty-state { text-align: center; padding: 60px 20px; color: #52525b; font-size: 14px; }

        [data-theme="light"] .table-card { background: rgba(255,255,255,0.9); border-color: rgba(0,0,0,0.07); }
        [data-theme="light"] thead th { color: #71717a; border-bottom-color: rgba(0,0,0,0.07); }
        [data-theme="light"] tbody td { color: #3f3f46; border-bottom-color: rgba(0,0,0,0.05); }
        [data-theme="light"] .td-name { color: #18181b; }
        [data-theme="light"] tbody tr:hover { background: rgba(0,0,0,0.02); }
        [data-theme="light"] .btn-ghost { background: rgba(0,0,0,0.04); border-color: rgba(0,0,0,0.1); color: #3f3f46; }
    </style>

    <div class="top">
        <p style="color:#71717a; font-size:13.5px;">Porcentaje de asistencia de cada beneficiario (presentes ÷ días registrados).</p>
        <a href="{{ route('asistencia.index') }}" class="btn btn-ghost">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.9">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/>
            </svg>
            Tomar asistencia
        </a>
    </div>

    <div class="table-card">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Beneficiario</th>
                        <th>Días presente</th>
                        <th>Días registrados</th>
                        <th>% Asistencia</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($personas as $p)
                        @php
                            $cls = $p->porcentaje >= 75 ? 'pct-high' : ($p->porcentaje >= 40 ? 'pct-mid' : 'pct-low');
                        @endphp
                        <tr>
                            <td class="td-name">
                                <span class="name-with-avatar">
                                    <x-avatar :name="$p->nombre_completo" :size="30" />
                                    {{ $p->nombre_completo }}
                                </span>
                            </td>
                            <td>{{ $p->dias_presente }}</td>
                            <td>{{ $p->total_dias }}</td>
                            <td>
                                <div class="pct-cell">
                                    <div class="pct-bar"><div class="pct-fill {{ $cls }}" style="width: {{ $p->porcentaje }}%"></div></div>
                                    <span class="pct-num">{{ $p->porcentaje }}%</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4"><div class="empty-state">No hay beneficiarios registrados.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
