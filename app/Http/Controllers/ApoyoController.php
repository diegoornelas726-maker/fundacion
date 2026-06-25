<?php

namespace App\Http\Controllers;

use App\Models\Apoyo;
use App\Models\Beneficiario;
use Illuminate\Http\Request;

class ApoyoController extends Controller
{
    public function index(Request $request)
    {
        $query = Apoyo::with('beneficiario');

        // Filtro por término de búsqueda (tipo o beneficiario)
        if ($request->filled('buscar')) {
            $b = $request->buscar;
            $query->where(function ($q) use ($b) {
                $q->where('tipo_apoyo', 'like', "%$b%")
                  ->orWhereHas('beneficiario', fn($q2) =>
                      $q2->where('nombre', 'like', "%$b%")
                         ->orWhere('apellido_paterno', 'like', "%$b%")
                  );
            });
        }

        // Filtro por Estado (Pendiente, Entregado, Cancelado)
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Filtro por Tipo de Apoyo
        if ($request->filled('tipo')) {
            $query->where('tipo_apoyo', $request->tipo);
        }

        // Filtro dinámico por periodo temporal (Día o Mes)
        if ($request->filled('periodo')) {
            if ($request->tipo_periodo === 'dia') {
                $query->whereDate('fecha_apoyo', $request->periodo);
            } else {
                $año = date('Y', strtotime($request->periodo));
                $mes = date('m', strtotime($request->periodo));
                $query->whereYear('fecha_apoyo', $año)
                      ->whereMonth('fecha_apoyo', $mes);
            }
        } else {
            $query->whereYear('fecha_apoyo', date('Y'))
                  ->whereMonth('fecha_apoyo', date('m'));
        }

        $apoyos = $query->latest('fecha_apoyo')->paginate(15)->withQueryString();
        $tipos = Apoyo::select('tipo_apoyo')->distinct()->orderBy('tipo_apoyo')->pluck('tipo_apoyo');

        return view('apoyos.index', compact('apoyos', 'tipos'));
    }

    /**
     * EXPORTAR A EXCEL/CSV CON LOS MISMOS FILTROS DE LA TABLA
     */
    public function export(Request $request)
    {
        $query = Apoyo::with('beneficiario');

        // 1. Aplicar exactamente los mismos filtros que en el index
        if ($request->filled('buscar')) {
            $b = $request->buscar;
            $query->where(function ($q) use ($b) {
                $q->where('tipo_apoyo', 'like', "%$b%")
                  ->orWhereHas('beneficiario', fn($q2) =>
                      $q2->where('nombre', 'like', "%$b%")
                         ->orWhere('apellido_paterno', 'like', "%$b%")
                  );
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('tipo')) {
            $query->where('tipo_apoyo', $request->tipo);
        }

        if ($request->filled('periodo')) {
            if ($request->tipo_periodo === 'dia') {
                $query->whereDate('fecha_apoyo', $request->periodo);
            } else {
                $año = date('Y', strtotime($request->periodo));
                $mes = date('m', strtotime($request->periodo));
                $query->whereYear('fecha_apoyo', $año)
                      ->whereMonth('fecha_apoyo', $mes);
            }
        } else {
            $query->whereYear('fecha_apoyo', date('Y'))
                  ->whereMonth('fecha_apoyo', date('m'));
        }

        $apoyos = $query->latest('fecha_apoyo')->get();

        // 2. Configurar cabeceras de descarga para Excel/CSV nativo
        $filename = "apoyos_reporte_" . date('Ymd_His') . ".csv";
        
        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        // 3. Crear el flujo del archivo estructurado
        $callback = function() use($apoyos) {
            $file = fopen('php://output', 'w');
            
            // Añadir la marca de orden de bytes (BOM) para que Excel detecte los acentos correctamente
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Encabezados correctos de las columnas para Apoyos
            fputcsv($file, ['#', 'Beneficiario', 'Descripción / Beneficio', 'Tipo de Apoyo', 'Monto', 'Fecha de Apoyo', 'Estado']);

            // Rellenar las filas (si no hay datos, el archivo se descargará limpio solo con los encabezados)
            foreach ($apoyos as $apoyo) {
                $nombreCompleto = $apoyo->beneficiario 
                    ? "{$apoyo->beneficiario->nombre} {$apoyo->beneficiario->apellido_paterno} {$apoyo->beneficiario->apellido_materno}"
                    : '—';

                fputcsv($file, [
                    $apoyo->id,
                    $nombreCompleto,
                    $apoyo->descripcion ?? '—',
                    $apoyo->tipo_apoyo,
                    "$" . number_format($apoyo->monto, 2),
                    date('d/m/Y', strtotime($apoyo->fecha_apoyo)),
                    $apoyo->estado
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function create()
    {
        $beneficiarios = Beneficiario::where('estado', 'Activo')
            ->orderBy('apellido_paterno')
            ->get();

        return view('apoyos.create', compact('beneficiarios'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'beneficiario_id' => 'required|exists:beneficiarios,id',
            'tipo_apoyo'      => 'required|string|max:100',
            'descripcion'     => 'nullable|string',
            'fecha_apoyo'     => 'required|date',
            'monto'           => 'nullable|numeric|min:0',
            'estado'          => 'required|in:Entregado,Pendiente,Cancelado',
            'observaciones'   => 'nullable|string',
        ], [
            'beneficiario_id.required' => 'Debes seleccionar un beneficiario.',
            'beneficiario_id.exists'   => 'El beneficiario seleccionado no existe.',
            'tipo_apoyo.required'      => 'El tipo de apoyo es obligatorio.',
            'fecha_apoyo.required'     => 'La fecha del apoyo es obligatoria.',
            'monto.numeric'            => 'El monto debe ser un número válido.',
        ]);

        Apoyo::create($request->all());

        return redirect()->route('apoyos.index')
            ->with('success', 'Apoyo registrado correctamente.');
    }

    public function edit(Apoyo $apoyo)
    {
        $beneficiarios = Beneficiario::orderBy('apellido_paterno')->get();

        return view('apoyos.edit', compact('apoyo', 'beneficiarios'));
    }

    public function update(Request $request, Apoyo $apoyo)
    {
        $request->validate([
            'beneficiario_id' => 'required|exists:beneficiarios,id',
            'tipo_apoyo'      => 'required|string|max:100',
            'descripcion'     => 'nullable|string',
            'fecha_apoyo'     => 'required|date',
            'monto'           => 'nullable|numeric|min:0',
            'estado'          => 'required|in:Entregado,Pendiente,Cancelado',
            'observaciones'   => 'nullable|string',
        ], [
            'beneficiario_id.required' => 'Debes seleccionar un beneficiario.',
            'tipo_apoyo.required'      => 'El tipo de apoyo es obligatorio.',
            'fecha_apoyo.required'     => 'La fecha del apoyo es obligatoria.',
        ]);

        $apoyo->update($request->all());

        return redirect()->route('apoyos.index')
            ->with('success', 'Apoyo actualizado correctamente.');
    }

    public function destroy(Apoyo $apoyo)
    {
        $apoyo->delete();

        return redirect()->route('apoyos.index')
            ->with('success', 'Apoyo eliminado correctamente.');
    }
}