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

        // Filtro Temporal Simplificado (Día o Mes)
        $tipo = $request->input('tipo_periodo', 'mes');
        $periodo = $request->input('periodo');
        $tituloPeriodo = 'Reporte';

        if ($periodo) {
            if ($tipo === 'dia') {
                $query->whereDate('created_at', $periodo);
                $tituloPeriodo = 'Día ' . Carbon::parse($periodo)->format('d-m-Y');
            } else {
                $query->whereYear('created_at', substr($periodo, 0, 4))
                      ->whereMonth('created_at', substr($periodo, 5, 2));
                $parsed = $this->parseMes($periodo);
                $tituloPeriodo = $parsed ? $parsed->locale('es')->isoFormat('MMMM [de] YYYY') : $periodo;
            }
        }

        $rows = $query->latest()->get();
        $formato = $request->input('formato', 'pdf');

        if ($formato === 'excel') {
            $columnas = ['#', 'Nombre completo', 'CURP', 'Teléfono', 'Colonia', 'Estado', 'Fecha Registro'];
            $filas = [];
            foreach ($rows as $r) {
                $filas[] = [
                    $r->id,
                    "$r->nombre $r->apellido_paterno $r->apellido_materno",
                    $r->curp,
                    $r->telefono,
                    $r->colonia,
                    $r->estado,
                    $r->created_at->format('d/m/Y')
                ];
            }
            return $this->csv($columnas, $filas, 'beneficiarios_' . $tipo . '_' . $periodo);
        }

        $pdf = Pdf::loadView('beneficiarios.pdf', [
            'beneficiarios' => $rows, 
            'mes' => $tituloPeriodo
        ]);

        return $pdf->download('beneficiarios_' . $tipo . '_' . $periodo . '.pdf');
    }

    public function apoyos(Request $request)
    {
        $query = Apoyo::query();

        if ($request->filled('buscar')) {
            $b = $request->buscar;
            $query->where(function ($q) use ($b) {
                $q->where('nombre', 'like', "%$b%")
                    ->orWhere('tipo', 'like', "%$b%")
                    ->orWhere('descripcion', 'like', "%$b%");
            });
        }

        $rows = $query->latest()->get();

        $columnas = ['#', 'Nombre del apoyo', 'Tipo', 'Descripción', 'Cantidad', 'Fecha Registro'];
        $filas = [];
        foreach ($rows as $r) {
            $filas[] = [$r->id, $r->nombre, $r->tipo, $r->descripcion, $r->cantidad, $r->created_at->format('d/m/Y')];
        }

        return $this->descargarPdfOCsv($rows, 'apoyos.pdf', $columnas, $filas, 'apoyos_reporte', $request->input('mes'));
    }

    public function actividades(Request $request)
    {
        $query = Actividad::query();

        if ($request->filled('buscar')) {
            $b = $request->buscar;
            $query->where(function ($q) use ($b) {
                $q->where('nombre', 'like', "%$b%")
                    ->orWhere('descripcion', 'like', "%$b%")
                    ->orWhere('lugar', 'like', "%$b%");
            });
        }
        if ($request->filled('mes')) {
            $mes = $this->parseMes($request->mes);
            if ($mes) {
                $query->whereYear('fecha', $mes->year)
                    ->whereMonth('fecha', $mes->month);
            }
        }

        $rows = $query->latest()->get();

        $columnas = ['#', 'Nombre de la actividad', 'Descripción', 'Fecha', 'Hora', 'Lugar', 'Cupo'];
        $filas = [];
        foreach ($rows as $r) {
            $filas[] = [$r->id, $r->nombre, $r->descripcion, Carbon::parse($r->fecha)->format('d/m/Y'), $r->hora, $r->lugar, $r->cupo];
        }

        return $this->descargarPdfOCsv($rows, 'actividades.pdf', $columnas, $filas, 'actividades_reporte', $request->input('mes'));
    }

    public function asistencia(Request $request)
    {
        $query = Asistencia::query()->with('beneficiario');

        if ($request->filled('fecha')) {
            $query->whereDate('fecha', $request->fecha);
        } else {
            $query->whereDate('fecha', Carbon::today());
        }

        $rows = $query->get();

        $columnas = ['#', 'Nombre', 'Tipo', 'Estado', 'Fecha'];
        $filas = [];
        foreach ($rows as $i => $r) {
            $nombre = $r->beneficiario_id ? ($r->beneficiario->nombre.' '.$r->beneficiario->apellido_paterno) : $r->nombre_visitante;
            $tipo = $r->beneficiario_id ? 'Beneficiario' : 'Visitante';
            $estado = $r->presente ? 'Presente' : 'Ausente';
            $filas[] = [$i + 1, $nombre, $tipo, $estado, Carbon::parse($r->fecha)->format('d/m/Y')];
        }

        $fechaTitulo = Carbon::parse($request->input('fecha', Carbon::today()))->locale('es')->isoFormat('dddd, D [de] MMMM YYYY');

        return $this->descargarPdfOCsv($rows, 'asistencia.pdf', $columnas, $filas, 'asistencia_reporte', $fechaTitulo);
    }

    private function descargarPdfOCsv($rows, string $view, array $columnas, array $filas, string $slug, ?string $mes): mixed
    {
        $formato = request('formato', 'pdf');

        if ($formato === 'excel') {
            return $this->csv($columnas, $filas, $slug);
        }

        $titulo = $this->tituloConMes(ucfirst(str_replace('_reporte', '', $slug)), $mes);

        $pdf = Pdf::loadView($view, [
            str_replace('.pdf', '', $view) => $rows,
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

    private function parseMes(?string $value): ?Carbon
    {
        if (! $value || ! preg_match('/^\d{4}-\d{2}$/', $value)) {
            return null;
        }

        return Carbon::createFromFormat('Y-m', $value)->startOfMonth();
    }
}