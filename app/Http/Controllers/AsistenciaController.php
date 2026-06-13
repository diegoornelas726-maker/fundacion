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

        $presentesCount = $registros->where('presente', true)->count()
            + $visitantes->where('presente', true)->count();

        return view('asistencia.index', [
            'fecha' => $fecha,
            'buscar' => $buscar,
            'beneficiarios' => $beneficiarios,
            'registros' => $registros,
            'visitantes' => $visitantes,
            'presentesCount' => $presentesCount,
        ]);
    }

    /**
     * Guarda la asistencia del día (presente/ausente por beneficiario).
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'fecha' => ['required', 'date'],
            'estado' => ['array'],
            'estado.*' => ['in:0,1'],
        ]);

        $fecha = Carbon::parse($data['fecha'])->toDateString();
        $estado = $request->input('estado', []);

        $ids = Beneficiario::pluck('id');

        foreach ($ids as $id) {
            $isPres = isset($estado[$id]) && (string) $estado[$id] === '1';
            Asistencia::updateOrCreate(
                ['fecha' => $fecha, 'beneficiario_id' => $id],
                ['presente' => $isPres],
            );
        }

        return redirect()
            ->route('asistencia.index', ['fecha' => $fecha])
            ->with('success', 'Asistencia guardada correctamente.');
    }

    /**
     * Registra un visitante que no es beneficiario.
     */
    public function storeVisitante(Request $request)
    {
        $data = $request->validate([
            'fecha' => ['required', 'date'],
            'nombre_visitante' => ['required', 'string', 'max:255'],
        ]);

        Asistencia::create([
            'fecha' => Carbon::parse($data['fecha'])->toDateString(),
            'nombre_visitante' => $data['nombre_visitante'],
            'presente' => true,
        ]);

        return redirect()
            ->route('asistencia.index', ['fecha' => $data['fecha']])
            ->with('success', 'Visitante registrado.');
    }

    /**
     * Elimina un registro de asistencia de visitante.
     */
    public function destroyVisitante(Asistencia $asistencia)
    {
        $fecha = $asistencia->fecha->toDateString();
        $asistencia->delete();

        return redirect()
            ->route('asistencia.index', ['fecha' => $fecha])
            ->with('success', 'Visitante eliminado.');
    }

    /**
     * Historial de asistencias agrupado por fecha.
     */
    public function historial()
    {
        $fechas = Asistencia::query()
            ->selectRaw('fecha,
                SUM(CASE WHEN presente = 1 THEN 1 ELSE 0 END) as presentes,
                COUNT(*) as total')
            ->groupBy('fecha')
            ->orderByDesc('fecha')
            ->paginate(15);

        return view('asistencia.historial', ['fechas' => $fechas]);
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

    private function parseFecha(?string $value): Carbon
    {
        try {
            return $value ? Carbon::parse($value)->startOfDay() : Carbon::today();
        } catch (\Throwable $e) {
            return Carbon::today();
        }
    }
}
