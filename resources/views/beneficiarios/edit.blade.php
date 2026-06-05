<x-app-layout>
    <x-slot name="header">
        <h1>Editar beneficiario</h1>
    </x-slot>

    @include('beneficiarios._form', ['beneficiario' => $beneficiario, 'route' => route('beneficiarios.update', $beneficiario), 'method' => 'PUT'])
</x-app-layout>