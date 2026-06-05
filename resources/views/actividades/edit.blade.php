<x-app-layout>
    <x-slot name="header">
        <h1>Editar actividad</h1>
    </x-slot>

    @include('actividades._form', [
        'actividad' => $actividad,
        'route'     => route('actividades.update', $actividad),
        'method'    => 'PUT',
    ])
</x-app-layout>