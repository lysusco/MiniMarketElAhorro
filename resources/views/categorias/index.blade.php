<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Categorías') }}
            </h2>

            <a href="{{ route('categorias.create') }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">
                {{ __('Nueva categoría') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    <!-- Búsqueda -->
                    <form method="GET" action="{{ route('categorias.index') }}" class="flex flex-col sm:flex-row gap-2 mb-6">
                        <x-text-input type="search" name="buscar" :value="request('buscar')"
                                      placeholder="{{ __('Buscar por nombre...') }}"
                                      class="w-full sm:max-w-xs" />
                        <x-primary-button type="submit">{{ __('Buscar') }}</x-primary-button>
                        @if (request('buscar'))
                            <a href="{{ route('categorias.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-50">
                                {{ __('Limpiar') }}
                            </a>
                        @endif
                    </form>

                    <!-- Listado -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead>
                                <tr class="text-left text-gray-500">
                                    <th class="py-2 pe-4 font-medium">{{ __('Nombre') }}</th>
                                    <th class="py-2 pe-4 font-medium">{{ __('Descripción') }}</th>
                                    <th class="py-2 pe-4 font-medium text-end">{{ __('Productos') }}</th>
                                    <th class="py-2 pe-4 font-medium">{{ __('Estado') }}</th>
                                    <th class="py-2 pe-4 font-medium text-end">{{ __('Acciones') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse ($categorias as $categoria)
                                    <tr>
                                        <td class="py-3 pe-4 font-medium text-gray-900">{{ $categoria->nombre }}</td>
                                        <td class="py-3 pe-4 text-gray-600">{{ $categoria->descripcion ?? '—' }}</td>
                                        <td class="py-3 pe-4 text-gray-600 text-end">{{ $categoria->productos_count }}</td>
                                        <td class="py-3 pe-4">
                                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $categoria->activo ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                                {{ $categoria->activo ? __('Activa') : __('Inactiva') }}
                                            </span>
                                        </td>
                                        <td class="py-3 pe-4 text-end whitespace-nowrap">
                                            <a href="{{ route('categorias.edit', $categoria) }}"
                                               class="text-indigo-600 hover:text-indigo-900 font-medium">
                                                {{ __('Editar') }}
                                            </a>

                                            <form method="POST" action="{{ route('categorias.destroy', $categoria) }}"
                                                  class="inline ms-3"
                                                  onsubmit="return confirm('{{ __('¿Estás seguro de eliminar esta categoría?') }}');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 font-medium">
                                                    {{ __('Eliminar') }}
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-6 text-center text-gray-400">
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
