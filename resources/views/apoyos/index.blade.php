<x-app-layout>
    <x-slot name="header">
        <h1>Apoyos</h1>
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

        .search-group { display: flex; gap: 8px; flex-wrap: wrap; }

        .search-input, .filter-select {
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

        .search-input { min-width: 220px; }
        .search-input::placeholder { color: #3f3f46; }
        .search-input:focus, .filter-select:focus { border-color: rgba(99,102,241,0.5); }
        .filter-select option { background: #18181a; }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 18px;
            border-radius: 10px;
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
            box-shadow: 0 4px 14px rgba(79,70,229,0.3);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #6366f1, #60a5fa);
            transform: translateY(-1px);
        }

        .btn-search {
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.09);
            color: #a1a1aa;
        }

        .btn-search:hover { background: rgba(255,255,255,0.1); color: #e4e4e7; }

        .alert {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 13.5px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: rgba(34,197,94,0.08);
            border: 1px solid rgba(34,197,94,0.2);
            color: #86efac;
        }

        .table-card {
            background: rgba(18,18,20,0.8);
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(0,0,0,0.3);
        }

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

        .td-primary { color: #e4e4e7; font-weight: 600; }

        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 99px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-entregado {
            background: rgba(34,197,94,0.1);
            border: 1px solid rgba(34,197,94,0.2);
            color: #86efac;
        }

        .badge-pendiente {
            background: rgba(234,179,8,0.1);
            border: 1px solid rgba(234,179,8,0.2);
            color: #fde047;
        }

        .badge-cancelado {
            background: rgba(239,68,68,0.08);
            border: 1px solid rgba(239,68,68,0.18);
            color: #f87171;
        }

        .actions { display: flex; gap: 6px; }

        .btn-edit {
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 12.5px;
            font-weight: 600;
            background: rgba(99,102,241,0.1);
            border: 1px solid rgba(99,102,241,0.2);
            color: #a5b4fc;
            text-decoration: none;
            transition: all 0.15s;
        }

        .btn-edit:hover { background: rgba(99,102,241,0.18); color: #c7d2fe; }

        .btn-del {
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 12.5px;
            font-weight: 600;
            background: rgba(239,68,68,0.08);
            border: 1px solid rgba(239,68,68,0.18);
            color: #f87171;
            cursor: pointer;
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: all 0.15s;
        }

        .btn-del:hover { background: rgba(239,68,68,0.15); }

        .empty-state { text-align: center; padding: 60px 20px; }
        .empty-state svg { width: 40px; height: 40px; color: #3f3f46; margin-bottom: 12px; }
        .empty-state p { color: #52525b; font-size: 14px; }

        .pagination-wrap {
            padding: 16px;
            border-top: 1px solid rgba(255,255,255,0.06);
            display: flex;
            justify-content: center;
        }
    </style>



    <div class="toolbar">
        <form class="search-group" method="GET" action="{{ route('apoyos.index') }}">
            <input class="search-input" type="text" name="buscar"
                   value="{{ request('buscar') }}"
                   placeholder="Buscar por beneficiario o tipo…">

            <select class="filter-select" name="estado">
                <option value="">Todos los estados</option>
                @foreach(['Entregado','Pendiente','Cancelado'] as $e)
                    <option value="{{ $e }}" {{ request('estado') === $e ? 'selected' : '' }}>{{ $e }}</option>
                @endforeach
            </select>

            @if($tipos->count())
                <select class="filter-select" name="tipo">
                    <option value="">Todos los tipos</option>
                    @foreach($tipos as $t)
                        <option value="{{ $t }}" {{ request('tipo') === $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            @endif

            <button type="submit" class="btn btn-search">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                </svg>
                Buscar
            </button>
        </form>

        <a href="{{ route('apoyos.create') }}" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            Nuevo apoyo
        </a>
    </div>

    <div class="table-card">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Beneficiario</th>
                        <th>Tipo de apoyo</th>
                        <th>Fecha</th>
                        <th>Monto</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($apoyos as $a)
                        <tr class="fade-in-row" style="--row-index: {{ $loop->index }}">
                            <td>{{ $a->id }}</td>
                            <td class="td-primary">{{ $a->beneficiario->nombre_completo }}</td>
                            <td>{{ $a->tipo_apoyo }}</td>
                            <td>{{ $a->fecha_apoyo->format('d/m/Y') }}</td>
                            <td>{{ $a->monto ? '$' . number_format($a->monto, 2) : '—' }}</td>
                            <td>
                                @php
                                    $badgeClass = match($a->estado) {
                                        'Entregado' => 'badge-entregado',
                                        'Pendiente' => 'badge-pendiente',
                                        default     => 'badge-cancelado',
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ $a->estado }}</span>
                            </td>
                            <td>
                                <div class="actions">
                                    <a href="{{ route('apoyos.edit', $a) }}" class="btn-edit">Editar</a>
                                    <form method="POST" action="{{ route('apoyos.destroy', $a) }}"
                                          onsubmit="return confirm('¿Eliminar este apoyo? Esta acción no se puede deshacer.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-del">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 11.25v8.25a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5v-8.25M12 4.875A2.625 2.625 0 1 0 9.375 7.5H12m0-2.625V7.5m0-2.625A2.625 2.625 0 1 1 14.625 7.5H12m0 0V21m-8.625-9.75h18c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125h-18c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z"/>
                                    </svg>
                                    <p>No hay apoyos registrados. <a href="{{ route('apoyos.create') }}" style="color:#818cf8;">Registra el primero.</a></p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($apoyos->hasPages())
            <div class="pagination-wrap">{{ $apoyos->links() }}</div>
        @endif
    </div>
</x-app-layout>