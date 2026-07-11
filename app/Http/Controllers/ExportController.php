<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\Apoyo;
use App\Models\Asistencia;
use App\Models\Beneficiario;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function beneficiarios(Request $request)
    {
        $query = Beneficiario::query();

        if ($request->filled('buscar')) {
            $b = $request->buscar;
            $query->where(function ($q) use ($b) {
                $q->where('nombre', 'like', "%$b%")
                    ->orWhere('apellido_paterno', 'like', "%$b%")
                    ->orWhere('apellido_materno', 'like', "%$b%")
                    ->orWhere('curp', 'like', "%$b%")
                    ->orWhere('telefono', 'like', "%$b%");
            });
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $tipo = $request->input('tipo_periodo', 'mes');
        $periodo = $request->input('periodo');

        if ($periodo) {
            if ($tipo === 'dia') {
                $query->whereDate('created_at', $periodo);
            } else {
                $year = substr($periodo, 0, 4);
                $month = substr($periodo, 5, 2);
                $query->whereYear('created_at', $year)
                      ->whereMonth('created_at', $month);
            }
        }

        $rows = $query->orderBy('apellido_paterno')->get();

        if ($request->input('format') === 'pdf') {
            return $this->pdf($rows, 'beneficiarios.pdf', 'beneficiarios_reporte', $periodo);
        }

        $columnas = ['ID', 'Nombre', 'Apellido Paterno', 'Apellido Materno', 'CURP', 'Teléfono', 'Colonia', 'Estado', 'Fecha Registro'];
        $filas = [];
        foreach ($rows as $b) {
            $filas[] = [
                $b->id,
                $b->nombre,
                $b->apellido_paterno,
                $b->apellido_materno,
                $b->curp,
                $b->telefono,
                $b->colonia,
                $b->estado,
                $b->created_at ? $b->created_at->format('d/m/Y') : '',
            ];
        }

        return $this->excelNativo($columnas, $filas, 'beneficiarios_reporte');
    }

    /**
     * Exporta el historial de asistencias agrupado por fecha, con diseño
     * claro, limpio y profesional
     */
    public function asistencia(Request $request)
    {
        $tipo = $request->input('tipo_periodo', 'mes');
        $periodo = $request->input('periodo');

        $query = Asistencia::with('beneficiario');

        if ($periodo) {
            if ($tipo === 'dia') {
                $query->whereDate('fecha', $periodo);
            } else {
                $year = substr($periodo, 0, 4);
                $month = substr($periodo, 5, 2);
                $query->whereYear('fecha', $year)
                      ->whereMonth('fecha', $month);
            }
        }

        $registros = $query->orderBy('fecha', 'desc')->get();

        $totalPresentes = 0;
        $totalAusentes = 0;

        // Agrupamos los registros por fecha (Y-m-d) para mostrarlos con
        // un encabezado de fecha legible en vez de repetir Día/Mes/Año en cada fila
        $grupos = $registros->groupBy(function ($reg) {
            return $reg->fecha ? Carbon::parse($reg->fecha)->format('Y-m-d') : 'sin-fecha';
        });

        $estructura = [];

        foreach ($grupos as $fechaKey => $items) {
            $personas = [];

            foreach ($items as $reg) {
                $nombre = $reg->beneficiario
                    ? $reg->beneficiario->nombre . ' ' . $reg->beneficiario->apellido_paterno . ' ' . $reg->beneficiario->apellido_materno
                    : $reg->nombre_visitante;

                if ($reg->presente) {
                    $estado = 'Presente';
                    $totalPresentes++;
                } else {
                    $estado = 'Ausente';
                    $totalAusentes++;
                }

                $personas[] = [$nombre, $estado];
            }

            $estructura[] = [
                'fecha'    => $fechaKey !== 'sin-fecha' ? Carbon::parse($fechaKey) : null,
                'personas' => $personas,
            ];
        }

        return $request->formato === 'pdf'
            ? $this->pdfAsistencia($estructura, $totalPresentes, $totalAusentes, $periodo)
            : $this->excelNativoAsistencia($estructura, $totalPresentes, $totalAusentes, 'reporte_asistencias');
    }

    /**
     * Genera el PDF del historial de asistencias, agrupado por fecha,
     * con el mismo diseño de marca que el Excel
     */
    private function pdfAsistencia(array $grupos, int $totalPresentes, int $totalAusentes, ?string $periodo)
    {
        $titulo = $this->tituloConMes('Reporte de asistencias', $periodo);

        $pdf = Pdf::loadView('asistencia.historial_pdf', [
            'grupos'         => $grupos,
            'totalPresentes' => $totalPresentes,
            'totalAusentes'  => $totalAusentes,
            'titulo'         => $titulo,
        ]);

        return $pdf->download('reporte_asistencias_'.date('Ymd_His').'.pdf');
    }

    public function actividades(Request $request)
    {
        return redirect()->back(); 
    }

    /**
     * Renderiza el historial de asistencias agrupado por fecha, con la
     * paleta de marca (índigo) usada en el resto del sistema (PDF, navbar, etc.)
     *
     * $grupos: array de ['fecha' => Carbon|null, 'personas' => [[nombre, estado], ...]]
     */
    private function excelNativoAsistencia(array $grupos, int $totalPresentes, int $totalAusentes, string $slug): StreamedResponse
    {
        $filename = $slug.'.xls';

        return response()->streamDownload(function () use ($grupos, $totalPresentes, $totalAusentes) {
            echo "<meta charset='utf-8'>";
            echo "<table border='0' cellpadding='0' cellspacing='0' style='font-family: Calibri, Arial, sans-serif; border-collapse: collapse; background-color: #ffffff;'>";

            // Anchos de columna fijos
            echo "<colgroup>";
            echo "<col style='width:320px'>";  // Nombre
            echo "<col style='width:150px'>";  // Estado
            echo "</colgroup>";

            // ── Título de ancho completo ──
            echo "<tr><td colspan='2' style='background-color: #4f46e5; color: #ffffff; font-size: 22px; font-weight: bold; padding: 20px 22px 4px; border: none;'>Fundación Don Benjamín</td></tr>";
            echo "<tr><td colspan='2' style='background-color: #4f46e5; color: #e0e7ff; font-size: 14px; padding: 0 22px 18px; border: none;'>Reporte de asistencias &middot; Generado el ".now()->locale('es')->isoFormat('D [de] MMMM [de] YYYY, HH:mm')."</td></tr>";

            // Fila de respiro
            echo "<tr><td colspan='2' style='border: none; background-color: #ffffff; padding: 10px; font-size: 6px; line-height: 6px;'>&nbsp;</td></tr>";

            // ── Resumen de totales ──
            $totalRegistros = $totalPresentes + $totalAusentes;
            $pct = $totalRegistros > 0 ? round(($totalPresentes / $totalRegistros) * 100) : 0;

            echo "<tr>";
            echo "<td style='background-color: #ecfdf5; color: #059669; padding: 12px 16px; font-size: 13.5px; font-weight: bold; border: 1px solid #d1fae5;'>Total presentes: {$totalPresentes}</td>";
            echo "<td style='background-color: #fef2f2; color: #dc2626; padding: 12px 16px; font-size: 13.5px; font-weight: bold; border: 1px solid #fecaca;'>Ausentes: {$totalAusentes}</td>";
            echo "</tr>";
            echo "<tr>";
            echo "<td colspan='2' style='background-color: #eef2ff; color: #3730a3; padding: 10px 16px; font-size: 12.5px; font-weight: bold; border: 1px solid #c7d2fe;'>{$totalRegistros} registros en total &middot; {$pct}% de asistencia</td>";
            echo "</tr>";

            // Fila de respiro
            echo "<tr><td colspan='2' style='border: none; background-color: #ffffff; padding: 10px; font-size: 6px; line-height: 6px;'>&nbsp;</td></tr>";

            // ── Encabezado de columnas de personas ──
            echo "<tr>";
            echo "<td style='background-color: #eef2ff; color: #3730a3; padding: 12px 16px; text-align: left; font-size: 13px; font-weight: bold; text-transform: uppercase; border: 1px solid #c7d2fe;'>Nombre</td>";
            echo "<td style='background-color: #eef2ff; color: #3730a3; padding: 12px 16px; text-align: left; font-size: 13px; font-weight: bold; text-transform: uppercase; border: 1px solid #c7d2fe;'>Estado</td>";
            echo "</tr>";

            // ── Grupos por fecha ──
            $rowIndex = 0;
            foreach ($grupos as $grupo) {
                $fechaTexto = $grupo['fecha']
                    ? ucfirst($grupo['fecha']->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY'))
                    : 'Sin fecha';

                // Encabezado divisorio de fecha
                echo "<tr>";
                echo "<td colspan='2' style='background-color: #6366f1; color: #ffffff; padding: 10px 16px; font-size: 13px; font-weight: bold; border: 1px solid #4f46e5;'>{$fechaTexto}</td>";
                echo "</tr>";

                foreach ($grupo['personas'] as $persona) {
                    [$nombre, $estado] = $persona;
                    $bgAlt = $rowIndex % 2 === 0 ? '#ffffff' : '#f9fafb';

                    $styleNombre = "padding: 12px 16px; color: #1f2937; background-color: {$bgAlt}; font-size: 13.5px; border: 1px solid #e5e7eb;";

                    if ($estado === 'Presente') {
                        $styleEstado = "padding: 12px 16px; font-size: 13.5px; font-weight: bold; border: 1px solid #e5e7eb; color: #059669; background-color: #ecfdf5;";
                    } else {
                        $styleEstado = "padding: 12px 16px; font-size: 13.5px; font-weight: bold; border: 1px solid #e5e7eb; color: #dc2626; background-color: #fef2f2;";
                    }

                    echo "<tr>";
                    echo "<td style='{$styleNombre}'>".htmlspecialchars((string)$nombre)."</td>";
                    echo "<td style='{$styleEstado}'>".htmlspecialchars((string)$estado)."</td>";
                    echo "</tr>";

                    $rowIndex++;
                }
            }

            echo "</table>";
        }, $filename, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    /**
     * Renderiza los beneficiarios en un Excel claro, limpio y con la
     * paleta de marca (índigo) usada en el resto del sistema
     */
    private function excelNativo(array $columnas, array $filas, string $slug): StreamedResponse
    {
        $filename = $slug.'.xls';

        return response()->streamDownload(function () use ($columnas, $filas) {
            echo "<meta charset='utf-8'>";
            echo "<table border='0' cellpadding='0' cellspacing='0' style='font-family: Calibri, Arial, sans-serif; border-collapse: collapse; background-color: #ffffff;'>";

            // ── Título ──
            $totalCols = count($columnas);
            echo "<tr><td colspan='{$totalCols}' style='background-color: #4f46e5; color: #ffffff; font-size: 15px; font-weight: bold; padding: 12px 14px; border: none;'>Fundación Don Benjamín</td></tr>";
            echo "<tr><td colspan='{$totalCols}' style='background-color: #eef2ff; color: #4338ca; font-size: 11.5px; font-weight: bold; padding: 6px 14px; border: none;'>Reporte de beneficiarios &middot; Generado el ".now()->locale('es')->isoFormat('D [de] MMMM [de] YYYY, HH:mm')."</td></tr>";
            echo "<tr><td colspan='{$totalCols}' style='border: none; background-color: #ffffff; padding: 6px;'></td></tr>";

            // ── Encabezado ──
            echo "<tr>";
            foreach ($columnas as $col) {
                echo "<td style='background-color: #eef2ff; color: #3730a3; padding: 9px 12px; text-align: left; font-size: 11px; font-weight: bold; text-transform: uppercase; border: 1px solid #c7d2fe;'>".htmlspecialchars($col)."</td>";
            }
            echo "</tr>";

            // ── Filas de datos ──
            foreach ($filas as $rowIndex => $fila) {
                $bgAlt = $rowIndex % 2 === 0 ? '#ffffff' : '#f9fafb';
                echo "<tr>";
                foreach ($fila as $index => $celda) {
                    $styleCelda = "padding: 8px 12px; color: #1f2937; background-color: {$bgAlt}; font-size: 12px; border: 1px solid #e5e7eb;";

                    // Conserva tus etiquetas de Estado (Activo / Inactivo) con colores suaves
                    if ($index === 7) {
                        if ($celda === 'Activo') {
                            $styleCelda = "padding: 8px 12px; font-size: 12px; font-weight: bold; border: 1px solid #e5e7eb; color: #059669; background-color: #ecfdf5;";
                        } else {
                            $styleCelda = "padding: 8px 12px; font-size: 12px; font-weight: bold; border: 1px solid #e5e7eb; color: #dc2626; background-color: #fef2f2;";
                        }
                    }
                    echo "<td style='{$styleCelda}'>".htmlspecialchars((string)$celda)."</td>";
                }
                echo "</tr>";
            }

            echo "</table>";
        }, $filename, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    private function pdf($rows, string $view, string $slug, ?string $mes)
    {
        $titulo = $this->tituloConMes(ucfirst(str_replace('_', ' ', $slug)), $mes);

        $pdf = Pdf::loadView($view, [
            'beneficiarios' => $rows,
            'mes' => $titulo,
        ]);

        $suffix = $mes ? '_'.str_replace(' ', '_', $mes) : '';

        return $pdf->download($slug.$suffix.'.pdf');
    }

    private function tituloConMes(string $base, ?string $mes): string
    {
        if (! $mes) {
            return $base;
        }

        $parsed = $this->parseMes($mes);
        if (! $parsed) {
            return $base;
        }

        $nombreMes = $parsed->locale('es')->isoFormat('MMMM [de] YYYY');

        return $base.' — '.ucfirst($nombreMes);
    }

    private function parseMes(string $str): ?Carbon
    {
        if (preg_match('/^\d{4}-\d{2}$/', $str)) {
            return Carbon::createFromFormat('Y-m', $str);
        }
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $str)) {
            return Carbon::createFromFormat('Y-m-d', $str);
        }
        return null;
    }
}