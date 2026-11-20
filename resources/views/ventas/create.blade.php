<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-text-100 leading-tight">
            {{ __('Nueva venta') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-bg-200 border border-bg-300 overflow-hidden shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('ventas.store') }}" class="p-6 space-y-6"
                      x-data='ventaForm(@json($productos))'>
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="fecha" :value="__('Fecha')" />
                            <x-input id="fecha" name="fecha" type="date" class="block mt-1 w-full"
                                      :value="old('fecha', now()->toDateString())" required />
                            <x-input-error :messages="$errors->get('fecha')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label :value="__('Tipo de venta')" />
                            <div class="flex items-center gap-4 mt-2">
                                <label class="flex items-center gap-2 text-sm text-text-100">
                                    <input type="radio" name="tipo_venta" value="contado" x-model="tipoVenta"
                                           class="text-accent-200 focus:ring-accent-200 bg-bg-100 border-bg-300" />
                                    {{ __('Contado') }}
                                </label>
                                <label class="flex items-center gap-2 text-sm text-text-100">
                                    <input type="radio" name="tipo_venta" value="fiado" x-model="tipoVenta"
                                           class="text-accent-200 focus:ring-accent-200 bg-bg-100 border-bg-300" />
                                    {{ __('Fiado') }}
                                </label>
                            </div>
                            <x-input-error :messages="$errors->get('tipo_venta')" class="mt-2" />
                        </div>
                    </div>

                    <!-- Cliente (solo ventas fiadas) -->
                    <div x-show="tipoVenta === 'fiado'" x-cloak x-data='clienteSelector(@json($clientes))'>
                        <x-input-label for="cliente_id" :value="__('Cliente')" />
                        <input type="hidden" name="cliente_id"
                               :value="tipoVenta === 'fiado' && selected ? selected.id : ''" />

                        <div class="flex gap-2 mt-1">
                            <input type="text" x-model="query" @input="filtrar()"
                                   :disabled="!!selected"
                                   placeholder="{{ __('Buscar por nombre o identificación...') }}"
                                   class="bg-bg-100 border-bg-300 text-text-100 focus:border-accent-200 focus:ring-accent-200 rounded-md shadow-sm w-full disabled:opacity-50 disabled:cursor-not-allowed" />
                            <button type="button" onclick="document.getElementById('cliente-modal').style.display='flex'"
                                    x-show="!selected"
                                    class="px-3 bg-bg-300 text-text-100 rounded-md hover:bg-bg-300/80"
                                    title="{{ __('Listar clientes') }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M21 21l-4.35-4.35M11 18a7 7 0 100-14 7 7 0 000 14z" />
                                </svg>
                            </button>
                            <button type="button" @click="selected = null; query = ''"
                                    x-show="selected" x-cloak
                                    class="px-3 bg-bg-300 text-text-100 rounded-md hover:bg-bg-300/80"
                                    title="{{ __('Cambiar cliente') }}">
                                {{ __('Cambiar') }}
                            </button>
                        </div>

                        <!-- Autocompletado -->
                        <ul x-show="query && filtered.length" x-cloak
                            class="mt-1 bg-bg-100 border border-bg-300 rounded-md shadow-lg max-h-40 overflow-y-auto">
                            <template x-for="c in filtered" :key="c.id">
                                <li @click="seleccionar(c)"
                                    class="px-3 py-2 text-sm text-text-100 hover:bg-bg-300 cursor-pointer">
                                    <span x-text="c.nombre"></span>
                                    <span class="text-text-200" x-text="c.identificacion ? ' - ' + c.identificacion : ''"></span>
                                </li>
                            </template>
                        </ul>

                        <x-input-error :messages="$errors->get('cliente_id')" class="mt-2" />

                        <!-- Modal de listado -->
                        <div id="cliente-modal" style="display:none;"
                             class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
                             onclick="if(event.target===this)this.style.display='none'">
                            <div class="bg-bg-200 border border-bg-300 rounded-lg shadow-xl p-6 w-full max-w-md mx-auto max-h-[80vh] overflow-y-auto">
                                <h3 class="text-text-100 font-semibold mb-4">{{ __('Seleccionar cliente') }}</h3>
                                <ul class="space-y-2">
                                    <template x-for="c in clientes" :key="c.id">
                                        <li @click="seleccionar(c); document.getElementById('cliente-modal').style.display='none'"
                                            class="px-3 py-2 rounded-md hover:bg-bg-300 cursor-pointer">
                                            <div class="text-text-100 font-medium" x-text="c.nombre"></div>
                                            <div class="text-text-200 text-sm" x-text="c.identificacion ?? '—'"></div>
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

                    <div>
                        <x-input-label for="descuento" :value="__('Descuento')" />
                        <input id="descuento" name="descuento" type="number" step="0.01" min="0"
                               x-model="descuento" value="{{ old('descuento', '0') }}"
                               class="bg-bg-100 border-bg-300 text-text-100 focus:border-accent-200 focus:ring-accent-200 rounded-md shadow-sm block mt-1 w-full sm:max-w-xs" />
                        <x-input-error :messages="$errors->get('descuento')" class="mt-2" />
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
                                    <button type="button" onclick="document.getElementById('producto-modal').style.display='flex'"
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

                                <div id="producto-modal" style="display:none;"
                                     class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
                                     onclick="if(event.target===this)this.style.display='none'">
                                    <div class="bg-bg-200 border border-bg-300 rounded-lg shadow-xl p-6 w-full max-w-md mx-auto max-h-[80vh] overflow-y-auto">
                                        <h3 class="text-text-100 font-semibold mb-4">{{ __('Seleccionar producto') }}</h3>
                                        <input type="text" placeholder="{{ __('Buscar...') }}"
                                               class="bg-bg-100 border-bg-300 text-text-100 rounded-md shadow-sm w-full mb-3"
                                               @input="productoQuery = $event.target.value; productoOpen = true" />
                                        <ul class="space-y-2">
                                            <template x-for="p in (productoQuery ? filteredProductos : productos)" :key="p.id">
                                                <li @click="seleccionarProducto(p); document.getElementById('producto-modal').style.display='none'"
                                                    class="px-3 py-2 rounded-md hover:bg-bg-300 cursor-pointer">
                                                    <div class="text-text-100 font-medium" x-text="p.nombre"></div>
                                                    <div class="text-text-200 text-sm">
                                                        <span x-text="p.codigo"></span>
                                                        <span x-text="' - ' + Number(p.precio_venta).toFixed(2)"></span>
                                                    </div>
                                                </li>
                                            </template>
                                        </ul>
                                        <div class="mt-4 flex justify-center">
                                            <button type="button" onclick="document.getElementById('producto-modal').style.display='none'"
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
                                <label class="block text-xs text-text-200 mb-1">{{ __('Precio unitario') }}</label>
                                <input type="text" :value="Number(current.precio_venta || 0).toFixed(2)" disabled
                                       class="bg-bg-100 border-bg-300 text-text-200 rounded-md shadow-sm w-full text-end disabled:opacity-50" />
                            </div>
                            <div class="sm:col-span-4 flex justify-end">
                                <button type="button" @click="addItem()"
                                        class="px-4 py-2 bg-primary-100 text-text-100 text-sm font-medium rounded-md hover:bg-primary-200">
                                    {{ __('Agregar producto') }}
                                </button>
                            </div>
                        </div>

                        <h3 class="text-sm font-medium text-text-100 mb-2">{{ __('Productos de la venta') }}</h3>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-bg-300 text-sm">
                                <thead>
                                    <tr class="text-left text-text-200">
                                        <th class="py-2 pe-4 font-medium">{{ __('Producto') }}</th>
                                        <th class="py-2 pe-4 font-medium text-end">{{ __('Cantidad') }}</th>
                                        <th class="py-2 pe-4 font-medium text-end">{{ __('Precio unitario') }}</th>
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
                                            <td class="py-3 pe-4 text-text-200 text-end" x-text="Number(item.precio_venta).toFixed(2)"></td>
                                            <td class="py-3 pe-4 text-text-200 text-end" x-text="(item.cantidad * item.precio_venta).toFixed(2)"></td>
                                            <td class="py-3 pe-4 text-end">
                                                <input type="hidden" :name="'items['+index+'][producto_id]'" :value="item.producto_id" />
                                                <button type="button" @click="removeItem(index)"
                                                        class="text-red-700 hover:text-red-600 text-sm font-medium">
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
                            <p class="mt-2 text-sm text-red-700">
                                {{ $errors->first('items') ?: $errors->first('items.*.producto_id') ?: $errors->first('items.*.cantidad') }}
                            </p>
                        @endif
                    </div>

                    <!-- Totales -->
                    <div class="flex justify-end">
                        <dl class="w-full max-w-xs space-y-2 text-sm">
                            <div class="flex justify-between">
                                <dt class="text-text-200">{{ __('Subtotal') }}</dt>
                                <dd class="text-text-100" x-text="subtotal().toFixed(2)"></dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-text-200">{{ __('Descuento') }}</dt>
                                <dd class="text-text-100" x-text="Number(descuento || 0).toFixed(2)"></dd>
                            </div>
                            <div class="flex justify-between border-t border-bg-300 pt-2">
                                <dt class="text-text-100 font-semibold">{{ __('Total') }}</dt>
                                <dd class="text-text-100 font-semibold" x-text="total().toFixed(2)"></dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Modal de error -->
                    <div id="error-modal" style="display:none;"
                         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
                         onclick="if(event.target===this)this.style.display='none'">
                        <div class="bg-bg-200 border border-bg-300 rounded-lg shadow-xl p-6 w-full max-w-xs mx-auto">
                            <div class="flex justify-center mb-4">
                                <div class="w-12 h-12 rounded-full bg-red-500/15 flex items-center justify-center">
                                    <svg class="w-7 h-7 text-red-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-primary-100 text-text-100 text-sm font-medium rounded-md hover:bg-primary-200">
                            {{ __('Registrar venta') }}
                        </button>
                        <a href="{{ route('ventas.index') }}"
                           class="inline-flex items-center px-4 py-2 bg-bg-200 border border-bg-300 text-sm font-medium rounded-md text-text-100 hover:bg-bg-300">
                            {{ __('Cancelar') }}
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

        function ventaForm(productos) {
            return {
                productos: productos,
                tipoVenta: '{{ old('tipo_venta', 'contado') }}',
                descuento: '{{ old('descuento', '0') }}',
                current: { producto_id: '', producto_nombre: '', precio_venta: 0, cantidad: 1 },
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
                    this.current.precio_venta = Number(p.precio_venta);
                    this.productoQuery = p.nombre + (p.codigo ? ' - ' + p.codigo : '');
                    this.productoOpen = false;
                },
                cambiarProducto() {
                    this.current = { producto_id: '', producto_nombre: '', precio_venta: 0, cantidad: 1 };
                    this.productoQuery = '';
                },
                addItem() {
                    if (!this.current.producto_id) {
                        mostrarError('{{ __('Debe seleccionar un producto antes de agregarlo.') }}');
                        return;
                    }

                    const cantidad = Number(this.current.cantidad) || 0;
                    if (cantidad <= 0) {
                        mostrarError('{{ __('La cantidad debe ser mayor que cero.') }}');
                        return;
                    }

                    // Los items repetidos del mismo producto se suman en una sola línea.
                    const existente = this.items.find(i =>
                        String(i.producto_id) === String(this.current.producto_id)
                    );

                    if (existente) {
                        existente.cantidad += cantidad;
                    } else {
                        this.items.push({
                            producto_id: this.current.producto_id,
                            producto_nombre: this.current.producto_nombre,
                            precio_venta: this.current.precio_venta,
                            cantidad: cantidad,
                        });
                    }

                    this.cambiarProducto();
                },
                removeItem(index) {
                    this.items.splice(index, 1);
                },
                subtotal() {
                    return this.items.reduce((suma, i) => suma + Number(i.cantidad || 0) * Number(i.precio_venta || 0), 0);
                },
                total() {
                    return this.subtotal() - Number(this.descuento || 0);
                }
            };
        }

        function clienteSelector(clientes) {
            const oldId = '{{ old('cliente_id') }}';
            const selected = oldId
                ? (clientes.find(c => String(c.id) === String(oldId)) || null)
                : null;
            return {
                clientes: clientes,
                query: selected ? (selected.nombre + (selected.identificacion ? ' - ' + selected.identificacion : '')) : '',
                open: false,
                selected: selected,
                get filtered() {
                    if (!this.query) return [];
                    const q = this.query.toLowerCase();
                    return this.clientes.filter(c =>
                        (c.nombre || '').toLowerCase().includes(q) ||
                        (c.identificacion || '').toLowerCase().includes(q)
                    );
                },
                filtrar() {},
                seleccionar(c) {
                    this.selected = c;
                    this.query = c.nombre + (c.identificacion ? ' - ' + c.identificacion : '');
                }
            };
        }
    </script>
</x-app-layout>
