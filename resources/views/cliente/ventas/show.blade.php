<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-text-100 leading-tight">
            {{ __('Venta #') }}{{ $venta->id }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-bg-200 border border-bg-300 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div><span class="font-medium text-text-200">{{ __('Fecha:') }}</span> <span class="text-text-100">{{ $venta->fecha->format('d/m/Y') }}</span></div>
                    <div><span class="font-medium text-text-200">{{ __('Tipo:') }}</span> <span class="text-text-100">{{ $venta->tipo_venta === 'fiado' ? __('Fiado') : __('Contado') }}</span></div>
                    <div><span class="font-medium text-text-200">{{ __('Estado:') }}</span> <span class="text-text-100">{{ $venta->estado }}</span></div>
                    <div><span class="font-medium text-text-200">{{ __('Total:') }}</span> <span class="text-text-100">${{ number_format($venta->total, 0, ',', '.') }}</span></div>
                    <div><span class="font-medium text-text-200">{{ __('Saldo pendiente:') }}</span> <span class="text-text-100">${{ number_format($venta->saldoPendiente(), 0, ',', '.') }}</span></div>
                </div>
            </div>

            <div class="bg-bg-200 border border-bg-300 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-text-100">{{ __('Productos') }}</h3>
                    <div class="mt-4 overflow-x-auto">
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
                                    <tr class="hover:bg-bg-300/40">
                                        <td class="py-2 pe-4 text-text-100">{{ $detalle->producto->nombre }}</td>
                                        <td class="py-2 pe-4 text-end text-text-200">{{ $detalle->cantidad }}</td>
                                        <td class="py-2 pe-4 text-end text-text-200">${{ number_format($detalle->precio_unitario, 0, ',', '.') }}</td>
                                        <td class="py-2 pe-4 text-end text-text-100">${{ number_format($detalle->subtotal, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="bg-bg-200 border border-bg-300 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-text-100">{{ __('Abonos') }}</h3>
                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full divide-y divide-bg-300 text-sm">
                            <thead>
                                <tr class="text-left text-text-200">
                                    <th class="py-2 pe-4 font-medium">{{ __('Fecha') }}</th>
                                    <th class="py-2 pe-4 font-medium">{{ __('Método') }}</th>
                                    <th class="py-2 pe-4 font-medium text-end">{{ __('Monto') }}</th>
                                    <th class="py-2 pe-4 font-medium">{{ __('Observación') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-bg-300">
                                @forelse ($venta->abonos as $abono)
                                    <tr class="hover:bg-bg-300/40">
                                        <td class="py-2 pe-4 text-text-200">{{ $abono->fecha->format('d/m/Y') }}</td>
                                        <td class="py-2 pe-4 text-text-200">{{ $abono->metodo_pago }}</td>
                                        <td class="py-2 pe-4 text-end text-text-100">${{ number_format($abono->monto, 0, ',', '.') }}</td>
                                        <td class="py-2 pe-4 text-text-200">{{ $abono->observacion }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-4 text-center text-text-200">{{ __('Esta venta no tiene abonos registrados.') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>