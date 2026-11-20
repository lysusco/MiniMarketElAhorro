<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-text-100 leading-tight">
            {{ __('Venta #') }}{{ $venta->id }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-bg-200 border border-bg-300 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-text-200">{{ __('Cliente:') }}</span>
                            <span class="text-text-100 font-medium">{{ $venta->cliente?->nombre ?? __('Mostrador') }}</span>
                        </div>
                        <div>
                            <span class="text-text-200">{{ __('Fecha:') }}</span>
                            <span class="text-text-100 font-medium">{{ $venta->fecha->format('Y-m-d') }}</span>
                        </div>
                        <div>
                            <span class="text-text-200">{{ __('Tipo de venta:') }}</span>
                            <span class="text-text-100 font-medium">{{ __($venta->tipo_venta === \App\Models\Venta::TIPO_FIADO ? 'Fiado' : 'Contado') }}</span>
                        </div>
                        <div>
                            <span class="text-text-200">{{ __('Registrado por:') }}</span>
                            <span class="text-text-100 font-medium">{{ $venta->user?->name ?? '—' }}</span>
                        </div>
                        <div>
                            <span class="text-text-200">{{ __('Subtotal:') }}</span>
                            <span class="text-text-100 font-medium">{{ number_format($venta->subtotal, 2) }}</span>
                        </div>
                        <div>
                            <span class="text-text-200">{{ __('Descuento:') }}</span>
                            <span class="text-text-100 font-medium">{{ number_format($venta->descuento, 2) }}</span>
                        </div>
                        <div>
                            <span class="text-text-200">{{ __('Total:') }}</span>
                            <span class="text-text-100 font-medium">{{ number_format($venta->total, 2) }}</span>
                        </div>
                        <div>
                            <span class="text-text-200">{{ __('Estado de pago:') }}</span>
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
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-bg-300 text-sm">
                            <thead>
                                <tr class="text-left text-text-200">
                                    <th class="py-2 pe-4 font-medium">{{ __('Producto') }}</th>
                                    <th class="py-2 pe-4 font-medium text-end">{{ __('Cantidad') }}</th>
                                    <th class="py-2 pe-4 font-medium text-end">{{ __('Precio unitario') }}</th>
                                    <th class="py-2 pe-4 font-medium text-end">{{ __('Subtotal') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-bg-300">
                                @foreach ($venta->detalles as $detalle)
                                    <tr>
                                        <td class="py-3 pe-4 text-text-100">{{ $detalle->producto?->nombre ?? '—' }}</td>
                                        <td class="py-3 pe-4 text-text-200 text-end">{{ $detalle->cantidad }}</td>
                                        <td class="py-3 pe-4 text-text-200 text-end">{{ number_format($detalle->precio_unitario, 2) }}</td>
                                        <td class="py-3 pe-4 text-text-200 text-end">{{ number_format($detalle->subtotal, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if ($venta->abonos->isNotEmpty())
                        <div>
                            <h3 class="text-sm font-medium text-text-100 mb-2">{{ __('Abonos') }}</h3>

                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-bg-300 text-sm">
                                    <thead>
                                        <tr class="text-left text-text-200">
                                            <th class="py-2 pe-4 font-medium">{{ __('Fecha') }}</th>
                                            <th class="py-2 pe-4 font-medium text-end">{{ __('Monto') }}</th>
                                            <th class="py-2 pe-4 font-medium">{{ __('Método de pago') }}</th>
                                            <th class="py-2 pe-4 font-medium">{{ __('Registrado por') }}</th>
                                            <th class="py-2 pe-4 font-medium">{{ __('Observación') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-bg-300">
                                        @foreach ($venta->abonos as $abono)
                                            <tr>
                                                <td class="py-3 pe-4 text-text-200">{{ $abono->fecha->format('Y-m-d') }}</td>
                                                <td class="py-3 pe-4 text-text-200 text-end">{{ number_format($abono->monto, 2) }}</td>
                                                <td class="py-3 pe-4 text-text-200">{{ __($abono->metodo_pago) }}</td>
                                                <td class="py-3 pe-4 text-text-200">{{ $abono->user?->name ?? '—' }}</td>
                                                <td class="py-3 pe-4 text-text-200">{{ $abono->observacion ?? '—' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    <div class="flex flex-wrap items-center gap-3">
                        @if ($venta->esFiada() && $venta->saldoPendiente() > 0)
                            <a href="{{ route('abonos.create', ['venta' => $venta->id]) }}"
                               class="inline-flex items-center px-4 py-2 bg-primary-100 text-text-100 text-sm font-medium rounded-md hover:bg-primary-200">
                                {{ __('Registrar abono') }}
                            </a>
                        @endif
                        <a href="{{ route('ventas.index') }}"
                           class="inline-flex items-center px-4 py-2 bg-bg-200 border border-bg-300 text-sm font-medium rounded-md text-text-100 hover:bg-bg-300">
                            {{ __('Volver') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
