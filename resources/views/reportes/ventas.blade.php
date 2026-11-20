<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h2 class="font-semibold text-xl text-text-100 leading-tight">
                {{ __('Reporte de ventas') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-bg-200 border border-bg-300 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    <!-- Filtro por rango de fechas -->
                    <form method="GET" action="{{ route('reportes.ventas') }}" class="flex flex-col sm:flex-row flex-wrap gap-2 mb-6">
                        <x-input type="date" name="fecha_desde" :value="request('fecha_desde')" class="w-full sm:max-w-xs" />
                        <x-input type="date" name="fecha_hasta" :value="request('fecha_hasta')" class="w-full sm:max-w-xs" />

                        <x-button-primary type="submit">{{ __('Filtrar') }}</x-button-primary>
                        @if (request('fecha_desde') || request('fecha_hasta'))
                            <a href="{{ route('reportes.ventas') }}"
                               class="inline-flex items-center px-4 py-2 bg-bg-200 border border-bg-300 text-sm font-medium rounded-md text-text-100 hover:bg-bg-300">
                                {{ __('Limpiar') }}
                            </a>
                        @endif
                    </form>

                    <!-- Totales del rango -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                        <div class="bg-bg-100 border border-bg-300 rounded-md p-4">
                            <p class="text-xs uppercase tracking-wider text-text-200">{{ __('Cantidad de ventas') }}</p>
                            <p class="mt-1 text-lg font-semibold text-text-100">{{ $totales['cantidad'] }}</p>
                        </div>
                        <div class="bg-bg-100 border border-bg-300 rounded-md p-4">
                            <p class="text-xs uppercase tracking-wider text-text-200">{{ __('Total contado') }}</p>
                            <p class="mt-1 text-lg font-semibold text-text-100">{{ number_format($totales['contado'], 2) }}</p>
                        </div>
                        <div class="bg-bg-100 border border-bg-300 rounded-md p-4">
                            <p class="text-xs uppercase tracking-wider text-text-200">{{ __('Total fiado') }}</p>
                            <p class="mt-1 text-lg font-semibold text-text-100">{{ number_format($totales['fiado'], 2) }}</p>
                        </div>
                        <div class="bg-bg-100 border border-bg-300 rounded-md p-4">
                            <p class="text-xs uppercase tracking-wider text-text-200">{{ __('Total del rango') }}</p>
                            <p class="mt-1 text-lg font-semibold text-text-100">{{ number_format($totales['total'], 2) }}</p>
                        </div>
                    </div>

                    <!-- Listado de ventas del rango -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-bg-300 text-sm">
                            <thead>
                                <tr class="text-left text-text-200">
                                    <th class="py-2 pe-4 font-medium">{{ __('Venta') }}</th>
                                    <th class="py-2 pe-4 font-medium">{{ __('Fecha') }}</th>
                                    <th class="py-2 pe-4 font-medium">{{ __('Cliente') }}</th>
                                    <th class="py-2 pe-4 font-medium">{{ __('Registrada por') }}</th>
                                    <th class="py-2 pe-4 font-medium">{{ __('Tipo') }}</th>
                                    <th class="py-2 pe-4 font-medium">{{ __('Estado de pago') }}</th>
                                    <th class="py-2 pe-4 font-medium text-end">{{ __('Total') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-bg-300">
                                @forelse ($ventas as $venta)
                                    <tr class="hover:bg-bg-300/40">
                                        <td class="py-3 pe-4 font-medium text-text-100">
                                            <a href="{{ route('ventas.show', $venta) }}" class="text-accent-200 hover:text-text-100">
                                                Venta #{{ $venta->id }}
                                            </a>
                                        </td>
                                        <td class="py-3 pe-4 text-text-200">{{ $venta->fecha->format('Y-m-d') }}</td>
                                        <td class="py-3 pe-4 text-text-200">{{ $venta->cliente?->nombre ?? __('Mostrador') }}</td>
                                        <td class="py-3 pe-4 text-text-200">{{ $venta->user?->name ?? '—' }}</td>
                                        <td class="py-3 pe-4 text-text-200">{{ __($venta->tipo_venta === \App\Models\Venta::TIPO_FIADO ? 'Fiado' : 'Contado') }}</td>
                                        <td class="py-3 pe-4">
                                            @php
                                                $estadoClases = match ($venta->estado) {
                                                    \App\Models\Venta::ESTADO_PAGADA => 'bg-green-100 text-green-700',
                                                    \App\Models\Venta::ESTADO_PARCIAL => 'bg-blue-100 text-blue-700',
                                                    default => 'bg-amber-100 text-amber-700',
                                                };
                                                $estadoLabel = match ($venta->estado) {
                                                    \App\Models\Venta::ESTADO_PAGADA => 'Pagada',
                                                    \App\Models\Venta::ESTADO_PARCIAL => 'Parcial',
                                                    default => 'Pendiente',
                                                };
                                            @endphp
                                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $estadoClases }}">
                                                {{ __($estadoLabel) }}
                                            </span>
                                        </td>
                                        <td class="py-3 pe-4 text-text-200 text-end">{{ number_format($venta->total, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-6 text-center text-text-200">
                                            {{ __('No se encontraron ventas.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <div class="mt-4">
                        {{ $ventas->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
