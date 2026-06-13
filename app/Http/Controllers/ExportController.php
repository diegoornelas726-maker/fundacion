<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\Apoyo;
use App\Models\Beneficiario;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
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

        $rows = $query->latest()->get();

        $columnas = ['#', 'Nombre completo', 'CURP', 'Teléfono', 'Colonia', 'Estado', 'Registro'];
        $filas = $rows->map(fn ($b) => [
            $b->id,
            $b->nombre_completo,
            $b->curp ?? '—',
            $b->telefono ?? '—',
            $b->colonia ?? '—',
            $b->estado,
            optional($b->created_at)->format('d/m/Y'),
        ])->all();

        return $this->salida($request, 'Listado de beneficiarios', $columnas, $filas, 'beneficiarios');
    }

    public function apoyos(Request $request)
    {
        $query = Apoyo::with('beneficiario');

        if ($request->filled('buscar')) {
            $b = $request->buscar;
            $query->where(function ($q) use ($b) {
                $q->where('tipo_apoyo', 'like', "%$b%")
                    ->orWhereHas('beneficiario', fn ($q2) => $q2->where('nombre', 'like', "%$b%")
                        ->orWhere('apellido_paterno', 'like', "%$b%"));
            });
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('tipo')) {
            $query->where('tipo_apoyo', $request->tipo);
        }

        $rows = $query->latest('fecha_apoyo')->get();

        $columnas = ['#', 'Beneficiario', 'Tipo de apoyo', 'Fecha', 'Monto', 'Estado'];
        $filas = $rows->map(fn ($a) => [
            $a->id,
            $a->beneficiario?->nombre_completo ?? '—',
            $a->tipo_apoyo,
            optional($a->fecha_apoyo)->format('d/m/Y'),
            $a->monto ? '$'.number_format($a->monto, 2) : '—',
            $a->estado,
        ])->all();

        return $this->salida($request, 'Listado de apoyos', $columnas, $filas, 'apoyos');
    }

    public function actividades(Request $request)
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

        $rows = $query->orderBy('fecha_inicio', 'desc')->get();

        $columnas = ['#', 'Título', 'Tipo', 'Fecha inicio', 'Fecha fin', 'Lugar', 'Responsable', 'Estado'];
        $filas = $rows->map(fn ($a) => [
            $a->id,
            $a->titulo,
            $a->tipo ?? '—',
            optional($a->fecha_inicio)->format('d/m/Y'),
            $a->fecha_fin ? $a->fecha_fin->format('d/m/Y') : '—',
            $a->lugar ?? '—',
            $a->responsable ?? '—',
            $a->estado,
        ])->all();

        return $this->salida($request, 'Listado de actividades', $columnas, $filas, 'actividades');
    }

    private function salida(Request $request, string $titulo, array $columnas, array $filas, string $slug)
    {
        if ($request->input('formato') === 'excel') {
            return $this->csv($columnas, $filas, $slug);
        }

        $pdf = Pdf::loadView('exports.tabla', compact('titulo', 'columnas', 'filas'))
            ->setPaper('a4', count($columnas) > 6 ? 'landscape' : 'portrait');

        return $pdf->download($slug.'-'.now()->format('Y-m-d').'.pdf');
    }

    private function csv(array $columnas, array $filas, string $slug): StreamedResponse
    {
        $filename = $slug.'-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($columnas, $filas) {
            $out = fopen('php://output', 'w');
            // BOM para que Excel reconozca UTF-8 (acentos)
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, $columnas);
            foreach ($filas as $fila) {
                fputcsv($out, $fila);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
