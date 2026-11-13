<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-text-100 leading-tight">
            {{ __('Entrada de mercancía #') }}{{ $compra->id }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-bg-200 border border-bg-300 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-text-200">{{ __('Proveedor:') }}</span>
                            <span class="text-text-100 font-medium">{{ $compra->proveedor?->nombre ?? '—' }}</span>
                        </div>
                        <div>
                            <span class="text-text-200">{{ __('Fecha:') }}</span>
                            <span class="text-text-100 font-medium">{{ $compra->fecha->format('Y-m-d') }}</span>
                        </div>
                        <div>
                            <span class="text-text-200">{{ __('Registrado por:') }}</span>
                            <span class="text-text-100 font-medium">{{ $compra->user?->name ?? '—' }}</span>
                        </div>
                        <div>
                            <span class="text-text-200">{{ __('Total:') }}</span>
                            <span class="text-text-100 font-medium">{{ number_format($compra->total, 2) }}</span>
                        </div>
                        <div>
                            <span class="text-text-200">{{ __('Estado:') }}</span>
                            <span class="text-text-100 font-medium">{{ $compra->estado }}</span>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-bg-300 text-sm">
                            <thead>
                                <tr class="text-left text-text-200">
                                    <th class="py-2 pe-4 font-medium">{{ __('Producto') }}</th>
                                    <th class="py-2 pe-4 font-medium text-end">{{ __('Cantidad') }}</th>
                                    <th class="py-2 pe-4 font-medium text-end">{{ __('Costo unitario') }}</th>
                                    <th class="py-2 pe-4 font-medium text-end">{{ __('Subtotal') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-bg-300">
                                @foreach ($compra->detalles as $detalle)
                                    <tr>
                                        <td class="py-3 pe-4 text-text-100">{{ $detalle->producto?->nombre ?? '—' }}</td>
                                        <td class="py-3 pe-4 text-text-200 text-end">{{ $detalle->cantidad }}</td>
                                        <td class="py-3 pe-4 text-text-200 text-end">{{ number_format($detalle->costo_unitario, 2) }}</td>
                                        <td class="py-3 pe-4 text-text-200 text-end">{{ number_format($detalle->subtotal, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        @if ($compra->estado === \App\Models\Compra::ESTADO_EN_PROGRESO)
                            <a href="{{ route('compras.edit', $compra) }}"
                               class="inline-flex items-center px-4 py-2 bg-primary-100 text-text-100 text-sm font-medium rounded-md hover:bg-primary-200">
                                {{ __('Editar') }}
                            </a>

                            <form method="POST" action="{{ route('compras.finalizar', $compra) }}" class="inline">
                                @csrf
                                <button type="submit"
                                        class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-500">
                                    {{ __('Finalizar') }}
                                </button>
                            </form>

                            <form method="POST" action="{{ route('compras.inactivar', $compra) }}" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        class="px-4 py-2 bg-bg-300 text-text-100 text-sm font-medium rounded-md hover:bg-bg-300/80">
                                    {{ __('Inactivar') }}
                                </button>
                            </form>
                        @endif

                        <a href="{{ route('compras.index') }}"
                           class="inline-flex items-center px-4 py-2 bg-bg-200 border border-bg-300 text-sm font-medium rounded-md text-text-100 hover:bg-bg-300">
                            {{ __('Volver') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>