<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h2 class="font-semibold text-xl text-text-100 leading-tight">
                {{ __('Categorías') }}
            </h2>

            <a href="{{ route('categorias.create') }}"
               class="inline-flex items-center px-4 py-2 bg-primary-100 text-text-100 text-sm font-medium rounded-md hover:bg-primary-200">
                {{ __('Nueva categoría') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-bg-200 border border-bg-300 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    <!-- Búsqueda -->
                    <form method="GET" action="{{ route('categorias.index') }}" class="flex flex-col sm:flex-row gap-2 mb-6">
                        <x-input type="search" name="buscar" :value="request('buscar')"
                                      placeholder="{{ __('Buscar por nombre...') }}"
                                      class="w-full sm:max-w-xs" />
                        <x-button-primary type="submit">{{ __('Buscar') }}</x-button-primary>
                        @if (request('buscar'))
                            <a href="{{ route('categorias.index') }}"
                               class="inline-flex items-center px-4 py-2 bg-bg-200 border border-bg-300 text-sm font-medium rounded-md text-text-100 hover:bg-bg-300">
                                {{ __('Limpiar') }}
                            </a>
                        @endif
                    </form>

                    <!-- Listado -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-bg-300 text-sm">
                            <thead>
                                <tr class="text-left text-text-200">
                                    <th class="py-2 pe-4 font-medium">{{ __('Nombre') }}</th>
                                    <th class="py-2 pe-4 font-medium">{{ __('Descripción') }}</th>
                                    <th class="py-2 pe-4 font-medium text-end">{{ __('Productos') }}</th>
                                    <th class="py-2 pe-4 font-medium">{{ __('Estado') }}</th>
                                    <th class="py-2 pe-4 font-medium text-end">{{ __('Acciones') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-bg-300">
                                @forelse ($categorias as $categoria)
                                    <tr class="hover:bg-bg-300/40">
                                        <td class="py-3 pe-4 font-medium text-text-100">{{ $categoria->nombre }}</td>
                                        <td class="py-3 pe-4 text-text-200">{{ $categoria->descripcion ?? '—' }}</td>
                                        <td class="py-3 pe-4 text-text-200 text-end">{{ $categoria->productos_count }}</td>
                                        <td class="py-3 pe-4">
                                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $categoria->activo ? 'bg-green-900/40 text-green-300' : 'bg-bg-300 text-text-200' }}">
                                                {{ $categoria->activo ? __('Activa') : __('Inactiva') }}
                                            </span>
                                        </td>
                                        <td class="py-3 pe-4 text-end whitespace-nowrap">
                                            <a href="{{ route('categorias.edit', $categoria) }}"
                                               class="text-accent-200 hover:text-text-100 font-medium">
                                                {{ __('Editar') }}
                                            </a>

                                            <x-delete-button :action="route('categorias.destroy', $categoria)"
                                                             :message="__('¿Estás seguro de eliminar esta categoría?')" />
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-6 text-center text-text-200">
                                            {{ __('No se encontraron categorías.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <div class="mt-4">
                        {{ $categorias->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>