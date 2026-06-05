<?php

namespace App\Http\Controllers;

use App\Models\Beneficiario;
use Illuminate\Http\Request;

class BeneficiarioController extends Controller
{
    public function index(Request $request)
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

        $beneficiarios = $query->latest()->paginate(15)->withQueryString();

        return view('beneficiarios.index', compact('beneficiarios'));
    }

    public function create()
    {
        return view('beneficiarios.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'           => 'required|string|max:100',
            'apellido_paterno' => 'required|string|max:100',
            'apellido_materno' => 'nullable|string|max:100',
            'fecha_nacimiento' => 'nullable|date|before:today',
            'sexo'             => 'nullable|in:Masculino,Femenino,Otro',
            'telefono'         => 'nullable|string|max:20',
            'direccion'        => 'nullable|string|max:255',
            'colonia'          => 'nullable|string|max:100',
            'curp'             => 'nullable|string|size:18|unique:beneficiarios,curp',
            'estado'           => 'required|in:Activo,Inactivo',
            'observaciones'    => 'nullable|string',
        ], [
            'nombre.required'           => 'El nombre es obligatorio.',
            'apellido_paterno.required' => 'El apellido paterno es obligatorio.',
            'curp.size'                 => 'La CURP debe tener exactamente 18 caracteres.',
            'curp.unique'               => 'Esta CURP ya está registrada.',
            'fecha_nacimiento.before'   => 'La fecha de nacimiento debe ser anterior a hoy.',
        ]);

        Beneficiario::create($request->all());

        return redirect()->route('beneficiarios.index')
            ->with('success', 'Beneficiario registrado correctamente.');
    }

    public function edit(Beneficiario $beneficiario)
    {
        return view('beneficiarios.edit', compact('beneficiario'));
    }

    public function update(Request $request, Beneficiario $beneficiario)
    {
        $request->validate([
            'nombre'           => 'required|string|max:100',
            'apellido_paterno' => 'required|string|max:100',
            'apellido_materno' => 'nullable|string|max:100',
            'fecha_nacimiento' => 'nullable|date|before:today',
            'sexo'             => 'nullable|in:Masculino,Femenino,Otro',
            'telefono'         => 'nullable|string|max:20',
            'direccion'        => 'nullable|string|max:255',
            'colonia'          => 'nullable|string|max:100',
            'curp'             => 'nullable|string|size:18|unique:beneficiarios,curp,' . $beneficiario->id,
            'estado'           => 'required|in:Activo,Inactivo',
            'observaciones'    => 'nullable|string',
        ], [
            'nombre.required'           => 'El nombre es obligatorio.',
            'apellido_paterno.required' => 'El apellido paterno es obligatorio.',
            'curp.size'                 => 'La CURP debe tener exactamente 18 caracteres.',
            'curp.unique'               => 'Esta CURP ya está registrada.',
        ]);

        $beneficiario->update($request->all());

        return redirect()->route('beneficiarios.index')
            ->with('success', 'Beneficiario actualizado correctamente.');
    }

    public function destroy(Beneficiario $beneficiario)
    {
        $beneficiario->delete();

        return redirect()->route('beneficiarios.index')
            ->with('success', 'Beneficiario eliminado correctamente.');
    }
}