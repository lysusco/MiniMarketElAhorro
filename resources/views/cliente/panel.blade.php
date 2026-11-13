<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-text-100 leading-tight">
            {{ __('Mi panel') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Indicadores del cliente -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-bg-200 border border-bg-300 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-5">
                        <div class="text-sm font-medium text-text-200">{{ __('Límite de crédito') }}</div>
                        <div class="mt-1 text-3xl font-semibold text-text-100">${{ number_format($cliente->limite_credito, 0, ',', '.') }}</div>
                    </div>
                </div>

                <div class="bg-bg-200 border border-bg-300 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-5">
                        <div class="text-sm font-medium text-text-200">{{ __('Saldo pendiente') }}</div>
                        <div class="mt-1 text-3xl font-semibold {{ $cliente->saldo > 0 ? 'text-amber-400' : 'text-text-100' }}">
                            ${{ number_format($cliente->saldo, 0, ',', '.') }}
                        </div>
                    </div>
                </div>

                <div class="bg-bg-200 border border-bg-300 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-5">
                        <div class="text-sm font-medium text-text-200">{{ __('Crédito disponible') }}</div>
                        <div class="mt-1 text-3xl font-semibold text-text-100">${{ number_format($cliente->creditoDisponible(), 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Últimas ventas -->
                <div class="bg-bg-200 border border-bg-300 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-text-100">{{ __('Mis últimas ventas') }}</h3>
                        <div class="mt-4 overflow-x-auto">
                            <table class="min-w-full divide-y divide-bg-300 text-sm">
                                <thead>
                                    <tr class="text-left text-text-200">
                                        <th class="py-2 pe-4 font-medium">{{ __('N.º') }}</th>
                                        <th class="py-2 pe-4 font-medium">{{ __('Fecha') }}</th>
                                        <th class="py-2 pe-4 font-medium">{{ __('Tipo') }}</th>
                                        <th class="py-2 pe-4 font-medium text-end">{{ __('Total') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-bg-300">
                                    @forelse ($ventasRecientes as $venta)
                                        <tr class="hover:bg-bg-300/40">
                                            <td class="py-2 pe-4 text-text-100">
                                                <a href="{{ route('cliente.ventas.show', $venta) }}" class="text-accent-200 hover:text-text-100">#{{ $venta->id }}</a>
                                            </td>
                                            <td class="py-2 pe-4 text-text-200">{{ $venta->fecha->format('d/m/Y') }}</td>
                                            <td class="py-2 pe-4">
                                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $venta->tipo_venta === 'fiado' ? 'bg-amber-500/15 text-amber-400' : 'bg-green-500/15 text-green-400' }}">
                                                    {{ $venta->tipo_venta === 'fiado' ? __('Fiado') : __('Contado') }}
                                                </span>
                                            </td>
                                            <td class="py-2 pe-4 text-text-100 text-end">${{ number_format($venta->total, 0, ',', '.') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="py-4 text-center text-text-200">{{ __('Aún no tienes ventas registradas.') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Últimos abonos -->
                <div class="bg-bg-200 border border-bg-300 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-text-100">{{ __('Mis últimos abonos') }}</h3>
                        <div class="mt-4 overflow-x-auto">
                            <table class="min-w-full divide-y divide-bg-300 text-sm">
                                <thead>
                                    <tr class="text-left text-text-200">
                                        <th class="py-2 pe-4 font-medium">{{ __('Fecha') }}</th>
                                        <th class="py-2 pe-4 font-medium">{{ __('Venta') }}</th>
                                        <th class="py-2 pe-4 font-medium">{{ __('Método') }}</th>
                                        <th class="py-2 pe-4 font-medium text-end">{{ __('Monto') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-bg-300">
                                    @forelse ($abonosRecientes as $abono)
                                        <tr class="hover:bg-bg-300/40">
                                            <td class="py-2 pe-4 text-text-200">{{ $abono->fecha->format('d/m/Y') }}</td>
                                            <td class="py-2 pe-4 text-text-100">#{{ $abono->venta_id }}</td>
                                            <td class="py-2 pe-4 text-text-200">{{ $abono->metodo_pago }}</td>
                                            <td class="py-2 pe-4 text-text-100 text-end">${{ number_format($abono->monto, 0, ',', '.') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="py-4 text-center text-text-200">{{ __('Aún no tienes abonos registrados.') }}</td>
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