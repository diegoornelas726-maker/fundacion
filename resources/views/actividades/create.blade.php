<x-app-layout>
    <x-slot name="header">
        <h1>Nueva actividad</h1>
    </x-slot>

    @include('actividades._form', [
        'actividad' => new App\Models\Actividad,
        'route'     => route('actividades.store'),
        'method'    => 'POST',
    ])
</x-app-layout>