<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { color: #1f2937; font-size: 12px; }
        .header { border-bottom: 2px solid #4f46e5; padding-bottom: 12px; margin-bottom: 18px; }
        .header h1 { font-size: 18px; color: #312e81; margin: 0 0 2px; }
        .header p { margin: 0; color: #6b7280; font-size: 12px; }
        .meta { margin-bottom: 14px; font-size: 12px; color: #374151; }
        .meta strong { color: #111827; }
        h2 { font-size: 13px; color: #312e81; margin: 16px 0 6px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th { background: #eef2ff; color: #3730a3; text-align: left; padding: 7px 10px; font-size: 11px; border: 1px solid #c7d2fe; }
        td { padding: 6px 10px; font-size: 11.5px; border: 1px solid #e5e7eb; }
        .num { width: 30px; text-align: center; color: #6b7280; }
        .tag { display: inline-block; padding: 2px 8px; border-radius: 8px; font-size: 10px; font-weight: bold; }
        .tag-v { background: #fef3c7; color: #92400e; }
        .empty { color: #9ca3af; font-style: italic; padding: 6px 0; }
        .footer { margin-top: 20px; font-size: 10px; color: #9ca3af; text-align: center; border-top: 1px solid #e5e7eb; padding-top: 8px; }
        .summary { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px; padding: 8px 12px; margin-bottom: 14px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Fundación Don Benjamín</h1>
        <p>Lista de asistencia</p>
    </div>

    <div class="meta">
        <strong>Fecha:</strong> {{ $fecha->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
    </div>

    <div class="summary">
        <strong>{{ $presentes->count() }}</strong> presentes &nbsp;·&nbsp;
        <strong>{{ $ausentes->count() }}</strong> ausentes
    </div>

    <h2>Presentes ({{ $presentes->count() }})</h2>
    <table>
        <thead>
            <tr><th class="num">#</th><th>Nombre</th><th style="width:90px;">Tipo</th></tr>
        </thead>
        <tbody>
            @forelse ($presentes as $i => $r)
                <tr>
                    <td class="num">{{ $i + 1 }}</td>
                    <td>{{ $r->nombre }}</td>
                    <td>{{ $r->beneficiario_id ? 'Beneficiario' : '' }}@if(!$r->beneficiario_id)<span class="tag tag-v">Visitante</span>@endif</td>
                </tr>
            @empty
                <tr><td colspan="3" class="empty">Sin asistentes registrados.</td></tr>
            @endforelse
        </tbody>
    </table>

    @if ($ausentes->count() > 0)
        <h2>Ausentes ({{ $ausentes->count() }})</h2>
        <table>
            <thead>
                <tr><th class="num">#</th><th>Nombre</th></tr>
            </thead>
            <tbody>
                @foreach ($ausentes as $i => $r)
                    <tr>
                        <td class="num">{{ $i + 1 }}</td>
                        <td>{{ $r->nombre }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer">
        Generado el {{ now()->locale('es')->isoFormat('D [de] MMMM [de] YYYY, HH:mm') }} · Sistema Administrativo Interno
    </div>
</body>
</html>
