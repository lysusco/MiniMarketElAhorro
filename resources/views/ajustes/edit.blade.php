<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-text-100 leading-tight">
            {{ __('Editar ajuste de inventario #') }}{{ $ajuste->id }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-bg-200 border border-bg-300 overflow-hidden shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('ajustes.update', $ajuste) }}" class="p-6 space-y-6"
                      x-data='ajusteForm(@json($productos), @json($items))'>
                    @csrf
                    @method('PUT')

                    @if (session('success'))
                        <div class="mb-4">
                            <x-alert type="success">{{ session('success') }}</x-alert>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="motivo" :value="__('Motivo')" />
                            <x-select id="motivo" name="motivo" class="block mt-1 w-full" required>
                                <option value="">{{ __('Seleccione un motivo') }}</option>
                                <option value="daño" @selected(old('motivo', $ajuste->motivo) === 'daño')>{{ __('Daño') }}</option>
                                <option value="vencimiento" @selected(old('motivo', $ajuste->motivo) === 'vencimiento')>{{ __('Vencimiento') }}</option>
                                <option value="pérdida" @selected(old('motivo', $ajuste->motivo) === 'pérdida')>{{ __('Pérdida') }}</option>
                                <option value="otro" @selected(old('motivo', $ajuste->motivo) === 'otro')>{{ __('Otro') }}</option>
                            </x-select>
                            <x-input-error :messages="$errors->get('motivo')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="observacion" :value="__('Observación')" />
                            <x-input id="observacion" name="observacion" type="text" class="block mt-1 w-full"
                                      :value="old('observacion', $ajuste->observacion)" />
                            <x-input-error :messages="$errors->get('observacion')" class="mt-2" />
                        </div>
                    </div>

                    <div>
                        <h3 class="text-sm font-medium text-text-100 mb-2">{{ __('Agregar producto') }}</h3>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end bg-bg-300/30 p-3 rounded-md mb-4">
                            <div class="sm:col-span-1">
                                <label class="block text-xs text-text-200 mb-1">{{ __('Producto') }}</label>
                                <select x-model="current.producto_id"
                                        class="bg-bg-100 border-bg-300 text-text-100 focus:border-accent-200 focus:ring-accent-200 rounded-md shadow-sm w-full">
                                    <option value="">{{ __('Seleccione') }}</option>
                                    @foreach ($productos as $producto)
                                        <option value="{{ $producto->id }}">{{ $producto->nombre }} ({{ $producto->codigo }}) — stock: {{ $producto->stock_actual }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs text-text-200 mb-1">{{ __('Cantidad') }}</label>
                                <input type="number" min="1" x-model="current.cantidad"
                                       class="bg-bg-100 border-bg-300 text-text-100 focus:border-accent-200 focus:ring-accent-200 rounded-md shadow-sm w-full" />
                            </div>
                            <div class="flex justify-end">
                                <button type="button" @click="addItem()"
                                        class="px-4 py-2 bg-primary-100 text-text-100 text-sm font-medium rounded-md hover:bg-primary-200">
                                    {{ __('Agregar producto') }}
                                </button>
                            </div>
                        </div>

                        <h3 class="text-sm font-medium text-text-100 mb-2">{{ __('Productos a ajustar') }}</h3>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-bg-300 text-sm">
                                <thead>
                                    <tr class="text-left text-text-200">
                                        <th class="py-2 pe-4 font-medium">{{ __('Producto') }}</th>
                                        <th class="py-2 pe-4 font-medium text-end">{{ __('Cantidad') }}</th>
                                        <th class="py-2 pe-4 font-medium text-end">{{ __('Acciones') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-bg-300">
                                    <template x-for="(item, index) in items" :key="index">
                                        <tr>
                                            <td class="py-3 pe-4 text-text-100" x-text="item.producto_nombre"></td>
                                            <td class="py-3 pe-4 text-end">
                                                <input type="number" min="1" :name="'items['+index+'][cantidad]'" x-model="item.cantidad"
                                                       class="bg-bg-100 border-bg-300 text-text-100 rounded-md shadow-sm w-24 text-end" required />
                                            </td>
                                            <td class="py-3 pe-4 text-end">
                                                <input type="hidden" :name="'items['+index+'][producto_id]'" :value="item.producto_id" />
                                                <button type="button" @click="removeItem(index)"
                                                        class="text-red-400 hover:text-red-300 text-sm font-medium">
                                                    {{ __('Quitar') }}
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                    <tr x-show="items.length === 0" x-cloak>
                                        <td colspan="3" class="py-6 text-center text-text-200">
                                            {{ __('No hay productos agregados.') }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        @if ($errors->has('items') || $errors->has('items.*'))
                            <p class="mt-2 text-sm text-red-300">
                                {{ $errors->first('items') ?: $errors->first('items.*.producto_id') ?: $errors->first('items.*.cantidad') }}
                            </p>
                        @endif
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="submit" name="finalizar" value="0"
                                class="inline-flex items-center px-4 py-2 bg-primary-100 text-text-100 text-sm font-medium rounded-md hover:bg-primary-200">
                            {{ __('Guardar cambios') }}
                        </button>
                        @if ($ajuste->estado === \App\Models\Ajuste::ESTADO_EN_PROGRESO)
                            <button type="submit" form="finalizar-ajuste-{{ $ajuste->id }}"
                                    class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-500">
                                {{ __('Finalizar') }}
                            </button>
                        @endif
                        <a href="{{ route('ajustes.index') }}"
                           class="inline-flex items-center px-4 py-2 bg-bg-200 border border-bg-300 text-sm font-medium rounded-md text-text-100 hover:bg-bg-300">
                            {{ __('Volver') }}
                        </a>
                    </div>
                </form>

                @if ($ajuste->estado === \App\Models\Ajuste::ESTADO_EN_PROGRESO)
                    <form id="finalizar-ajuste-{{ $ajuste->id }}" method="POST" action="{{ route('ajustes.finalizar', $ajuste) }}" class="hidden">
                        @csrf
                    </form>
                @endif
            </div>
        </div>
    </div>

    <script>
        function mostrarError(msg) {
            const el = document.getElementById('error-modal-msg');
            if (el) el.textContent = msg;
            const m = document.getElementById('error-modal');
            if (m) m.style.display = 'flex';
        }

        function ajusteForm(productos, items) {
            return {
                productos: productos,
                current: { producto_id: '', cantidad: 1 },
                items: (items || []).map(i => ({
                    producto_id: i.producto_id,
                    producto_nombre: i.producto_nombre || '',
                    cantidad: Number(i.cantidad) || 1,
                })),
                addItem() {
                    const motivo = document.querySelector('select[name="motivo"]')?.value;
                    if (!motivo) {
                        mostrarError('{{ __('Debe seleccionar un motivo antes de agregar productos.') }}');
                        return;
                    }
                    if (!this.current.producto_id) return;
                    const prod = this.productos.find(p => String(p.id) === String(this.current.producto_id));
                    const existente = this.items.find(i => String(i.producto_id) === String(this.current.producto_id));
                    if (existente) {
                        existente.cantidad += Number(this.current.cantidad) || 1;
                    } else {
                        this.items.push({
                            producto_id: this.current.producto_id,
                            producto_nombre: prod ? prod.nombre : '',
                            cantidad: Number(this.current.cantidad) || 1,
                        });
                    }
                    this.current = { producto_id: '', cantidad: 1 };
                },
                removeItem(index) {
                    this.items.splice(index, 1);
                }
            };
        }
    </script>
</x-app-layout>