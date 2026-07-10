<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use Illuminate\Http\Request;

class ActividadController extends Controller
{
    public function index(Request $request)
    {
        $query = Actividad::query();

        if ($request->filled('buscar')) {
            $b = $request->buscar;
            $query->where(function ($q) use ($b) {
                $q->where('titulo', 'like', "%$b%")
                  ->orWhere('lugar', 'like', "%$b%")
                  ->orWhere('responsable', 'like', "%$b%");
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        // Filtro dinámico temporal idéntico al de Apoyos
        if ($request->filled('periodo')) {
            if ($request->tipo_periodo === 'dia') {
                $query->whereDate('fecha_inicio', $request->periodo);
            } else {
                $año = date('Y', strtotime($request->periodo));
                $mes = date('m', strtotime($request->periodo));
                $query->whereYear('fecha_inicio', $año)
                      ->whereMonth('fecha_inicio', $mes);
            }
        } else {
            $query->whereYear('fecha_inicio', date('Y'))
                  ->whereMonth('fecha_inicio', date('m'));
        }

        $actividades = $query->orderBy('fecha_inicio', 'desc')->paginate(15)->withQueryString();

        $tipos = Actividad::select('tipo')->whereNotNull('tipo')->distinct()->orderBy('tipo')->pluck('tipo');

        return view('actividades.index', compact('actividades', 'tipos'));
    }

    /**
     * EXPORTAR ACTIVIDADES A EXCEL/CSV O PDF CON FILTROS APLICADOS
     */
    public function export(Request $request)
    {
        $query = Actividad::query();

        if ($request->filled('buscar')) {
            $b = $request->buscar;
            $query->where(function ($q) use ($b) {
                $q->where('titulo', 'like', "%$b%")
                  ->orWhere('lugar', 'like', "%$b%")
                  ->orWhere('responsable', 'like', "%$b%");
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('periodo')) {
            if ($request->tipo_periodo === 'dia') {
                $query->whereDate('fecha_inicio', $request->periodo);
            } else {
                $año = date('Y', strtotime($request->periodo));
                $mes = date('m', strtotime($request->periodo));
                $query->whereYear('fecha_inicio', $año)
                      ->whereMonth('fecha_inicio', $mes);
            }
        } else {
            $query->whereYear('fecha_inicio', date('Y'))
                  ->whereMonth('fecha_inicio', date('m'));
        }

        $actividades = $query->orderBy('fecha_inicio', 'desc')->get();

        // GENERACIÓN EN CASO DE SOLICITAR PDF
        if ($request->formato === 'pdf') {
            $mesTexto = $request->filled('periodo') ? date('m/Y', strtotime($request->periodo)) : date('m/Y');
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('actividades.pdf', [
                'actividades' => $actividades,
                'mes'         => "Periodo: " . $mesTexto
            ]);
            
            return $pdf->download("reporte_actividades_" . date('Ymd_His') . ".pdf");
        }

        // GENERACIÓN POR DEFECTO EN CASO DE EXCEL / CSV
        $filename = "actividades_reporte_" . date('Ymd_His') . ".csv";
        
        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($actividades) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8

            fputcsv($file, ['#', 'Título', 'Tipo', 'Responsable', 'Lugar', 'Fecha Inicio', 'Fecha Fin', 'Participantes', 'Estado']);

            foreach ($actividades as $act) {
                fputcsv($file, [
                    $act->id,
                    $act->titulo,
                    $act->tipo ?? '—',
                    $act->responsable ?? '—',
                    $act->lugar ?? '—',
                    $act->fecha_inicio ? date('d/m/Y', strtotime($act->fecha_inicio)) : '—',
                    $act->fecha_fin ? date('d/m/Y', strtotime($act->fecha_fin)) : '—',
                    $act->participantes_esperados ?? '—',
                    $act->estado
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function create()
    {
        return view('actividades.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo'                  => 'required|string|max:200',
            'descripcion'             => 'nullable|string',
            'fecha_inicio'            => 'required|date',
            'fecha_fin'               => 'nullable|date|after_or_equal:fecha_inicio',
            'lugar'                   => 'nullable|string|max:200',
            'tipo'                    => 'nullable|string|max:100',
            'responsable'             => 'nullable|string|max:150',
            'participantes_esperados' => 'nullable|integer|min:1',
            'estado'                  => 'required|in:Programada,En curso,Finalizada,Cancelada',
            'observaciones'           => 'nullable|string',
        ], [
            'titulo.required'          => 'El título es obligatorio.',
            'fecha_inicio.required'    => 'La fecha de inicio es obligatoria.',
            'fecha_fin.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la de inicio.',
        ]);

        Actividad::create($request->all());

        return redirect()->route('actividades.index')
            ->with('success', 'Actividad registrada correctamente.');
    }

    public function edit(Actividad $actividad)
    {
        return view('actividades.edit', compact('actividad'));
    }

    public function update(Request $request, Actividad $actividad)
    {
        $request->validate([
            'titulo'                  => 'required|string|max:200',
            'descripcion'             => 'nullable|string',
            'fecha_inicio'            => 'required|date',
            'fecha_fin'               => 'nullable|date|after_or_equal:fecha_inicio',
            'lugar'                   => 'nullable|string|max:200',
            'tipo'                    => 'nullable|string|max:100',
            'responsable'             => 'nullable|string|max:150',
            'participantes_esperados' => 'nullable|integer|min:1',
            'estado'                  => 'required|in:Programada,En curso,Finalizada,Cancelada',
            'observaciones'           => 'nullable|string',
        ], [
            'titulo.required'          => 'El título es obligatorio.',
            'fecha_inicio.required'    => 'La fecha de inicio es obligatoria.',
            'fecha_fin.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la de inicio.',
        ]);

        $actividad->update($request->all());

        return redirect()->route('actividades.index')
            ->with('success', 'Actividad actualizada correctamente.');
    }

    public function destroy(Actividad $actividad)
    {
        $actividad->delete();

        return redirect()->route('actividades.index')
            ->with('success', 'Actividad registrada correctamente.');
    }
}