<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use Illuminate\Http\Request;

class ActividadController extends Controller
{
    public function index(Request $request)
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

        $actividades = $query->orderBy('fecha_inicio', 'desc')->paginate(15)->withQueryString();

        $tipos = Actividad::select('tipo')->whereNotNull('tipo')->distinct()->orderBy('tipo')->pluck('tipo');

        return view('actividades.index', compact('actividades', 'tipos'));
    }

    public function create()
    {
        return view('actividades.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo'                  => 'required|string|max:200',
            'descripcion'             => 'nullable|string',
            'fecha_inicio'            => 'required|date',
            'fecha_fin'               => 'nullable|date|after_or_equal:fecha_inicio',
            'lugar'                   => 'nullable|string|max:200',
            'tipo'                    => 'nullable|string|max:100',
            'responsable'             => 'nullable|string|max:150',
            'participantes_esperados' => 'nullable|integer|min:1',
            'estado'                  => 'required|in:Programada,En curso,Finalizada,Cancelada',
            'observaciones'           => 'nullable|string',
        ], [
            'titulo.required'         => 'El título es obligatorio.',
            'fecha_inicio.required'   => 'La fecha de inicio es obligatoria.',
            'fecha_fin.after_or_equal'=> 'La fecha de fin debe ser igual o posterior a la de inicio.',
        ]);

        Actividad::create($request->all());

        return redirect()->route('actividades.index')
            ->with('success', 'Actividad registrada correctamente.');
    }

    public function edit(Actividad $actividad)
    {
        return view('actividades.edit', compact('actividad'));
    }

    public function update(Request $request, Actividad $actividad)
    {
        $request->validate([
            'titulo'                  => 'required|string|max:200',
            'descripcion'             => 'nullable|string',
            'fecha_inicio'            => 'required|date',
            'fecha_fin'               => 'nullable|date|after_or_equal:fecha_inicio',
            'lugar'                   => 'nullable|string|max:200',
            'tipo'                    => 'nullable|string|max:100',
            'responsable'             => 'nullable|string|max:150',
            'participantes_esperados' => 'nullable|integer|min:1',
            'estado'                  => 'required|in:Programada,En curso,Finalizada,Cancelada',
            'observaciones'           => 'nullable|string',
        ], [
            'titulo.required'          => 'El título es obligatorio.',
            'fecha_inicio.required'    => 'La fecha de inicio es obligatoria.',
            'fecha_fin.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la de inicio.',
        ]);

        $actividad->update($request->all());

        return redirect()->route('actividades.index')
            ->with('success', 'Actividad actualizada correctamente.');
    }

    public function destroy(Actividad $actividad)
    {
        $actividad->delete();

        return redirect()->route('actividades.index')
            ->with('success', 'Actividad eliminada correctamente.');
    }
}