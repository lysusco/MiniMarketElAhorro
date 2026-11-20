<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h2 class="font-semibold text-xl text-text-100 leading-tight">
                {{ __('Clientes') }}
            </h2>

            <a href="{{ route('clientes.create') }}"
               class="inline-flex items-center px-4 py-2 bg-primary-100 text-text-100 text-sm font-medium rounded-md hover:bg-primary-200">
                {{ __('Nuevo cliente') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-bg-200 border border-bg-300 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    <!-- Búsqueda -->
                    <form method="GET" action="{{ route('clientes.index') }}" class="flex flex-col sm:flex-row flex-wrap gap-2 mb-6">
                        <x-input type="search" name="buscar" :value="request('buscar')"
                                      placeholder="{{ __('Buscar por nombre o identificación...') }}"
                                      class="w-full sm:max-w-xs" />

                        <x-button-primary type="submit">{{ __('Buscar') }}</x-button-primary>
                        @if (request('buscar'))
                            <a href="{{ route('clientes.index') }}"
                               class="inline-flex items-center px-4 py-2 bg-bg-200 border border-bg-300 text-sm font-medium rounded-md text-text-100 hover:bg-bg-300">
                                {{ __('Limpiar') }}
                            </a>
                        @endif
                    </form>

                    @if (session('success'))
                        <div class="mb-4">
                            <x-alert type="success">{{ session('success') }}</x-alert>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="mb-4">
                            <x-alert type="danger">{{ session('error') }}</x-alert>
                        </div>
                    @endif

                    <!-- Listado -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-bg-300 text-sm">
                            <thead>
                                <tr class="text-left text-text-200">
                                    <th class="py-2 pe-4 font-medium">{{ __('Nombre') }}</th>
                                    <th class="py-2 pe-4 font-medium">{{ __('Identificación') }}</th>
                                    <th class="py-2 pe-4 font-medium">{{ __('Teléfono') }}</th>
                                    <th class="py-2 pe-4 font-medium text-end">{{ __('Saldo') }}</th>
                                    <th class="py-2 pe-4 font-medium text-end">{{ __('Crédito disponible') }}</th>
                                    <th class="py-2 pe-4 font-medium">{{ __('Estado') }}</th>
                                    <th class="py-2 pe-4 font-medium text-end">{{ __('Acciones') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-bg-300">
                                @forelse ($clientes as $cliente)
                                    <tr class="hover:bg-bg-300/40">
                                        <td class="py-3 pe-4 font-medium text-text-100">{{ $cliente->nombre }}</td>
                                        <td class="py-3 pe-4 text-text-200">{{ $cliente->identificacion }}</td>
                                        <td class="py-3 pe-4 text-text-200">{{ $cliente->telefono ?? '—' }}</td>
                                        <td class="py-3 pe-4 text-text-200 text-end">{{ number_format($cliente->saldo, 2) }}</td>
                                        <td class="py-3 pe-4 text-text-200 text-end">{{ number_format($cliente->creditoDisponible(), 2) }}</td>
                                        <td class="py-3 pe-4">
                                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $cliente->activo ? 'bg-green-100 text-green-700' : 'bg-bg-300 text-text-200' }}">
                                                {{ $cliente->activo ? __('Activo') : __('Inactivo') }}
                                            </span>
                                        </td>
                                        <td class="py-3 pe-4 text-end whitespace-nowrap">
                                            <a href="{{ route('clientes.edit', $cliente) }}"
                                               class="text-accent-200 hover:text-text-100 font-medium">
                                                {{ __('Editar') }}
                                            </a>

                                            <form method="POST" action="{{ route('clientes.toggleActivo', $cliente) }}"
                                                  class="inline ms-3">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="text-text-200 hover:text-text-100 font-medium">
                                                    {{ $cliente->activo ? __('Desactivar') : __('Activar') }}
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-6 text-center text-text-200">
                                            {{ __('No se encontraron clientes.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <div class="mt-4">
                        {{ $clientes->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
