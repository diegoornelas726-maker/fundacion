<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\Apoyo;
use App\Models\Asistencia;
use App\Models\Beneficiario;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard', [
            'totalBeneficiarios' => Beneficiario::count(),
            'totalApoyos' => Apoyo::count(),
            'totalActividades' => Actividad::count(),
            'asistenciasHoy' => Asistencia::whereDate('fecha', Carbon::today())
                ->where('presente', true)->count(),
            'asistenciasMes' => Asistencia::whereYear('fecha', Carbon::now()->year)
                ->whereMonth('fecha', Carbon::now()->month)
                ->where('presente', true)->count(),
        ]);
    }
}
