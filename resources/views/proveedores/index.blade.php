<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h2 class="font-semibold text-xl text-text-100 leading-tight">
                {{ __('Proveedores') }}
            </h2>

            <a href="{{ route('proveedores.create') }}"
               class="inline-flex items-center px-4 py-2 bg-primary-100 text-text-100 text-sm font-medium rounded-md hover:bg-primary-200">
                {{ __('Nuevo proveedor') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-bg-200 border border-bg-300 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    <form method="GET" action="{{ route('proveedores.index') }}" class="flex flex-col sm:flex-row gap-2 mb-6">
                        <x-input type="search" name="buscar" :value="request('buscar')"
                                      placeholder="{{ __('Buscar por nombre, teléfono o correo...') }}"
                                      class="w-full sm:max-w-xs" />
                        <x-button-primary type="submit">{{ __('Buscar') }}</x-button-primary>
                        @if (request('buscar'))
                            <a href="{{ route('proveedores.index') }}"
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

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-bg-300 text-sm">
                            <thead>
                                <tr class="text-left text-text-200">
                                    <th class="py-2 pe-4 font-medium">{{ __('Nombre') }}</th>
                                    <th class="py-2 pe-4 font-medium">{{ __('NIT') }}</th>
                                    <th class="py-2 pe-4 font-medium">{{ __('Teléfono') }}</th>
                                    <th class="py-2 pe-4 font-medium">{{ __('Correo') }}</th>
                                    <th class="py-2 pe-4 font-medium">{{ __('Estado') }}</th>
                                    <th class="py-2 pe-4 font-medium text-end">{{ __('Acciones') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-bg-300">
                                @forelse ($proveedores as $proveedor)
                                    <tr class="hover:bg-bg-300/40">
                                        <td class="py-3 pe-4 font-medium text-text-100">{{ $proveedor->nombre }}</td>
                                        <td class="py-3 pe-4 text-text-200">{{ $proveedor->nit ?? '—' }}</td>
                                        <td class="py-3 pe-4 text-text-200">{{ $proveedor->telefono ?? '—' }}</td>
                                        <td class="py-3 pe-4 text-text-200">{{ $proveedor->correo ?? '—' }}</td>
                                        <td class="py-3 pe-4">
                                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $proveedor->activo ? 'bg-green-900/40 text-green-300' : 'bg-bg-300 text-text-200' }}">
                                                {{ $proveedor->activo ? __('Activo') : __('Inactivo') }}
                                            </span>
                                        </td>
                                        <td class="py-3 pe-4 text-end whitespace-nowrap">
                                            <a href="{{ route('proveedores.edit', $proveedor) }}"
                                               class="text-accent-200 hover:text-text-100 font-medium">
                                                {{ __('Editar') }}
                                            </a>

                                            <x-delete-button :action="route('proveedores.destroy', $proveedor)"
                                                             :message="__('¿Estás seguro de eliminar este proveedor?')" />
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-6 text-center text-text-200">
                                            {{ __('No se encontraron proveedores.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $proveedores->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>