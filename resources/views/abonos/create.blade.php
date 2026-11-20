<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-text-100 leading-tight">
            {{ __('Registrar abono') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-bg-200 border border-bg-300 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 space-y-6">

                    @if (session('success'))
                        <x-alert type="success">{{ session('success') }}</x-alert>
                    @endif

                    @if ($advertencia)
                        <x-alert type="warning">{{ $advertencia }}</x-alert>
                    @endif

                    @if ($errors->has('venta_id'))
                        <x-alert type="error">{{ $errors->first('venta_id') }}</x-alert>
                    @endif

                    @if ($ventaSeleccionada)
                        @php
                            $abonado = (float) $ventaSeleccionada->total - $ventaSeleccionada->saldoPendiente();
                        @endphp

                        <div>
                            <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                                <h3 class="text-sm font-medium text-text-100">
                                    {{ __('Venta #') }}{{ $ventaSeleccionada->id }}
                                    · {{ $ventaSeleccionada->cliente?->nombre ?? __('Mostrador') }}
                                </h3>
                                <a href="{{ route('abonos.create', ['cliente' => $ventaSeleccionada->cliente_id]) }}"
                                   class="text-sm text-accent-200 hover:text-text-100">
                                    {{ __('Cambiar venta') }}
                                </a>
                            </div>

                            <!-- Resumen de la venta -->
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 bg-bg-300/30 p-4 rounded-md text-sm">
                                <div>
                                    <span class="text-text-200">{{ __('Total:') }}</span>
                                    <span class="text-text-100 font-medium">{{ number_format($ventaSeleccionada->total, 2) }}</span>
                                </div>
                                <div>
                                    <span class="text-text-200">{{ __('Abonado:') }}</span>
                                    <span class="text-text-100 font-medium">{{ number_format($abonado, 2) }}</span>
                                </div>
                                <div>
                                    <span class="text-text-200">{{ __('Saldo pendiente:') }}</span>
                                    <span class="text-text-100 font-medium">{{ number_format($ventaSeleccionada->saldoPendiente(), 2) }}</span>
                                </div>
                            </div>

                            <!-- Historial de abonos -->
                            @if ($ventaSeleccionada->abonos->isNotEmpty())
                                <h3 class="text-sm font-medium text-text-100 mt-4 mb-2">{{ __('Abonos registrados') }}</h3>

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
                                            @foreach ($ventaSeleccionada->abonos as $abono)
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
                            @endif
                        </div>

                        <!-- Formulario del abono -->
                        <form method="POST" action="{{ route('abonos.store') }}" class="space-y-4">
                            @csrf
                            <input type="hidden" name="venta_id" value="{{ $ventaSeleccionada->id }}" />

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div>
                                    <x-input-label for="fecha" :value="__('Fecha')" />
                                    <x-input id="fecha" name="fecha" type="date" class="block mt-1 w-full"
                                             :value="old('fecha', now()->toDateString())" required />
                                    <x-input-error :messages="$errors->get('fecha')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="monto" :value="__('Monto')" />
                                    <x-input id="monto" name="monto" type="number" step="0.01" min="0.01"
                                             class="block mt-1 w-full" :value="old('monto')" required />
                                    <x-input-error :messages="$errors->get('monto')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="metodo_pago" :value="__('Método de pago')" />
                                    <x-select id="metodo_pago" name="metodo_pago" class="block mt-1 w-full" required>
                                        <option value="efectivo" @selected(old('metodo_pago') === 'efectivo')>{{ __('Efectivo') }}</option>
                                        <option value="transferencia" @selected(old('metodo_pago') === 'transferencia')>{{ __('Transferencia') }}</option>
                                        <option value="otro" @selected(old('metodo_pago') === 'otro')>{{ __('Otro') }}</option>
                                    </x-select>
                                    <x-input-error :messages="$errors->get('metodo_pago')" class="mt-2" />
                                </div>
                            </div>

                            <div>
                                <x-input-label for="observacion" :value="__('Observación (opcional)')" />
                                <x-textarea id="observacion" name="observacion" rows="2" class="block mt-1 w-full">{{ old('observacion') }}</x-textarea>
                                <x-input-error :messages="$errors->get('observacion')" class="mt-2" />
                            </div>

                            <div class="flex items-center gap-3">
                                <x-button-primary>{{ __('Registrar abono') }}</x-button-primary>
                                <a href="{{ route('ventas.show', $ventaSeleccionada) }}"
                                   class="inline-flex items-center px-4 py-2 bg-bg-200 border border-bg-300 text-sm font-medium rounded-md text-text-100 hover:bg-bg-300">
                                    {{ __('Cancelar') }}
                                </a>
                            </div>
                        </form>
                    @else
                        @if ($clienteSeleccionado)
                            <!-- Cliente elegido: listar sus ventas fiadas con saldo pendiente (CU-07) -->
                            <div class="flex flex-wrap items-center justify-between gap-2 bg-bg-300/30 p-3 rounded-md">
                                <div class="text-sm">
                                    <span class="text-text-100 font-medium">{{ $clienteSeleccionado->nombre }}</span>
                                    <span class="text-text-200">{{ $clienteSeleccionado->identificacion ? ' - '.$clienteSeleccionado->identificacion : '' }}</span>
                                    <span class="text-text-200">· {{ __('Saldo') }}: ${{ number_format($clienteSeleccionado->saldo, 2) }}</span>
                                </div>
                                <a href="{{ route('abonos.create') }}" class="text-sm text-accent-200 hover:text-text-100">
                                    {{ __('Cambiar cliente') }}
                                </a>
                            </div>

                            <div>
                                <h3 class="text-sm font-medium text-text-100 mb-2">{{ __('Ventas fiadas con saldo pendiente') }}</h3>

                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-bg-300 text-sm">
                                        <thead>
                                            <tr class="text-left text-text-200">
                                                <th class="py-2 pe-4 font-medium">{{ __('Venta') }}</th>
                                                <th class="py-2 pe-4 font-medium">{{ __('Fecha') }}</th>
                                                <th class="py-2 pe-4 font-medium text-end">{{ __('Total') }}</th>
                                                <th class="py-2 pe-4 font-medium text-end">{{ __('Abonado') }}</th>
                                                <th class="py-2 pe-4 font-medium text-end">{{ __('Saldo pendiente') }}</th>
                                                <th class="py-2 pe-4 font-medium text-end">{{ __('Acciones') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-bg-300">
                                            @forelse ($ventasCliente as $venta)
                                                <tr>
                                                    <td class="py-3 pe-4 text-text-100">{{ __('Venta #') }}{{ $venta->id }}</td>
                                                    <td class="py-3 pe-4 text-text-200">{{ $venta->fecha->format('Y-m-d') }}</td>
                                                    <td class="py-3 pe-4 text-text-200 text-end">{{ number_format($venta->total, 2) }}</td>
                                                    <td class="py-3 pe-4 text-text-200 text-end">{{ number_format($venta->abonado ?? 0, 2) }}</td>
                                                    <td class="py-3 pe-4 text-text-100 text-end">{{ number_format((float) $venta->total - (float) ($venta->abonado ?? 0), 2) }}</td>
                                                    <td class="py-3 pe-4 text-end">
                                                        <a href="{{ route('abonos.create', ['venta' => $venta->id]) }}"
                                                           class="text-accent-200 hover:text-text-100 text-sm font-medium">
                                                            {{ __('Abonar') }}
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="py-4 text-center text-text-200">
                                                        {{ __('Este cliente no tiene ventas fiadas con saldo pendiente.') }}
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @else
                            <!-- Buscador de cliente (patrón de modal de compras/create) -->
                            <div x-data="clienteSelector(@json($clientes))">
                                <x-input-label for="cliente" :value="__('Cliente')" />

                                <div class="flex gap-2 mt-1">
                                    <input type="text" id="cliente" x-model="query"
                                           placeholder="{{ __('Buscar por nombre o identificación...') }}"
                                           class="bg-bg-100 border-bg-300 text-text-100 focus:border-accent-200 focus:ring-accent-200 rounded-md shadow-sm w-full" />
                                    <button type="button" onclick="document.getElementById('cliente-modal').style.display='flex'"
                                            class="px-3 bg-bg-300 text-text-100 rounded-md hover:bg-bg-300/80"
                                            title="{{ __('Listar clientes') }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M21 21l-4.35-4.35M11 18a7 7 0 100-14 7 7 0 000 14z" />
                                        </svg>
                                    </button>
                                </div>

                                <!-- Autocompletado -->
                                <ul x-show="query && filtered.length" x-cloak
                                    class="mt-1 bg-bg-100 border border-bg-300 rounded-md shadow-lg max-h-40 overflow-y-auto">
                                    <template x-for="c in filtered" :key="c.id">
                                        <li>
                                            <a :href="'{{ route('abonos.create') }}?cliente=' + c.id"
                                               class="block px-3 py-2 text-sm text-text-100 hover:bg-bg-300">
                                                <span x-text="c.nombre"></span>
                                                <span class="text-text-200" x-text="c.identificacion ? ' - ' + c.identificacion : ''"></span>
                                            </a>
                                        </li>
                                    </template>
                                </ul>

                                <p class="mt-2 text-sm text-text-200">
                                    {{ __('Solo se listan clientes con saldo pendiente. Seleccione uno para ver sus ventas fiadas.') }}
                                </p>

                                <!-- Modal de listado -->
                                <div id="cliente-modal" style="display:none;"
                                     class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
                                     onclick="if(event.target===this)this.style.display='none'">
                                    <div class="bg-bg-200 border border-bg-300 rounded-lg shadow-xl p-6 w-full max-w-md mx-auto max-h-[80vh] overflow-y-auto">
                                        <h3 class="text-text-100 font-semibold mb-4">{{ __('Seleccionar cliente') }}</h3>
                                        <input type="text" placeholder="{{ __('Buscar...') }}"
                                               class="bg-bg-100 border-bg-300 text-text-100 rounded-md shadow-sm w-full mb-3"
                                               @input="query = $event.target.value" />
                                        <ul class="space-y-2">
                                            <template x-for="c in (query ? filtered : clientes)" :key="c.id">
                                                <li>
                                                    <a :href="'{{ route('abonos.create') }}?cliente=' + c.id"
                                                       class="block px-3 py-2 rounded-md hover:bg-bg-300">
                                                        <div class="text-text-100 font-medium" x-text="c.nombre"></div>
                                                        <div class="text-text-200 text-sm"
                                                             x-text="(c.identificacion ? c.identificacion + ' · ' : '') + 'Saldo: ' + c.saldo"></div>
                                                    </a>
                                                </li>
                                            </template>
                                        </ul>
                                        <div class="mt-4 flex justify-center">
                                            <button type="button" onclick="document.getElementById('cliente-modal').style.display='none'"
                                                    class="px-4 py-2 bg-bg-300 text-text-100 text-sm font-medium rounded-md hover:bg-bg-300/80">
                                                {{ __('Cerrar') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        function clienteSelector(clientes) {
            return {
                clientes: clientes,
                query: '',
                get filtered() {
                    if (!this.query) return [];
                    const q = this.query.toLowerCase();
                    return this.clientes.filter(c =>
                        (c.nombre || '').toLowerCase().includes(q) ||
                        (c.identificacion || '').toLowerCase().includes(q)
                    );
                }
            };
        }
    </script>
</x-app-layout>
