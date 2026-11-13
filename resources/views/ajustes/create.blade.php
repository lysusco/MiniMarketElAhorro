<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-text-100 leading-tight">
            {{ __('Ajuste de inventario') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-bg-200 border border-bg-300 overflow-hidden shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('ajustes.store') }}" class="p-6 space-y-6"
                      x-data='ajusteForm(@json($productos))'>
                    @csrf

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
                                <option value="daño" @selected(old('motivo') === 'daño')>{{ __('Daño') }}</option>
                                <option value="vencimiento" @selected(old('motivo') === 'vencimiento')>{{ __('Vencimiento') }}</option>
                                <option value="pérdida" @selected(old('motivo') === 'pérdida')>{{ __('Pérdida') }}</option>
                                <option value="otro" @selected(old('motivo') === 'otro')>{{ __('Otro') }}</option>
                            </x-select>
                            <x-input-error :messages="$errors->get('motivo')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="observacion" :value="__('Observación')" />
                            <x-input id="observacion" name="observacion" type="text" class="block mt-1 w-full"
                                      :value="old('observacion')" />
                            <x-input-error :messages="$errors->get('observacion')" class="mt-2" />
                        </div>
                    </div>

                    <div>
                        <h3 class="text-sm font-medium text-text-100 mb-2">{{ __('Agregar producto') }}</h3>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end bg-bg-300/30 p-3 rounded-md mb-4">
                            <div class="sm:col-span-1">
                                <label class="block text-xs text-text-200 mb-1">{{ __('Producto') }}</label>

                                <div class="flex gap-2">
                                    <input type="text" x-model="productoQuery"
                                           :disabled="!!current.producto_id"
                                           placeholder="{{ __('Buscar por nombre o código...') }}"
                                           class="bg-bg-100 border-bg-300 text-text-100 focus:border-accent-200 focus:ring-accent-200 rounded-md shadow-sm w-full disabled:opacity-50 disabled:cursor-not-allowed" />
                                    <button type="button" onclick="document.getElementById('producto-modal-ajuste').style.display='flex'"
                                            x-show="!current.producto_id"
                                            class="px-3 bg-bg-300 text-text-100 rounded-md hover:bg-bg-300/80"
                                            title="{{ __('Listar productos') }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M21 21l-4.35-4.35M11 18a7 7 0 100-14 7 7 0 000 14z" />
                                        </svg>
                                    </button>
                                    <button type="button" @click="cambiarProducto()"
                                            x-show="current.producto_id" x-cloak
                                            class="px-3 bg-bg-300 text-text-100 rounded-md hover:bg-bg-300/80"
                                            title="{{ __('Cambiar producto') }}">
                                        {{ __('Cambiar') }}
                                    </button>
                                </div>

                                <ul x-show="productoQuery && filteredProductos.length" x-cloak
                                    class="mt-1 bg-bg-100 border border-bg-300 rounded-md shadow-lg max-h-40 overflow-y-auto">
                                    <template x-for="p in filteredProductos" :key="p.id">
                                        <li @click="seleccionarProducto(p)"
                                            class="px-3 py-2 text-sm text-text-100 hover:bg-bg-300 cursor-pointer">
                                            <span x-text="p.nombre"></span>
                                            <span class="text-text-200" x-text="p.codigo ? ' - ' + p.codigo : ''"></span>
                                        </li>
                                    </template>
                                </ul>

                                <div id="producto-modal-ajuste" style="display:none;"
                                     class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
                                     onclick="if(event.target===this)this.style.display='none'">
                                    <div class="bg-bg-200 border border-bg-300 rounded-lg shadow-xl p-6 w-full max-w-md mx-auto max-h-[80vh] overflow-y-auto">
                                        <h3 class="text-text-100 font-semibold mb-4">{{ __('Seleccionar producto') }}</h3>
                                        <input type="text" placeholder="{{ __('Buscar...') }}"
                                               class="bg-bg-100 border-bg-300 text-text-100 rounded-md shadow-sm w-full mb-3"
                                               @input="productoQuery = $event.target.value" />
                                        <ul class="space-y-2">
                                            <template x-for="p in (productoQuery ? filteredProductos : productos)" :key="p.id">
                                                <li @click="seleccionarProducto(p); document.getElementById('producto-modal-ajuste').style.display='none'"
                                                    class="px-3 py-2 rounded-md hover:bg-bg-300 cursor-pointer">
                                                    <div class="text-text-100 font-medium" x-text="p.nombre"></div>
                                                    <div class="text-text-200 text-sm" x-text="p.codigo + ' — stock: ' + p.stock_actual"></div>
                                                </li>
                                            </template>
                                        </ul>
                                        <div class="mt-4 flex justify-center">
                                            <button type="button" onclick="document.getElementById('producto-modal-ajuste').style.display='none'"
                                                    class="px-4 py-2 bg-bg-300 text-text-100 text-sm font-medium rounded-md hover:bg-bg-300/80">
                                                {{ __('Cerrar') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
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

                    <div id="error-modal" style="display:none;"
                         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
                         onclick="if(event.target===this)this.style.display='none'">
                        <div class="bg-bg-200 border border-bg-300 rounded-lg shadow-xl p-6 w-full max-w-xs mx-auto">
                            <div class="flex justify-center mb-4">
                                <div class="w-12 h-12 rounded-full bg-red-500/15 flex items-center justify-center">
                                    <svg class="w-7 h-7 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </div>
                            </div>
                            <p id="error-modal-msg" class="text-text-100 text-center mb-4"></p>
                            <div class="flex justify-center">
                                <button type="button" onclick="document.getElementById('error-modal').style.display='none'"
                                        class="px-4 py-2 bg-bg-300 text-text-100 text-sm font-medium rounded-md hover:bg-bg-300/80">
                                    {{ __('Cerrar') }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="submit" name="finalizar" value="1"
                                class="inline-flex items-center px-4 py-2 bg-primary-100 text-text-100 text-sm font-medium rounded-md hover:bg-primary-200">
                            {{ __('Registrar ajuste') }}
                        </button>
                        <button type="button" onclick="cancelarAjuste(this)"
                                class="inline-flex items-center px-4 py-2 bg-bg-200 border border-bg-300 text-sm font-medium rounded-md text-text-100 hover:bg-bg-300">
                            {{ __('Cancelar') }}
                        </button>
                        <a href="{{ route('ajustes.index') }}"
                           class="inline-flex items-center px-4 py-2 bg-bg-200 border border-bg-300 text-sm font-medium rounded-md text-text-100 hover:bg-bg-300">
                            {{ __('Volver') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function cancelarAjuste(btn) {
            const form = btn.closest('form');
            const data = window.Alpine ? window.Alpine.$data(form) : null;
            if (data && Array.isArray(data.items) && data.items.length > 0) {
                const hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = 'finalizar';
                hidden.value = '0';
                form.appendChild(hidden);
                form.submit();
            } else {
                window.location.href = '{{ route('ajustes.index') }}';
            }
        }

        function mostrarError(msg) {
            const el = document.getElementById('error-modal-msg');
            if (el) el.textContent = msg;
            const m = document.getElementById('error-modal');
            if (m) m.style.display = 'flex';
        }

        function ajusteForm(productos) {
            return {
                productos: productos,
                current: { producto_id: '', producto_nombre: '', cantidad: 1 },
                items: [],
                productoQuery: '',
                productoOpen: false,
                get filteredProductos() {
                    if (!this.productoQuery) return [];
                    const q = this.productoQuery.toLowerCase();
                    return this.productos.filter(p =>
                        (p.nombre || '').toLowerCase().includes(q) ||
                        (p.codigo || '').toLowerCase().includes(q)
                    );
                },
                seleccionarProducto(p) {
                    this.current.producto_id = p.id;
                    this.current.producto_nombre = p.nombre;
                    this.productoQuery = p.nombre + (p.codigo ? ' - ' + p.codigo : '');
                    this.productoOpen = false;
                },
                cambiarProducto() {
                    this.current.producto_id = '';
                    this.current.producto_nombre = '';
                    this.productoQuery = '';
                },
                addItem() {
                    const motivo = document.querySelector('select[name="motivo"]')?.value;
                    if (!motivo) {
                        mostrarError('{{ __('Debe seleccionar un motivo antes de agregar productos.') }}');
                        return;
                    }
                    if (!this.current.producto_id) return;
                    const existente = this.items.find(i => String(i.producto_id) === String(this.current.producto_id));
                    if (existente) {
                        existente.cantidad += Number(this.current.cantidad) || 1;
                    } else {
                        this.items.push({
                            producto_id: this.current.producto_id,
                            producto_nombre: this.current.producto_nombre,
                            cantidad: Number(this.current.cantidad) || 1,
                        });
                    }
                    this.current = { producto_id: '', producto_nombre: '', cantidad: 1 };
                },
                removeItem(index) {
                    this.items.splice(index, 1);
                }
            };
        }
    </script>
</x-app-layout>