<x-app-layout>
    <x-slot name="header">
        <h1>Nuevo beneficiario</h1>
    </x-slot>

    @include('beneficiarios._form', ['beneficiario' => new App\Models\Beneficiario, 'route' => route('beneficiarios.store'), 'method' => 'POST'])
</x-app-layout>