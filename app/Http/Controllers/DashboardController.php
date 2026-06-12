<?php

namespace App\Http\Controllers;

use App\Models\Beneficiario;
use App\Models\Apoyo;
use App\Models\Actividad;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard', [
            'totalBeneficiarios' => Beneficiario::count(),
            'totalApoyos'        => Apoyo::count(),
            'totalActividades'   => Actividad::count(),
        ]);
    }
}