<x-app-layout>
    <x-slot name="header">
        <h1>Editar apoyo</h1>
    </x-slot>

    @include('apoyos._form', [
        'apoyo'         => $apoyo,
        'beneficiarios' => $beneficiarios,
        'route'         => route('apoyos.update', $apoyo),
        'method'        => 'PUT',
    ])
</x-app-layout>