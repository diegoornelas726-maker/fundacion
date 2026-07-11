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
     * EXPORTAR A EXCEL (con estilo) O PDF CON LOS MISMOS FILTROS DE LA TABLA
     */
    public function export(Request $request)
    {
        $query = Apoyo::with('beneficiario');

        // Aplicar filtros idénticos al index
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

        // GENERACIÓN EN CASO DE SOLICITAR PDF
        if ($request->formato === 'pdf') {
            $mesTexto = $request->filled('periodo') ? date('m/Y', strtotime($request->periodo)) : date('m/Y');
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('apoyos.pdf', [
                'apoyos' => $apoyos,
                'mes'    => "Periodo: " . $mesTexto
            ]);
            
            return $pdf->download("reporte_apoyos_" . date('Ymd_His') . ".pdf");
        }

        // GENERACIÓN POR DEFECTO: EXCEL CON ESTILO (banner de marca, encabezados índigo, estado en color)
        return $this->excelApoyos($apoyos, $request);
    }

    /**
     * Renderiza los apoyos en un Excel claro, limpio y con la
     * paleta de marca (índigo) usada en el resto del sistema
     */
    private function excelApoyos($apoyos, Request $request)
    {
        $filename = "apoyos_reporte_" . date('Ymd_His') . ".xls";

        $periodoTexto = $request->filled('periodo')
            ? date('m/Y', strtotime($request->periodo))
            : date('m/Y');

        return response()->streamDownload(function () use ($apoyos, $periodoTexto) {
            echo "<meta charset='utf-8'>";
            echo "<table border='0' cellpadding='0' cellspacing='0' style='font-family: Calibri, Arial, sans-serif; border-collapse: collapse; background-color: #ffffff;'>";

            // Anchos de columna fijos
            echo "<colgroup>";
            echo "<col style='width:50px'>";   // #
            echo "<col style='width:220px'>";  // Beneficiario
            echo "<col style='width:260px'>";  // Descripción
            echo "<col style='width:150px'>";  // Tipo de apoyo
            echo "<col style='width:110px'>";  // Monto
            echo "<col style='width:130px'>";  // Fecha
            echo "<col style='width:130px'>";  // Estado
            echo "</colgroup>";

            // ── Título de ancho completo ──
            echo "<tr><td colspan='7' style='background-color: #4f46e5; color: #ffffff; font-size: 22px; font-weight: bold; padding: 20px 22px 4px; border: none;'>Fundación Don Benjamín</td></tr>";
            echo "<tr><td colspan='7' style='background-color: #4f46e5; color: #e0e7ff; font-size: 14px; padding: 0 22px 18px; border: none;'>Reporte de apoyos &middot; Periodo: {$periodoTexto} &middot; Generado el ".now()->locale('es')->isoFormat('D [de] MMMM [de] YYYY, HH:mm')."</td></tr>";

            // Filas de respiro
            echo "<tr><td colspan='7' style='border: none; background-color: #ffffff; padding: 10px; font-size: 6px; line-height: 6px;'>&nbsp;</td></tr>";
            echo "<tr><td colspan='7' style='border: none; background-color: #ffffff; padding: 10px; font-size: 6px; line-height: 6px;'>&nbsp;</td></tr>";

            // ── Encabezados ──
            echo "<tr>";
            foreach (['#', 'Beneficiario', 'Descripción / Beneficio', 'Tipo de Apoyo', 'Monto', 'Fecha de Apoyo', 'Estado'] as $col) {
                echo "<td style='background-color: #eef2ff; color: #3730a3; padding: 14px 16px; text-align: left; font-size: 13.5px; font-weight: bold; text-transform: uppercase; border: 1px solid #c7d2fe;'>".htmlspecialchars($col)."</td>";
            }
            echo "</tr>";

            // ── Filas de datos ──
            foreach ($apoyos as $rowIndex => $apoyo) {
                $bgAlt = $rowIndex % 2 === 0 ? '#ffffff' : '#f9fafb';

                $nombreCompleto = $apoyo->beneficiario
                    ? "{$apoyo->beneficiario->nombre} {$apoyo->beneficiario->apellido_paterno} {$apoyo->beneficiario->apellido_materno}"
                    : '—';

                switch ($apoyo->estado) {
                    case 'Entregado':
                        $colorEstado = 'color: #059669; background-color: #ecfdf5;';
                        break;
                    case 'Pendiente':
                        $colorEstado = 'color: #b45309; background-color: #fffbeb;';
                        break;
                    case 'Cancelado':
                        $colorEstado = 'color: #dc2626; background-color: #fef2f2;';
                        break;
                    default:
                        $colorEstado = 'color: #374151; background-color: '.$bgAlt.';';
                }

                $celdaBase = "padding: 13px 16px; color: #1f2937; background-color: {$bgAlt}; font-size: 14px; border: 1px solid #e5e7eb;";

                echo "<tr>";
                echo "<td style='{$celdaBase}'>".htmlspecialchars($apoyo->id)."</td>";
                echo "<td style='{$celdaBase} font-weight: bold;'>".htmlspecialchars($nombreCompleto)."</td>";
                echo "<td style='{$celdaBase}'>".htmlspecialchars($apoyo->descripcion ?? '—')."</td>";
                echo "<td style='{$celdaBase}'>".htmlspecialchars($apoyo->tipo_apoyo)."</td>";
                echo "<td style='{$celdaBase}'>$".htmlspecialchars(number_format($apoyo->monto ?? 0, 2))."</td>";
                echo "<td style='{$celdaBase}'>".htmlspecialchars(date('d/m/Y', strtotime($apoyo->fecha_apoyo)))."</td>";
                echo "<td style='padding: 13px 16px; font-size: 14px; font-weight: bold; border: 1px solid #e5e7eb; {$colorEstado}'>".htmlspecialchars($apoyo->estado)."</td>";
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