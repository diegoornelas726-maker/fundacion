<?php

namespace App\Http\Controllers;

use App\Models\Apoyo;
use App\Models\Beneficiario;
use Illuminate\Http\Request;

class ApoyoController extends Controller
{
    public function index(Request $request)
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

        if ($request->filled('tipo')) {
            $query->where('tipo_apoyo', $request->tipo);
        }

        $apoyos = $query->latest('fecha_apoyo')->paginate(15)->withQueryString();

        $tipos = Apoyo::select('tipo_apoyo')->distinct()->orderBy('tipo_apoyo')->pluck('tipo_apoyo');

        return view('apoyos.index', compact('apoyos', 'tipos'));
    }

    public function create()
    {
        $beneficiarios = Beneficiario::where('estado', 'Activo')
            ->orderBy('apellido_paterno')
            ->get();

        return view('apoyos.create', compact('beneficiarios'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'beneficiario_id' => 'required|exists:beneficiarios,id',
            'tipo_apoyo'      => 'required|string|max:100',
            'descripcion'     => 'nullable|string',
            'fecha_apoyo'     => 'required|date',
            'monto'           => 'nullable|numeric|min:0',
            'estado'          => 'required|in:Entregado,Pendiente,Cancelado',
            'observaciones'   => 'nullable|string',
        ], [
            'beneficiario_id.required' => 'Debes seleccionar un beneficiario.',
            'beneficiario_id.exists'   => 'El beneficiario seleccionado no existe.',
            'tipo_apoyo.required'      => 'El tipo de apoyo es obligatorio.',
            'fecha_apoyo.required'     => 'La fecha del apoyo es obligatoria.',
            'monto.numeric'            => 'El monto debe ser un número válido.',
        ]);

        Apoyo::create($request->all());

        return redirect()->route('apoyos.index')
            ->with('success', 'Apoyo registrado correctamente.');
    }

    public function edit(Apoyo $apoyo)
    {
        $beneficiarios = Beneficiario::orderBy('apellido_paterno')->get();

        return view('apoyos.edit', compact('apoyo', 'beneficiarios'));
    }

    public function update(Request $request, Apoyo $apoyo)
    {
        $request->validate([
            'beneficiario_id' => 'required|exists:beneficiarios,id',
            'tipo_apoyo'      => 'required|string|max:100',
            'descripcion'     => 'nullable|string',
            'fecha_apoyo'     => 'required|date',
            'monto'           => 'nullable|numeric|min:0',
            'estado'          => 'required|in:Entregado,Pendiente,Cancelado',
            'observaciones'   => 'nullable|string',
        ], [
            'beneficiario_id.required' => 'Debes seleccionar un beneficiario.',
            'tipo_apoyo.required'      => 'El tipo de apoyo es obligatorio.',
            'fecha_apoyo.required'     => 'La fecha del apoyo es obligatoria.',
        ]);

        $apoyo->update($request->all());

        return redirect()->route('apoyos.index')
            ->with('success', 'Apoyo actualizado correctamente.');
    }

    public function destroy(Apoyo $apoyo)
    {
        $apoyo->delete();

        return redirect()->route('apoyos.index')
            ->with('success', 'Apoyo eliminado correctamente.');
    }
}