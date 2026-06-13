<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { color: #1f2937; font-size: 11px; }
        .header { border-bottom: 2px solid #4f46e5; padding-bottom: 10px; margin-bottom: 14px; }
        .header h1 { font-size: 17px; color: #312e81; margin: 0 0 2px; }
        .header p { margin: 0; color: #6b7280; font-size: 11px; }
        .meta { margin-bottom: 12px; font-size: 11px; color: #374151; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #eef2ff; color: #3730a3; text-align: left; padding: 6px 8px; font-size: 10px; border: 1px solid #c7d2fe; }
        td { padding: 5px 8px; font-size: 10.5px; border: 1px solid #e5e7eb; }
        tr:nth-child(even) td { background: #f9fafb; }
        .footer { margin-top: 16px; font-size: 9px; color: #9ca3af; text-align: center; border-top: 1px solid #e5e7eb; padding-top: 6px; }
        .empty { color: #9ca3af; font-style: italic; padding: 8px 0; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Fundación Don Benjamín</h1>
        <p>{{ $titulo }}</p>
    </div>

    <div class="meta">
        <strong>Total de registros:</strong> {{ count($filas) }}
    </div>

    <table>
        <thead>
            <tr>
                @foreach ($columnas as $col)
                    <th>{{ $col }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse ($filas as $fila)
                <tr>
                    @foreach ($fila as $celda)
                        <td>{{ $celda }}</td>
                    @endforeach
                </tr>
            @empty
                <tr><td colspan="{{ count($columnas) }}" class="empty">Sin registros.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Generado el {{ now()->locale('es')->isoFormat('D [de] MMMM [de] YYYY, HH:mm') }} · Sistema Administrativo Interno
    </div>
</body>
</html>
