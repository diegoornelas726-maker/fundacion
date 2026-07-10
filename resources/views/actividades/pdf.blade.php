<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Reporte de Actividades</title>
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
            font-size: 20px; \n            color: #1e1b4b; 
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
            margin-top: 10px;
        }
        th { 
            background-color: #f3f4f6; 
            color: #374151; 
            font-weight: bold; 
            text-transform: uppercase; 
            font-size: 10px; 
            letter-spacing: 0.5px; 
            border-bottom: 2px solid #e5e7eb;
            padding: 10px 12px;
            text-align: left;
        }
        td { 
            border-bottom: 1px solid #e5e7eb; 
            padding: 10px 12px;
            color: #4b5563;
            vertical-align: middle;
        }
        .td-id {
            font-weight: bold;
            color: #9ca3af;
        }
        .td-title {
            font-weight: bold;
            color: #111827;
        }
        .empty { 
            text-align: center; 
            padding: 30px; 
            color: #9ca3af; 
            font-style: italic;
            font-size: 13px;
        }
        
        /* Badges/Chips estilizados para los estados de actividades */
        .badge {
            display: inline-block;
            padding: 4px 7px;
            border-radius: 4px;
            font-size: 9.5px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-programada { background-color: #dbeafe; color: #1e40af; }
        .badge-encurso { background-color: #fef08a; color: #854d0e; }
        .badge-finalizada { background-color: #dcfce7; color: #166534; }
        .badge-cancelada { background-color: #fee2e2; color: #991b1b; }

        .footer { 
            position: fixed; 
            bottom: 0; 
            left: 0; 
            right: 0; 
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
        <h1>Reporte de Actividades</h1>
        <p>{{ $mes }}</p>
    </div>

    <div class="meta">
        <strong>Total de Registros:</strong> {{ $actividades->count() }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 25%;">Título</th>
                <th style="width: 15%;">Lugar</th>
                <th style="width: 20%;">Responsable</th>
                <th style="width: 12%;">Inicio</th>
                <th style="width: 11%;">Fin</th>
                <th style="width: 12%;">Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($actividades as $act)
                <tr>
                    <td class="td-id">{{ $act->id }}</td>
                    <td class="td-title">{{ $act->titulo }}</td>
                    <td>{{ $act->lugar ?? '—' }}</td>
                    <td>{{ $act->responsable ?? '—' }}</td>
                    <td>{{ $act->fecha_inicio ? \Carbon\Carbon::parse($act->fecha_inicio)->format('d/m/Y') : '—' }}</td>
                    <td>{{ $act->fecha_fin ? \Carbon\Carbon::parse($act->fecha_fin)->format('d/m/Y') : '—' }}</td>
                    <td>
                        @if($act->estado === 'Programada')
                            <span class="badge badge-programada">Programada</span>
                        @elseif($act->estado === 'En curso')
                            <span class="badge badge-encurso">En curso</span>
                        @elseif($act->estado === 'Finalizada')
                            <span class="badge badge-finalizada">Finalizada</span>
                        @else
                            <span class="badge badge-cancelada">Cancelada</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="empty">
                        No se encontraron actividades registradas en este rango temporal.
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