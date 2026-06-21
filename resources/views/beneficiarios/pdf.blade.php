<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Reporte de Beneficiarios</title>
    <style>
        * { 
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; 
            color: #1f2937;
        }
        body { 
            font-size: 11px; 
            margin: 10px;
        }
        .header { 
            border-bottom: 2px solid #4f46e5; 
            padding-bottom: 10px; 
            margin-bottom: 20px; 
        }
        .header h1 { 
            font-size: 20px; 
            color: #1e1b4b; 
            margin: 0 0 4px 0; 
        }
        .header p { 
            margin: 0; 
            color: #4b5563; 
            font-size: 12px; 
            font-weight: bold;
        }
        .meta { 
            margin-bottom: 15px; 
            font-size: 11px; 
            color: #374151; 
            text-align: right;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 20px; 
        }
        th { 
            background: #f3f4f6; 
            color: #1f2937; 
            text-align: left; 
            padding: 8px 10px; 
            font-size: 10px; 
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #d1d5db; 
        }
        td { 
            padding: 8px 10px; 
            font-size: 11px; 
            border-bottom: 1px solid #f3f4f6; 
        }
        .td-id {
            color: #9ca3af;
            font-weight: bold;
        }
        .td-name {
            font-weight: bold;
            color: #111827;
        }
        .badge {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 4px;
            font-size: 9.5px;
            font-weight: bold;
        }
        .badge-activo {
            background: #e6f4ea;
            color: #137333;
        }
        .badge-inactivo {
            background: #f1f3f4;
            color: #5f6368;
        }
        .empty { 
            text-align: center; 
            padding: 30px; 
            color: #6b7280; 
            font-size: 13px;
        }
        .footer { 
            position: fixed; 
            bottom: 0; 
            width: 100%; 
            text-align: center; 
            font-size: 10px; 
            color: #9ca3af; 
            border-top: 1px solid #e5e7eb;
            padding-top: 8px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Reporte de Beneficiarios</h1>
        <p>{{ $mes }}</p> 
    </div>

    <div class="meta">
        Total de registros en este periodo: <strong>{{ $beneficiarios->count() }}</strong>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 35%;">Nombre Completo</th>
                <th style="width: 20%;">CURP</th>
                <th style="width: 15%;">Teléfono</th>
                <th style="width: 15%;">Colonia</th>
                <th style="width: 10%;">Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($beneficiarios as $b)
                <tr>
                    <td class="td-id">{{ $b->id }}</td>
                    <td class="td-name">{{ $b->nombre }} {{ $b->apellido_paterno }} {{ $b->apellido_materno }}</td>
                    <td>{{ $b->curp ?? '—' }}</td>
                    <td>{{ $b->telefono ?? '—' }}</td>
                    <td>{{ $b->colonia ?? '—' }}</td>
                    <td>
                        <span class="badge {{ $b->estado === 'Activo' ? 'badge-activo' : 'badge-inactivo' }}">
                            {{ $b->estado }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="empty">
                        No se encontraron beneficiarios registrados en este rango temporal.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Generado automáticamente el {{ now()->format('d/m/Y a las h:i A') }} · Fundación Don Benjamín
    </div>

</body>
</html>