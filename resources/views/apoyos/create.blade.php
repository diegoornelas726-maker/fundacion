<x-app-layout>
    <x-slot name="header">
        <h1>Nuevo apoyo</h1>
    </x-slot>

    @include('apoyos._form', [
        'apoyo'         => new App\Models\Apoyo,
        'beneficiarios' => $beneficiarios,
        'route'         => route('apoyos.store'),
        'method'        => 'POST',
    ])
</x-app-layout>