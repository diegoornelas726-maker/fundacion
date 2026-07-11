<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { color: #1f2937; font-size: 12px; }

        .header {
            background: #4f46e5;
            color: #ffffff;
            padding: 16px 20px;
            margin-bottom: 16px;
            border-radius: 6px;
        }
        .header h1 { font-size: 19px; margin: 0 0 3px; }
        .header p { margin: 0; color: #e0e7ff; font-size: 11.5px; }

        .summary {
            display: block;
            margin-bottom: 18px;
        }
        .summary table { width: 100%; border-collapse: collapse; }
        .summary td {
            padding: 10px 14px;
            border-radius: 6px;
            font-size: 12.5px;
            font-weight: bold;
        }
        .summary .pill-presentes { background: #ecfdf5; color: #059669; border: 1px solid #d1fae5; }
        .summary .pill-ausentes { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
        .summary .pill-total { background: #eef2ff; color: #3730a3; border: 1px solid #c7d2fe; }
        .summary .gap { width: 10px; }

        .fecha-header {
            background: #6366f1;
            color: #ffffff;
            padding: 8px 14px;
            font-size: 12.5px;
            font-weight: bold;
            border-radius: 5px;
            margin-top: 16px;
            margin-bottom: 6px;
        }

        table.personas { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
        table.personas th {
            background: #eef2ff; color: #3730a3; text-align: left;
            padding: 7px 12px; font-size: 10.5px; text-transform: uppercase;
            border: 1px solid #c7d2fe;
        }
        table.personas td {
            padding: 7px 12px; font-size: 11.5px; border: 1px solid #e5e7eb;
        }
        .estado-presente { color: #059669; font-weight: bold; background: #ecfdf5; }
        .estado-ausente { color: #dc2626; font-weight: bold; background: #fef2f2; }

        .footer {
            margin-top: 24px; font-size: 10px; color: #9ca3af;
            text-align: center; border-top: 1px solid #e5e7eb; padding-top: 8px;
        }

        .empty { color: #9ca3af; font-style: italic; padding: 10px 0; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Fundación Don Benjamín</h1>
        <p>{{ $titulo }}</p>
    </div>

    <div class="summary">
        <table>
            <tr>
                <td class="pill-presentes">Total presentes: {{ $totalPresentes }}</td>
                <td class="gap"></td>
                <td class="pill-ausentes">Total ausentes: {{ $totalAusentes }}</td>
                <td class="gap"></td>
                @php
                    $totalReg = $totalPresentes + $totalAusentes;
                    $pct = $totalReg > 0 ? round(($totalPresentes / $totalReg) * 100) : 0;
                @endphp
                <td class="pill-total">{{ $totalReg }} registros &middot; {{ $pct }}% de asistencia</td>
            </tr>
        </table>
    </div>

    @forelse ($grupos as $grupo)
        @php
            $fechaTexto = $grupo['fecha']
                ? ucfirst($grupo['fecha']->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY'))
                : 'Sin fecha';
        @endphp

        <div class="fecha-header">{{ $fechaTexto }}</div>

        <table class="personas">
            <thead>
                <tr>
                    <th style="width: 75%;">Nombre</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($grupo['personas'] as [$nombre, $estado])
                    <tr>
                        <td>{{ $nombre }}</td>
                        <td class="{{ $estado === 'Presente' ? 'estado-presente' : 'estado-ausente' }}">{{ $estado }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @empty
        <div class="empty">No se encontraron registros de asistencia para el período seleccionado.</div>
    @endforelse

    <div class="footer">
        Generado el {{ now()->locale('es')->isoFormat('D [de] MMMM [de] YYYY, HH:mm') }} · Sistema Administrativo Interno
    </div>
</body>
</html>