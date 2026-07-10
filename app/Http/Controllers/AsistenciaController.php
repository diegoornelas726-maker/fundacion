<?php

namespace App\Http\Controllers;

use App\Models\Asistencia;
use App\Models\Beneficiario;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AsistenciaController extends Controller
{
    /**
     * Pantalla para tomar asistencia de un día.
     */
    public function index(Request $request)
    {
        $fecha = $this->parseFecha($request->input('fecha'));
        $buscar = trim((string) $request->input('buscar', ''));

        $beneficiarios = Beneficiario::query()
            ->when($buscar !== '', function ($q) use ($buscar) {
                $q->where(function ($sub) use ($buscar) {
                    $sub->where('nombre', 'like', "%{$buscar}%")
                        ->orWhere('apellido_paterno', 'like', "%{$buscar}%")
                        ->orWhere('apellido_materno', 'like', "%{$buscar}%");
                });
            })
            ->orderBy('nombre')
            ->orderBy('apellido_paterno')
            ->get();

        $registros = Asistencia::whereDate('fecha', $fecha)
            ->whereNotNull('beneficiario_id')
            ->get()
            ->keyBy('beneficiario_id');

        $visitantes = Asistencia::whereDate('fecha', $fecha)
            ->whereNull('beneficiario_id')
            ->orderBy('created_at')
            ->get();

        $presentesCount = $registros->where('presente', true)->count() + $visitantes->where('presente', true)->count();

        return view('asistencia.index', compact(
            'beneficiarios', 
            'registros', 
            'visitantes', 
            'fecha', 
            'buscar', 
            'presentesCount'
        ));
    }

    /**
     * Guarda el listado de asistencia de forma correcta sincronizado con la vista.
     */
    public function store(Request $request)
    {
        $fecha = $this->parseFecha($request->input('fecha'))->format('Y-m-d');
        
        $estados = $request->input('estado', []);
        $observaciones = $request->input('observaciones', []);

        foreach ($estados as $bId => $status) {
            Asistencia::updateOrCreate(
                ['fecha' => $fecha, 'beneficiario_id' => $bId],
                [
                    'presente' => ($status == '1'), 
                    'observaciones' => $observaciones[$bId] ?? null
                ]
            );
        }

        return redirect()->route('asistencia.index', ['fecha' => $fecha])
            ->with('success', 'Asistencias guardadas correctamente.');
    }

    /**
     * Vista agrupada por fechas con filtrado interactivo por Mes y Día independientes.
     */
    public function historial(Request $request)
    {
        $query = Asistencia::selectRaw('fecha, COUNT(CASE WHEN presente = 1 THEN 1 END) as presentes, COUNT(*) as total')
            ->groupBy('fecha');

        // CORRECCIÓN: Filtros independientes de Mes (YYYY-MM) y Día (DD)
        $filtroMes = $request->input('filtro_mes', date('Y-m'));
        $filtroDia = $request->input('filtro_dia');

        if ($filtroMes) {
            $year = substr($filtroMes, 0, 4);
            $month = substr($filtroMes, 5, 2);
            $query->whereYear('fecha', $year)
                  ->whereMonth('fecha', $month);
        }

        if ($filtroDia && $filtroDia !== 'todos') {
            $query->whereDay('fecha', $filtroDia);
        }

        $fechas = $query->orderBy('fecha', 'desc')->paginate(15)->withQueryString();

        return view('asistencia.historial', compact('fechas', 'filtroMes', 'filtroDia'));
    }

    /**
     * Resumen general de asistencia acumulado por beneficiario.
     */
    public function personas()
    {
        $personas = Beneficiario::select('id', 'nombre', 'apellido_paterno', 'apellido_materno')
            ->withCount([
                'asistencias as total_dias',
                'asistencias as dias_presente' => fn ($q) => $q->where('presente', true),
            ])
            ->orderBy('nombre')
            ->orderBy('apellido_paterno')
            ->get()
            ->map(function ($b) {
                $b->porcentaje = $b->total_dias > 0
                    ? round($b->dias_presente / $b->total_dias * 100)
                    : 0;

                return $b;
            });

        return view('asistencia.personas', ['personas' => $personas]);
    }

    /**
     * Exporta la lista de asistencia del día a PDF.
     */
    public function pdf(Request $request)
    {
        $fecha = $this->parseFecha($request->input('fecha'));

        $registros = Asistencia::with('beneficiario')
            ->whereDate('fecha', $fecha)
            ->get();

        $presentes = $registros->filter(fn ($r) => $r->presente)
            ->sortBy('nombre')
            ->values();

        $ausentes = $registros->filter(fn ($r) => ! $r->presente && $r->beneficiario_id)
            ->sortBy('nombre')
            ->values();

        $pdf = Pdf::loadView('asistencia.pdf', [
            'fecha' => $fecha,
            'presentes' => $presentes,
            'ausentes' => $ausentes,
        ]);

        return $pdf->download('asistencia-'.$fecha->format('Y-m-d').'.pdf');
    }

    /**
     * Registra un nuevo visitante para el día actual.
     */
    public function storeVisitante(Request $request)
    {
        $request->validate(['nombre_visitante' => 'required|string|max:150']);
        $fecha = $this->parseFecha($request->input('fecha'))->format('Y-m-d');

        Asistencia::create([
            'fecha' => $fecha,
            'nombre_visitante' => $request->nombre_visitante,
            'presente' => true
        ]);

        return redirect()->route('asistencia.index', ['fecha' => $fecha])
            ->with('success', 'Visitante agregado correctamente.');
    }

    /**
     * Elimina el registro de un visitante.
     */
    public function destroyVisitante(Asistencia $asistencia)
    {
        $fecha = $asistencia->fecha ? $asistencia->fecha->format('Y-m-d') : date('Y-m-d');
        $asistencia->delete();

        return redirect()->route('asistencia.index', ['fecha' => $fecha])
            ->with('success', 'Visitante eliminado correctamente.');
    }

    /**
     * Parsea un string a un objeto Carbon seguro.
     */
    private function parseFecha(?string $value): Carbon
    {
        try {
            return $value ? Carbon::parse($value) : Carbon::today();
        } catch (\Exception $e) {
            return Carbon::today();
        }
    }
}