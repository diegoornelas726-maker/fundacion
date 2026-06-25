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
        $tituloPeriodo = 'Reporte';

        if ($periodo) {
            if ($tipo === 'dia') {
                $query->whereDate('created_at', $periodo);
                $tituloPeriodo = 'Día '.Carbon::parse($periodo)->format('d-m-Y');
            } else {
                $year = substr($periodo, 0, 4);
                $month = substr($periodo, 5, 2);
                $query->whereYear('created_at', $year)->whereMonth('created_at', $month);
                $tituloPeriodo = $periodo;
            }
        } else {
            $query->whereYear('created_at', date('Y'))->whereMonth('created_at', date('m'));
            $tituloPeriodo = date('Y-m');
        }

        $rows = $query->latest()->get();
        $formato = $request->input('formato', 'pdf');

        if ($formato === 'excel') {
            $columnas = ['#', 'Nombre Completo', 'CURP', 'Teléfono', 'Colonia', 'Estado', 'Registro'];
            $filas = [];
            foreach ($rows as $r) {
                $filas[] = [
                    $r->id,
                    "$r->nombre $r->apellido_paterno $r->apellido_materno",
                    $r->curp ?? '—',
                    $r->telefono ?? '—',
                    $r->colonia ?? '—',
                    $r->estado,
                    $r->created_at->format('d/m/Y'),
                ];
            }

            return $this->csv($columnas, $filas, 'beneficiarios_'.$tipo.'_'.$periodo);
        }

        // CORRECCIÓN: Apuntamos correctamente a la ruta de la vista de beneficiarios 'beneficiarios.pdf'
        // Si tu archivo está en resources/views/pdf.blade.php cambia 'beneficiarios.pdf' por 'pdf'
        return $this->pdfExport('beneficiarios.pdf', $rows, 'beneficiarios_reporte', $tituloPeriodo);
    }

    public function apoyos(Request $request)
    {
        $query = Apoyo::with('beneficiario');

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

        $tipo = $request->input('tipo_periodo', 'mes');
        $periodo = $request->input('periodo');
        $tituloPeriodo = 'Reporte';

        if ($periodo) {
            if ($tipo === 'dia') {
                $query->whereDate('fecha_apoyo', $periodo);
                $tituloPeriodo = 'Día ' . Carbon::parse($periodo)->format('d-m-Y');
            } else {
                $year = substr($periodo, 0, 4);
                $month = substr($periodo, 5, 2);
                $query->whereYear('fecha_apoyo', $year)->whereMonth('fecha_apoyo', $month);
                $tituloPeriodo = $periodo;
            }
        } else {
            $query->whereYear('fecha_apoyo', date('Y'))->whereMonth('fecha_apoyo', date('m'));
            $tituloPeriodo = date('Y-m');
        }

        $rows = $query->latest('fecha_apoyo')->get();
        $formato = $request->input('formato', 'pdf');

        if ($formato === 'excel') {
            $columnas = ['#', 'Beneficiario', 'Descripción / Beneficio', 'Tipo de Apoyo', 'Monto', 'Fecha de Apoyo', 'Estado'];
            $filas = [];
            foreach ($rows as $r) {
                $nombreCompleto = $r->beneficiario 
                    ? "{$r->beneficiario->nombre} {$r->beneficiario->apellido_paterno} {$r->beneficiario->apellido_materno}"
                    : 'No asignado';

                $filas[] = [
                    $r->id,
                    $nombreCompleto,
                    $r->descripcion ?? '—',
                    $r->tipo_apoyo,
                    $r->monto ? "$" . number_format($r->monto, 2) : '—',
                    Carbon::parse($r->fecha_apoyo)->format('d/m/Y'),
                    $r->estado
                ];
            }

            return $this->csv($columnas, $filas, 'apoyos_'.$tipo.'_'.$periodo);
        }

        $parsed = $this->parseMes($tituloPeriodo);
        if ($parsed) {
            $tituloPeriodo = ucfirst($parsed->locale('es')->isoFormat('MMMM [de] YYYY'));
        }

        $pdf = Pdf::loadView('apoyos.pdf', [
            'apoyos' => $rows,
            'mes' => $tituloPeriodo
        ]);

        return $pdf->download('apoyos_reporte_'.$tipo.'_'.$periodo.'.pdf');
    }

    public function actividades(Request $request)
    {
        $query = Actividad::query();
        $rows = $query->latest()->get();

        return $this->csv(['ID', 'Nombre', 'Fecha'], $rows->toArray(), 'actividades');
    }

    public function asistencia(Request $request)
    {
        $query = Asistencia::query();
        $rows = $query->latest()->get();

        return $this->csv(['ID', 'Fecha'], $rows->toArray(), 'asistencia');
    }

    private function pdfExport(string $view, $rows, string $slug, ?string $mes): \Illuminate\Http\Response
    {
        $view = str_replace('.blade.php', '', $view);
        
        if (! view()->exists($view)) {
            abort(444, "Vista [{$view}] no encontrada.");
        }

        $titulo = $this->tituloConMes(ucfirst(str_replace('_reporte', '', $slug)), $mes);

        $pdf = Pdf::loadView($view, [
            'beneficiarios' => $rows, 
            'mes' => $titulo,
        ]);

        $suffix = $mes ? '_'.str_replace(' ', '_', $mes) : '';

        return $pdf->download($slug.$suffix.'.pdf');
    }

    private function csv(array $columnas, array $filas, string $slug): StreamedResponse
    {
        $filename = $slug.'.csv';

        return response()->streamDownload(function () use ($columnas, $filas) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, $columnas);
            foreach ($filas as $fila) {
                fputcsv($out, $fila);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
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
        if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $str)) {
            return Carbon::createFromFormat('d-m-Y', $str);
        }

        return null;
    }
}