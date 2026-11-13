<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h2 class="font-semibold text-xl text-text-100 leading-tight">
                {{ __('Productos') }}
            </h2>

            @if (auth()->user()->isAdmin())
                <a href="{{ route('productos.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-primary-100 text-text-100 text-sm font-medium rounded-md hover:bg-primary-200">
                    {{ __('Nuevo producto') }}
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-bg-200 border border-bg-300 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    <!-- Búsqueda y filtros -->
                    <form method="GET" action="{{ route('productos.index') }}" class="flex flex-col sm:flex-row flex-wrap gap-2 mb-6">
                        <x-input type="search" name="buscar" :value="request('buscar')"
                                      placeholder="{{ __('Buscar por nombre o código...') }}"
                                      class="w-full sm:max-w-xs" />

                        <x-select name="categoria_id" class="w-full sm:max-w-xs">
                            <option value="">{{ __('Todas las categorías') }}</option>
                            @foreach ($categorias as $categoria)
                                <option value="{{ $categoria->id }}" @selected(request('categoria_id', request('categoria')) == $categoria->id)>
                                    {{ $categoria->nombre }}
                                </option>
                            @endforeach
                        </x-select>

                        <label class="inline-flex items-center gap-2 text-sm text-text-200">
                            <input type="checkbox" name="stock_bajo" value="1"
                                   class="rounded border-bg-300 bg-bg-100 text-primary-100 shadow-sm focus:ring-primary-300"
                                   @checked(request()->boolean('stock_bajo'))>
                            {{ __('Solo stock bajo') }}
                        </label>

                        <x-button-primary type="submit">{{ __('Filtrar') }}</x-button-primary>
                        @if (request('buscar') || request('categoria_id') || request('stock_bajo'))
                            <a href="{{ route('productos.index') }}"
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
                                    <th class="py-2 pe-4 font-medium">{{ __('Código') }}</th>
                                    <th class="py-2 pe-4 font-medium">{{ __('Nombre') }}</th>
                                    <th class="py-2 pe-4 font-medium">{{ __('Categoría') }}</th>
                                    <th class="py-2 pe-4 font-medium text-end">{{ __('P. venta') }}</th>
                                    <th class="py-2 pe-4 font-medium text-end">{{ __('Stock') }}</th>
                                    <th class="py-2 pe-4 font-medium">{{ __('Estado') }}</th>
                                    <th class="py-2 pe-4 font-medium text-end">{{ __('Acciones') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-bg-300">
                                @forelse ($productos as $producto)
                                    <tr class="hover:bg-bg-300/40">
                                        <td class="py-3 pe-4 font-medium text-text-100">{{ $producto->codigo }}</td>
                                        <td class="py-3 pe-4 text-text-100">{{ $producto->nombre }}</td>
                                        <td class="py-3 pe-4 text-text-200">{{ $producto->categoria?->nombre ?? '—' }}</td>
                                        <td class="py-3 pe-4 text-text-200 text-end">{{ number_format($producto->precio_venta, 2) }}</td>
                                        <td class="py-3 pe-4 text-end">
                                            <span class="{{ $producto->enStockBajo() ? 'text-amber-300 font-medium' : 'text-text-200' }}">
                                                {{ $producto->stock_actual }} / {{ $producto->stock_minimo }}
                                            </span>
                                        </td>
                                        <td class="py-3 pe-4">
                                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $producto->activo ? 'bg-green-900/40 text-green-300' : 'bg-bg-300 text-text-200' }}">
                                                {{ $producto->activo ? __('Activo') : __('Inactivo') }}
                                            </span>
                                        </td>
                                        <td class="py-3 pe-4 text-end whitespace-nowrap">
                                            @if (auth()->user()->isAdmin())
                                                <a href="{{ route('productos.edit', $producto) }}"
                                                   class="text-accent-200 hover:text-text-100 font-medium">
                                                    {{ __('Editar') }}
                                                </a>

                                                <form method="POST" action="{{ route('productos.toggleActivo', $producto) }}"
                                                      class="inline ms-3">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="text-text-200 hover:text-text-100 font-medium">
                                                        {{ $producto->activo ? __('Desactivar') : __('Activar') }}
                                                    </button>
                                                </form>

                                                @php
                                                    $tieneOperaciones = $producto->detalleCompras()->exists()
                                                        || $producto->detalleVentas()->exists()
                                                        || $producto->movimientosInventario()->exists();
                                                @endphp

                                                @if (! $tieneOperaciones)
                                                    <x-delete-button :action="route('productos.destroy', $producto)"
                                                                     :message="__('¿Estás seguro de eliminar este producto?')" />
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-6 text-center text-text-200">
                                            {{ __('No se encontraron productos.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <div class="mt-4">
                        {{ $productos->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>