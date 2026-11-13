<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-text-100 leading-tight">
            {{ __('Editar entrada de mercancía #') }}{{ $compra->id }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-bg-200 border border-bg-300 overflow-hidden shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('compras.update', $compra) }}" class="p-6 space-y-6"
                      x-data='compraForm(@json($productos), @json($items))'>
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div x-data='proveedorSelector(@json($proveedores), {{ $compra->proveedor_id }})'>
                            <x-input-label for="proveedor_id" :value="__('Proveedor')" />
                            <input type="hidden" name="proveedor_id" :value="selected?.id" />

                            <div class="flex gap-2 mt-1">
                                <input type="text" x-model="query" @input="filtrar()"
                                       :disabled="!!selected"
                                       placeholder="{{ __('Buscar por NIT o nombre...') }}"
                                       class="bg-bg-100 border-bg-300 text-text-100 focus:border-accent-200 focus:ring-accent-200 rounded-md shadow-sm w-full disabled:opacity-50 disabled:cursor-not-allowed" />
                                <button type="button" onclick="document.getElementById('proveedor-modal-edit').style.display='flex'"
                                        x-show="!selected"
                                        class="px-3 bg-bg-300 text-text-100 rounded-md hover:bg-bg-300/80"
                                        title="{{ __('Listar proveedores') }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M21 21l-4.35-4.35M11 18a7 7 0 100-14 7 7 0 000 14z" />
                                    </svg>
                                </button>
                            </div>

                            <ul x-show="query && filtered.length" x-cloak
                                class="mt-1 bg-bg-100 border border-bg-300 rounded-md shadow-lg max-h-40 overflow-y-auto">
                                <template x-for="p in filtered" :key="p.id">
                                    <li @click="seleccionar(p)"
                                        class="px-3 py-2 text-sm text-text-100 hover:bg-bg-300 cursor-pointer">
                                        <span x-text="p.nombre"></span>
                                        <span class="text-text-200" x-text="p.nit ? ' - ' + p.nit : ''"></span>
                                    </li>
                                </template>
                            </ul>

                            <div x-show="selected" x-cloak
                                 class="mt-2 text-sm text-text-200 flex items-center gap-2">
                                {{ __('Seleccionado:') }}
                                <span class="text-text-100 font-medium" x-text="selected?.nombre"></span>
                                <span x-text="selected?.nit ? ' (' + selected.nit + ')' : ''"></span>
                            </div>

                            <x-input-error :messages="$errors->get('proveedor_id')" class="mt-2" />

                            <div id="proveedor-modal-edit" style="display:none;"
                                 class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
                                 onclick="if(event.target===this)this.style.display='none'">
                                <div class="bg-bg-200 border border-bg-300 rounded-lg shadow-xl p-6 w-full max-w-md mx-auto max-h-[80vh] overflow-y-auto">
                                    <h3 class="text-text-100 font-semibold mb-4">{{ __('Seleccionar proveedor') }}</h3>
                                    <ul class="space-y-2">
                                        <template x-for="p in proveedores" :key="p.id">
                                            <li @click="seleccionar(p); document.getElementById('proveedor-modal-edit').style.display='none'"
                                                class="px-3 py-2 rounded-md hover:bg-bg-300 cursor-pointer">
                                                <div class="text-text-100 font-medium" x-text="p.nombre"></div>
                                                <div class="text-text-200 text-sm" x-text="p.nit ?? '—'"></div>
                                            </li>
                                        </template>
                                    </ul>
                                    <div class="mt-4 flex justify-center">
                                        <button type="button" onclick="document.getElementById('proveedor-modal-edit').style.display='none'"
                                                class="px-4 py-2 bg-bg-300 text-text-100 text-sm font-medium rounded-md hover:bg-bg-300/80">
                                            {{ __('Cerrar') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <x-input-label for="fecha" :value="__('Fecha')" />
                            <x-input id="fecha" name="fecha" type="date" class="block mt-1 w-full"
                                      :value="old('fecha', $compra->fecha->format('Y-m-d'))" required />
                            <x-input-error :messages="$errors->get('fecha')" class="mt-2" />
                        </div>
                    </div>

                    <div>
                        <h3 class="text-sm font-medium text-text-100 mb-2">{{ __('Agregar producto') }}</h3>

                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-end bg-bg-300/30 p-3 rounded-md mb-4">
                            <div class="sm:col-span-2">
                                <label class="block text-xs text-text-200 mb-1">{{ __('Producto') }}</label>

                                <div class="flex gap-2">
                                    <input type="text" x-model="productoQuery"
                                           :disabled="!!current.producto_id"
                                           placeholder="{{ __('Buscar por nombre o código...') }}"
                                           class="bg-bg-100 border-bg-300 text-text-100 focus:border-accent-200 focus:ring-accent-200 rounded-md shadow-sm w-full disabled:opacity-50 disabled:cursor-not-allowed" />
                                    <button type="button" onclick="document.getElementById('producto-modal-edit').style.display='flex'"
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

                                <div id="producto-modal-edit" style="display:none;"
                                     class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
                                     onclick="if(event.target===this)this.style.display='none'">
                                    <div class="bg-bg-200 border border-bg-300 rounded-lg shadow-xl p-6 w-full max-w-md mx-auto max-h-[80vh] overflow-y-auto">
                                        <h3 class="text-text-100 font-semibold mb-4">{{ __('Seleccionar producto') }}</h3>
                                        <input type="text" placeholder="{{ __('Buscar...') }}"
                                               class="bg-bg-100 border-bg-300 text-text-100 rounded-md shadow-sm w-full mb-3"
                                               @input="productoQuery = $event.target.value" />
                                        <ul class="space-y-2">
                                            <template x-for="p in (productoQuery ? filteredProductos : productos)" :key="p.id">
                                                <li @click="seleccionarProducto(p); document.getElementById('producto-modal-edit').style.display='none'"
                                                    class="px-3 py-2 rounded-md hover:bg-bg-300 cursor-pointer">
                                                    <div class="text-text-100 font-medium" x-text="p.nombre"></div>
                                                    <div class="text-text-200 text-sm" x-text="p.codigo"></div>
                                                </li>
                                            </template>
                                        </ul>
                                        <div class="mt-4 flex justify-center">
                                            <button type="button" onclick="document.getElementById('producto-modal-edit').style.display='none'"
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
                            <div>
                                <label class="block text-xs text-text-200 mb-1">{{ __('Costo unitario') }}</label>
                                <input type="number" step="0.01" min="0" x-model="current.costo_unitario"
                                       class="bg-bg-100 border-bg-300 text-text-100 focus:border-accent-200 focus:ring-accent-200 rounded-md shadow-sm w-full" />
                            </div>
                            <div class="sm:col-span-4 flex justify-end">
                                <button type="button" @click="addItem()"
                                        class="px-4 py-2 bg-primary-100 text-text-100 text-sm font-medium rounded-md hover:bg-primary-200">
                                    {{ __('Agregar producto') }}
                                </button>
                            </div>
                        </div>

                        <h3 class="text-sm font-medium text-text-100 mb-2">{{ __('Productos recibidos') }}</h3>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-bg-300 text-sm">
                                <thead>
                                    <tr class="text-left text-text-200">
                                        <th class="py-2 pe-4 font-medium">{{ __('Producto') }}</th>
                                        <th class="py-2 pe-4 font-medium text-end">{{ __('Cantidad') }}</th>
                                        <th class="py-2 pe-4 font-medium text-end">{{ __('Costo unitario') }}</th>
                                        <th class="py-2 pe-4 font-medium text-end">{{ __('Subtotal') }}</th>
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
                                                <input type="number" step="0.01" min="0" :name="'items['+index+'][costo_unitario]'" x-model="item.costo_unitario"
                                                       class="bg-bg-100 border-bg-300 text-text-100 rounded-md shadow-sm w-28 text-end" required />
                                            </td>
                                            <td class="py-3 pe-4 text-text-200 text-end" x-text="(item.cantidad * item.costo_unitario).toFixed(2)"></td>
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
                                        <td colspan="5" class="py-6 text-center text-text-200">
                                            {{ __('No hay productos agregados.') }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        @if ($errors->has('items') || $errors->has('items.*'))
                            <p class="mt-2 text-sm text-red-300">
                                {{ $errors->first('items') ?: $errors->first('items.*.producto_id') ?: $errors->first('items.*.cantidad') ?: $errors->first('items.*.costo_unitario') }}
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
                        <button type="submit" name="finalizar" value="0"
                                class="inline-flex items-center px-4 py-2 bg-primary-100 text-text-100 text-sm font-medium rounded-md hover:bg-primary-200">
                            {{ __('Guardar cambios') }}
                        </button>
                        @if ($compra->estado === \App\Models\Compra::ESTADO_EN_PROGRESO)
                            <form method="POST" action="{{ route('compras.finalizar', $compra) }}" class="inline">
                                @csrf
                                <button type="submit"
                                        class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-500">
                                    {{ __('Finalizar') }}
                                </button>
                            </form>
                        @endif
                        <a href="{{ route('compras.index') }}"
                           class="inline-flex items-center px-4 py-2 bg-bg-200 border border-bg-300 text-sm font-medium rounded-md text-text-100 hover:bg-bg-300">
                            {{ __('Volver') }}
                        </a>
                    </div>
                </form>
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

        function compraForm(productos, items) {
            return {
                productos: productos,
                current: { producto_id: '', producto_nombre: '', cantidad: 1, costo_unitario: 0 },
                items: (items || []).map(i => ({
                    producto_id: i.producto_id,
                    producto_nombre: i.producto_nombre || '',
                    cantidad: Number(i.cantidad) || 1,
                    costo_unitario: Number(i.costo_unitario) || 0,
                })),
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
                    const proveedorId = document.querySelector('input[name="proveedor_id"]')?.value;
                    if (!proveedorId) {
                        mostrarError('{{ __('Debe seleccionar un proveedor activo antes de agregar productos.') }}');
                        return;
                    }
                    if (!this.current.producto_id) return;

                    const cantidad = Number(this.current.cantidad) || 1;
                    const costo = Number(this.current.costo_unitario) || 0;

                    const existente = this.items.find(i =>
                        String(i.producto_id) === String(this.current.producto_id) &&
                        Number(i.costo_unitario) === costo
                    );

                    if (existente) {
                        existente.cantidad += cantidad;
                    } else {
                        this.items.push({
                            producto_id: this.current.producto_id,
                            producto_nombre: this.current.producto_nombre,
                            cantidad: cantidad,
                            costo_unitario: costo,
                        });
                    }

                    this.current = { producto_id: '', cantidad: 1, costo_unitario: 0 };
                },
                removeItem(index) {
                    this.items.splice(index, 1);
                }
            };
        }

        function proveedorSelector(proveedores, selectedId) {
            return {
                proveedores: proveedores,
                query: '',
                open: false,
                selected: (function () {
                    if (!selectedId) return null;
                    return proveedores.find(p => String(p.id) === String(selectedId)) || null;
                })(),
                get filtered() {
                    if (!this.query) return [];
                    const q = this.query.toLowerCase();
                    return this.proveedores.filter(p =>
                        (p.nombre || '').toLowerCase().includes(q) ||
                        (p.nit || '').toLowerCase().includes(q)
                    );
                },
                filtrar() {},
                seleccionar(p) {
                    this.selected = p;
                    this.query = p.nombre + (p.nit ? ' - ' + p.nit : '');
                }
            };
        }
    </script>
</x-app-layout>