<?php

use App\Http\Controllers\ActividadController;
use App\Http\Controllers\ApoyoController;
use App\Http\Controllers\AsistenciaController;
use App\Http\Controllers\BeneficiarioController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::middleware('auth')->group(function () {
    // ... rutas existentes ...
    Route::resource('beneficiarios', BeneficiarioController::class);
});
Route::middleware('auth')->group(function () {
    Route::get('asistencia', [AsistenciaController::class, 'index'])->name('asistencia.index');
    Route::post('asistencia', [AsistenciaController::class, 'store'])->name('asistencia.store');
    Route::get('asistencia/historial', [AsistenciaController::class, 'historial'])->name('asistencia.historial');
    Route::get('asistencia/personas', [AsistenciaController::class, 'personas'])->name('asistencia.personas');
    Route::get('asistencia/pdf', [AsistenciaController::class, 'pdf'])->name('asistencia.pdf');

    Route::get('exportar/beneficiarios', [ExportController::class, 'beneficiarios'])->name('beneficiarios.export');
    Route::get('exportar/apoyos', [ApoyoController::class, 'export'])->name('apoyos.export');
    Route::get('exportar/actividades', [ExportController::class, 'actividades'])->name('actividades.export');
    Route::get('exportar/asistencia', [ExportController::class, 'asistencia'])->name('asistencia.export');
    Route::post('asistencia/visitante', [AsistenciaController::class, 'storeVisitante'])->name('asistencia.visitante.store');
    Route::delete('asistencia/visitante/{asistencia}', [AsistenciaController::class, 'destroyVisitante'])->name('asistencia.visitante.destroy');
});

Route::middleware('auth')->group(function () {
    Route::resource('apoyos', ApoyoController::class)->except(['show']);
    Route::resource('actividades', ActividadController::class)
        ->parameters(['actividades' => 'actividad'])
        ->except(['show']);
});

require __DIR__.'/auth.php';
