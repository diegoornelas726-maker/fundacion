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
     * Exporta el historial de asistencias con colores fijos estilo Modo Oscuro de la App
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

        $columnasTabla1 = ['Día', 'Mes', 'Año', 'Nombre', 'Estado'];
        $filasTabla1 = [];

        foreach ($registros as $reg) {
            $fechaReg = $reg->fecha ? Carbon::parse($reg->fecha) : null;
            
            $dia = $fechaReg ? $fechaReg->format('d') : '—';
            $mes = $fechaReg ? ucfirst($fechaReg->locale('es')->isoFormat('MMMM')) : '—';
            $ano = $fechaReg ? $fechaReg->format('Y') : '—';

            // Lógica correcta obtenida de tu AsistenciaController para determinar nombres
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

            $filasTabla1[] = [$dia, $mes, $ano, $nombre, $estado];
        }

        $columnasFinales = ['Día', 'Mes', 'Año', 'Nombre', 'Estado', '', 'Concepto', 'Total'];
        $filasFinales = [];

        $resumenTotales = [
            0 => ['Total Presentes', $totalPresentes],
            1 => ['Total Ausentes', $totalAusentes]
        ];

        $maxFilas = max(count($filasTabla1), count($resumenTotales));

        for ($i = 0; $i < $maxFilas; $i++) {
            $fila = [];

            if (isset($filasTabla1[$i])) {
                $fila = $filasTabla1[$i];
            } else {
                $fila = ['', '', '', '', ''];
            }

            $fila[] = ''; // Separador

            if (isset($resumenTotales[$i])) {
                $fila[] = $resumenTotales[$i][0];
                $fila[] = $resumenTotales[$i][1];
            } else {
                $fila[] = '';
                $fila[] = '';
            }

            $filasFinales[] = $fila;
        }

        return $this->excelNativoAsistencia($columnasFinales, $filasFinales, 'reporte_asistencias');
    }

    public function actividades(Request $request)
    {
        return redirect()->back(); 
    }

    /**
     * Renderiza las asistencias manteniendo el Modo Oscuro de la app en Excel de forma permanente
     */
    private function excelNativoAsistencia(array $columnas, array $filas, string $slug): StreamedResponse
    {
        $filename = $slug.'.xls';

        return response()->streamDownload(function () use ($columnas, $filas) {
            echo "<meta charset='utf-8'>";
            // Forzamos un contenedor oscuro global con bordes finos e idénticos a tu layout
            echo "<table border='1' style='font-family: Arial, sans-serif; border-collapse: collapse; background-color: #121214; border-color: #2e2e33;'>";
            
            // Encabezados
            echo "<tr style='font-weight: bold;'>";
            foreach ($columnas as $index => $col) {
                if ($index < 5) {
                    echo "<td style='background-color: #1a1a1e; color: #a1a1aa; padding: 8px 12px; text-align: left; font-size: 12px; text-transform: uppercase;'>".htmlspecialchars($col)."</td>";
                } elseif ($index == 5) {
                    echo "<td style='border: none; background-color: #121214; width: 30px;'></td>";
                } else {
                    echo "<td style='background-color: #1e1b4b; color: #818cf8; padding: 8px 12px; text-align: left; font-size: 12px; text-transform: uppercase;'>".htmlspecialchars($col)."</td>";
                }
            }
            echo "</tr>";
            
            // Celdas de datos con fondo oscuro e inmunidad ante cambios de tema
            foreach ($filas as $fila) {
                echo "<tr>";
                foreach ($fila as $index => $celda) {
                    if ($index < 5) {
                        $styleCelda = "padding: 8px 12px; color: #e4e4e7; background-color: #18181a; font-size: 13.5px;";
                        
                        if ($index === 4) {
                            if ($celda === 'Presente') {
                                $styleCelda .= " color: #34d399; background-color: rgba(52, 211, 153, 0.1); font-weight: bold;";
                            } elseif ($celda === 'Ausente') {
                                $styleCelda .= " color: #f87171; background-color: rgba(248, 113, 113, 0.1); font-weight: bold;";
                            }
                        }
                        echo "<td style='{$styleCelda}'>".htmlspecialchars((string)$celda)."</td>";
                    } elseif ($index == 5) {
                        echo "<td style='border: none; background-color: #121214;'></td>";
                    } else {
                        $styleTotales = "padding: 8px 12px; font-size: 13.5px; background-color: #1c1917; font-weight: bold;";
                        if (strpos((string)$fila[6], 'Presentes') !== false) {
                            $styleTotales .= " color: #34d399;";
                        } elseif (strpos((string)$fila[6], 'Ausentes') !== false) {
                            $styleTotales .= " color: #f87171;";
                        } else {
                            $styleTotales .= " color: #a1a1aa;";
                        }
                        echo "<td style='{$styleTotales}'>".htmlspecialchars((string)$celda)."</td>";
                    }
                }
                echo "</tr>";
            }
            
            echo "</table>";
        }, $filename, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    /**
     * Renderiza los beneficiarios manteniendo la consistencia visual oscura
     */
    private function excelNativo(array $columnas, array $filas, string $slug): StreamedResponse
    {
        $filename = $slug.'.xls';

        return response()->streamDownload(function () use ($columnas, $filas) {
            echo "<meta charset='utf-8'>";
            echo "<table border='1' style='font-family: Arial, sans-serif; border-collapse: collapse; background-color: #121214; border-color: #2e2e33;'>";
            
            // Encabezado
            echo "<tr style='background-color: #1a1a1e; font-weight: bold;'>";
            foreach ($columnas as $col) {
                echo "<td style='padding: 8px 12px; color: #a1a1aa; text-align: left; font-size: 12px; text-transform: uppercase;'>".htmlspecialchars($col)."</td>";
            }
            echo "</tr>";
            
            // Filas estables
            foreach ($filas as $fila) {
                echo "<tr>";
                foreach ($fila as $index => $celda) {
                    $styleCelda = "padding: 8px 12px; color: #e4e4e7; background-color: #18181a; font-size: 13.5px;";
                    // Conserva tus etiquetas de Estado (Activo / Inactivo) con colores armoniosos
                    if ($index === 7) {
                        if ($celda === 'Activo') {
                            $styleCelda .= " color: #34d399; font-weight: bold;";
                        } else {
                            $styleCelda .= " color: #ef4444; font-weight: bold;";
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