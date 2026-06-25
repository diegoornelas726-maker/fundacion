<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Reporte de Apoyos</title>
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
        .tipo-chip {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 4px;
            font-size: 9.5px;
            font-weight: bold;
            background: #e0e7ff;
            color: #4338ca;
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
        <h1>Reporte de Apoyos</h1>
        <p>{{ $mes }}</p> 
    </div>

    <div class="meta">
        Total de registros en este periodo: <strong>{{ $apoyos->count() }}</strong>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 35%;">Beneficiario</th>
                <th style="width: 25%;">Descripción / Beneficio</th>
                <th style="width: 15%;">Tipo de Apoyo</th>
                <th style="width: 10%;">Monto</th>
                <th style="width: 10%;">Fecha</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($apoyos as $a)
                <tr>
                    <td class="td-id">{{ $a->id }}</td>
                    <td class="td-name">
                        @if($a->beneficiario)
                            {{ $a->beneficiario->nombre }} {{ $a->beneficiario->apellido_paterno }} {{ $a->beneficiario->apellido_materno }}
                        @else
                            <span style="color:#9ca3af; font-style:italic;">No asignado</span>
                        @endif
                    </td>
                    <td>{{ $a->descripcion ?? '—' }}</td>
                    <td>
                        <span class="tipo-chip">
                            {{ $a->tipo_apoyo }}
                        </span>
                    </td>
                    <td>{{ $a->monto ? '$' . number_format($a->monto, 2) : '—' }}</td>
                    <td>{{ \Carbon\Carbon::parse($a->fecha_apoyo)->format('d/m/Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="empty">
                        No se encontraron apoyos registrados en este rango temporal.
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