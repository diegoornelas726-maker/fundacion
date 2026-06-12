<x-app-layout>
    <x-slot name="header">
        <h1>Beneficiarios</h1>
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

        .search-group {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

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

        .search-input { min-width: 240px; }
        .search-input::placeholder { color: #3f3f46; }
        .search-input:focus, .filter-select:focus {
            border-color: rgba(99,102,241,0.5);
        }

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

        /* Alerta */
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

        /* Tabla */
        .table-card {
            background: rgba(18,18,20,0.8);
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(0,0,0,0.3);
        }

        .table-wrap { overflow-x: auto; }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead tr {
            border-bottom: 1px solid rgba(255,255,255,0.07);
        }

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

        tbody td {
            padding: 13px 16px;
            font-size: 13.5px;
            color: #a1a1aa;
        }

        .td-name { color: #e4e4e7; font-weight: 600; }

        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 99px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-activo {
            background: rgba(34,197,94,0.1);
            border: 1px solid rgba(34,197,94,0.2);
            color: #86efac;
        }

        .badge-inactivo {
            background: rgba(113,113,122,0.12);
            border: 1px solid rgba(113,113,122,0.2);
            color: #71717a;
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

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-state svg { width: 40px; height: 40px; color: #3f3f46; margin-bottom: 12px; }
        .empty-state p { color: #52525b; font-size: 14px; }

        /* Paginación */
        .pagination-wrap {
            padding: 16px;
            border-top: 1px solid rgba(255,255,255,0.06);
            display: flex;
            justify-content: center;
        }

        .pagination-wrap nav { display: flex; gap: 4px; align-items: center; }
    </style>


    <!-- Barra de herramientas -->
    <div class="toolbar">
        <form class="search-group" method="GET" action="{{ route('beneficiarios.index') }}">
            <input class="search-input" type="text" name="buscar"
                   value="{{ request('buscar') }}"
                   placeholder="Buscar por nombre, CURP o teléfono…">
            <select class="filter-select" name="estado">
                <option value="">Todos los estados</option>
                <option value="Activo"   {{ request('estado') === 'Activo'   ? 'selected' : '' }}>Activo</option>
                <option value="Inactivo" {{ request('estado') === 'Inactivo' ? 'selected' : '' }}>Inactivo</option>
            </select>
            <button type="submit" class="btn btn-search">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                </svg>
                Buscar
            </button>
        </form>

        <a href="{{ route('beneficiarios.create') }}" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            Nuevo beneficiario
        </a>
    </div>

    <!-- Tabla -->
    <div class="table-card">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nombre completo</th>
                        <th>CURP</th>
                        <th>Teléfono</th>
                        <th>Colonia</th>
                        <th>Estado</th>
                        <th>Registro</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($beneficiarios as $b)
                        <tr class="fade-in-row" style="--row-index: {{ $loop->index }}">
                            <td>{{ $b->id }}</td>
                            <td class="td-name">{{ $b->nombre_completo }}</td>
                            <td>{{ $b->curp ?? '—' }}</td>
                            <td>{{ $b->telefono ?? '—' }}</td>
                            <td>{{ $b->colonia ?? '—' }}</td>
                            <td>
                                <span class="badge {{ $b->estado === 'Activo' ? 'badge-activo' : 'badge-inactivo' }}">
                                    {{ $b->estado }}
                                </span>
                            </td>
                            <td>{{ $b->created_at->format('d/m/Y') }}</td>
                            <td>
                                <div class="actions">
                                    <a href="{{ route('beneficiarios.edit', $b) }}" class="btn-edit">Editar</a>
                                    <form method="POST" action="{{ route('beneficiarios.destroy', $b) }}"
                                          onsubmit="return confirm('¿Eliminar a {{ $b->nombre_completo }}? Esta acción no se puede deshacer.')">
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
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/>
                                    </svg>
                                    <p>No se encontraron beneficiarios. <a href="{{ route('beneficiarios.create') }}" style="color:#818cf8;">Registra el primero.</a></p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($beneficiarios->hasPages())
            <div class="pagination-wrap">
                {{ $beneficiarios->links() }}
            </div>
        @endif
    </div>
</x-app-layout>