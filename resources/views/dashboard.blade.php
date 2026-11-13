<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-text-100 leading-tight">
            {{ __('Panel principal') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Indicadores generales -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Productos activos -->
                <div class="bg-bg-200 border border-bg-300 overflow-hidden sm:rounded-lg">
                    <div class="p-5">
                        <div class="text-sm font-medium text-text-200">{{ __('Productos activos') }}</div>
                        <div class="mt-1 text-3xl font-semibold text-text-100">{{ $productosActivos }}</div>
                    </div>
                </div>

                <!-- Productos con stock bajo -->
                <div class="bg-bg-200 border border-bg-300 overflow-hidden sm:rounded-lg">
                    <div class="p-5">
                        <div class="text-sm font-medium text-text-200">{{ __('Stock bajo') }}</div>
                        <div class="mt-1 text-3xl font-semibold {{ $productosStockBajo > 0 ? 'text-red-400' : 'text-text-100' }}">
                            {{ $productosStockBajo }}
                        </div>
                        <div class="mt-1 text-xs text-text-200">{{ __('Productos en o bajo el mínimo') }}</div>
                    </div>
                </div>

                <!-- Ventas de hoy -->
                <div class="bg-bg-200 border border-bg-300 overflow-hidden sm:rounded-lg">
                    <div class="p-5">
                        <div class="text-sm font-medium text-text-200">{{ __('Ventas de hoy') }}</div>
                        <div class="mt-1 text-3xl font-semibold text-text-100">{{ $ventasHoyCantidad }}</div>
                        <div class="mt-1 text-xs text-text-200">
                            {{ __('Total:') }} ${{ number_format($ventasHoyTotal, 0, ',', '.') }}
                        </div>
                    </div>
                </div>

                <!-- Cartera pendiente -->
                <div class="bg-bg-200 border border-bg-300 overflow-hidden sm:rounded-lg">
                    <div class="p-5">
                        <div class="text-sm font-medium text-text-200">{{ __('Cartera pendiente') }}</div>
                        <div class="mt-1 text-3xl font-semibold {{ $carteraPendiente > 0 ? 'text-amber-400' : 'text-text-100' }}">
                            ${{ number_format($carteraPendiente, 0, ',', '.') }}
                        </div>
                        <div class="mt-1 text-xs text-text-200">{{ __('Saldo fiado de los clientes') }}</div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Últimas ventas -->
                <div class="bg-bg-200 border border-bg-300 overflow-hidden sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-text-100">{{ __('Últimas ventas') }}</h3>

                        <div class="mt-4 overflow-x-auto">
                            <table class="min-w-full divide-y divide-bg-300 text-sm">
                                <thead>
                                    <tr class="text-left text-text-200">
                                        <th class="py-2 pe-4 font-medium">{{ __('N.º') }}</th>
                                        <th class="py-2 pe-4 font-medium">{{ __('Fecha') }}</th>
                                        <th class="py-2 pe-4 font-medium">{{ __('Cliente') }}</th>
                                        <th class="py-2 pe-4 font-medium">{{ __('Tipo') }}</th>
                                        <th class="py-2 pe-4 font-medium text-end">{{ __('Total') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-bg-300">
                                    @forelse ($ultimasVentas as $venta)
                                        <tr class="hover:bg-bg-300/40">
                                            <td class="py-2 pe-4 text-text-100">#{{ $venta->id }}</td>
                                            <td class="py-2 pe-4 text-text-200">{{ $venta->fecha->format('d/m/Y') }}</td>
                                            <td class="py-2 pe-4 text-text-200">
                                                {{ $venta->cliente->nombre ?? __('Venta de mostrador') }}
                                            </td>
                                            <td class="py-2 pe-4">
                                                @if ($venta->tipo_venta === 'fiado')
                                                    <x-badge variant="warning">{{ __('Fiado') }}</x-badge>
                                                @else
                                                    <x-badge variant="success">{{ __('Contado') }}</x-badge>
                                                @endif
                                            </td>
                                            <td class="py-2 pe-4 text-text-100 text-end">${{ number_format($venta->total, 0, ',', '.') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="py-4 text-center text-text-200">{{ __('Aún no hay ventas registradas.') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Productos con stock bajo -->
                <div class="bg-bg-200 border border-bg-300 overflow-hidden sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-text-100">{{ __('Productos por reponer') }}</h3>
                        <p class="mt-1 text-sm text-text-200">{{ __('Stock igual o menor al mínimo definido (RF-11).') }}</p>

                        <div class="mt-4 overflow-x-auto">
                            <table class="min-w-full divide-y divide-bg-300 text-sm">
                                <thead>
                                    <tr class="text-left text-text-200">
                                        <th class="py-2 pe-4 font-medium">{{ __('Producto') }}</th>
                                        <th class="py-2 pe-4 font-medium">{{ __('Categoría') }}</th>
                                        <th class="py-2 pe-4 font-medium text-end">{{ __('Stock') }}</th>
                                        <th class="py-2 pe-4 font-medium text-end">{{ __('Mínimo') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-bg-300">
                                    @forelse ($stockBajo as $producto)
                                        <tr class="hover:bg-bg-300/40">
                                            <td class="py-2 pe-4 text-text-100">{{ $producto->nombre }}</td>
                                            <td class="py-2 pe-4 text-text-200">{{ $producto->categoria->nombre }}</td>
                                            <td class="py-2 pe-4 text-end font-semibold text-red-400">{{ $producto->stock_actual }}</td>
                                            <td class="py-2 pe-4 text-end text-text-200">{{ $producto->stock_minimo }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="py-4 text-center text-text-200">{{ __('Todos los productos tienen existencias suficientes.') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>