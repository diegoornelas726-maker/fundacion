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
     * EXPORTAR ACTIVIDADES A EXCEL (con estilo) O PDF CON FILTROS APLICADOS
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

        // GENERACIÓN POR DEFECTO: EXCEL CON ESTILO (banner de marca, encabezados índigo, estado en color)
        return $this->excelActividades($actividades, $request);
    }

    /**
     * Renderiza las actividades en un Excel claro, limpio y con la
     * paleta de marca (índigo) usada en el resto del sistema
     */
    private function excelActividades($actividades, Request $request)
    {
        $filename = "actividades_reporte_" . date('Ymd_His') . ".xls";

        $periodoTexto = $request->filled('periodo')
            ? date('m/Y', strtotime($request->periodo))
            : date('m/Y');

        return response()->streamDownload(function () use ($actividades, $periodoTexto) {
            echo "<meta charset='utf-8'>";
            echo "<table border='0' cellpadding='0' cellspacing='0' style='font-family: Calibri, Arial, sans-serif; border-collapse: collapse; background-color: #ffffff;'>";

            // Anchos de columna fijos
            echo "<colgroup>";
            echo "<col style='width:50px'>";   // #
            echo "<col style='width:240px'>";  // Título
            echo "<col style='width:140px'>";  // Tipo
            echo "<col style='width:170px'>";  // Responsable
            echo "<col style='width:190px'>";  // Lugar
            echo "<col style='width:120px'>";  // Fecha Inicio
            echo "<col style='width:120px'>";  // Fecha Fin
            echo "<col style='width:130px'>";  // Participantes
            echo "<col style='width:130px'>";  // Estado
            echo "</colgroup>";

            // ── Título de ancho completo ──
            echo "<tr><td colspan='9' style='background-color: #4f46e5; color: #ffffff; font-size: 22px; font-weight: bold; padding: 20px 22px 4px; border: none;'>Fundación Don Benjamín</td></tr>";
            echo "<tr><td colspan='9' style='background-color: #4f46e5; color: #e0e7ff; font-size: 14px; padding: 0 22px 18px; border: none;'>Reporte de actividades &middot; Periodo: {$periodoTexto} &middot; Generado el ".now()->locale('es')->isoFormat('D [de] MMMM [de] YYYY, HH:mm')."</td></tr>";

            // Filas de respiro
            echo "<tr><td colspan='9' style='border: none; background-color: #ffffff; padding: 10px; font-size: 6px; line-height: 6px;'>&nbsp;</td></tr>";
            echo "<tr><td colspan='9' style='border: none; background-color: #ffffff; padding: 10px; font-size: 6px; line-height: 6px;'>&nbsp;</td></tr>";

            // ── Encabezados ──
            echo "<tr>";
            foreach (['#', 'Título', 'Tipo', 'Responsable', 'Lugar', 'Fecha Inicio', 'Fecha Fin', 'Participantes', 'Estado'] as $col) {
                echo "<td style='background-color: #eef2ff; color: #3730a3; padding: 14px 16px; text-align: left; font-size: 13.5px; font-weight: bold; text-transform: uppercase; border: 1px solid #c7d2fe;'>".htmlspecialchars($col)."</td>";
            }
            echo "</tr>";

            // ── Filas de datos ──
            foreach ($actividades as $rowIndex => $act) {
                $bgAlt = $rowIndex % 2 === 0 ? '#ffffff' : '#f9fafb';

                switch ($act->estado) {
                    case 'Programada':
                        $colorEstado = 'color: #2563eb; background-color: #eff6ff;';
                        break;
                    case 'En curso':
                        $colorEstado = 'color: #b45309; background-color: #fffbeb;';
                        break;
                    case 'Finalizada':
                        $colorEstado = 'color: #059669; background-color: #ecfdf5;';
                        break;
                    case 'Cancelada':
                        $colorEstado = 'color: #dc2626; background-color: #fef2f2;';
                        break;
                    default:
                        $colorEstado = 'color: #374151; background-color: '.$bgAlt.';';
                }

                $celdaBase = "padding: 13px 16px; color: #1f2937; background-color: {$bgAlt}; font-size: 14px; border: 1px solid #e5e7eb;";

                echo "<tr>";
                echo "<td style='{$celdaBase}'>".htmlspecialchars($act->id)."</td>";
                echo "<td style='{$celdaBase} font-weight: bold;'>".htmlspecialchars($act->titulo)."</td>";
                echo "<td style='{$celdaBase}'>".htmlspecialchars($act->tipo ?? '—')."</td>";
                echo "<td style='{$celdaBase}'>".htmlspecialchars($act->responsable ?? '—')."</td>";
                echo "<td style='{$celdaBase}'>".htmlspecialchars($act->lugar ?? '—')."</td>";
                echo "<td style='{$celdaBase}'>".htmlspecialchars($act->fecha_inicio ? date('d/m/Y', strtotime($act->fecha_inicio)) : '—')."</td>";
                echo "<td style='{$celdaBase}'>".htmlspecialchars($act->fecha_fin ? date('d/m/Y', strtotime($act->fecha_fin)) : '—')."</td>";
                echo "<td style='{$celdaBase}'>".htmlspecialchars($act->participantes_esperados ?? '—')."</td>";
                echo "<td style='padding: 13px 16px; font-size: 14px; font-weight: bold; border: 1px solid #e5e7eb; {$colorEstado}'>".htmlspecialchars($act->estado)."</td>";
                echo "</tr>";
            }

            echo "</table>";
        }, $filename, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
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