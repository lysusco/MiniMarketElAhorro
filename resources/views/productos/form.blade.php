{{-- Formulario compartido para crear y editar productos --}}

<!-- Categoría -->
<div>
    <x-input-label for="categoria_id" :value="__('Categoría')" />
    <x-select id="categoria_id" name="categoria_id" class="block mt-1 w-full" required>
        <option value="">{{ __('Seleccione una categoría') }}</option>
        @foreach ($categorias as $categoria)
            <option value="{{ $categoria->id }}" @selected(old('categoria_id', $producto?->categoria_id) == $categoria->id)>
                {{ $categoria->nombre }}
            </option>
        @endforeach
    </x-select>
    <x-input-error :messages="$errors->get('categoria_id')" class="mt-2" />
</div>

<!-- Nombre -->
<div>
    <x-input-label for="nombre" :value="__('Nombre')" />
    <x-input id="nombre" name="nombre" type="text" class="block mt-1 w-full"
                  :value="old('nombre', $producto?->nombre)" required autofocus />
    <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
</div>

<!-- Código -->
<div>
    <x-input-label for="codigo" :value="__('Código')" />
    <x-input id="codigo" name="codigo" type="text" class="block mt-1 w-full"
                  :value="old('codigo', $producto?->codigo)" required />
    <x-input-error :messages="$errors->get('codigo')" class="mt-2" />
</div>

<!-- Precios -->
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <x-input-label for="precio_compra" :value="__('Precio de compra')" />
        <x-input id="precio_compra" name="precio_compra" type="number" step="0.01" min="0"
                      class="block mt-1 w-full"
                      :value="old('precio_compra', $producto?->precio_compra)" required />
        <x-input-error :messages="$errors->get('precio_compra')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="precio_venta" :value="__('Precio de venta')" />
        <x-input id="precio_venta" name="precio_venta" type="number" step="0.01" min="0"
                      class="block mt-1 w-full"
                      :value="old('precio_venta', $producto?->precio_venta)" required />
        <x-input-error :messages="$errors->get('precio_venta')" class="mt-2" />
    </div>
</div>

<!-- Stock mínimo -->
<div>
    <x-input-label for="stock_minimo" :value="__('Stock mínimo')" />
    <x-input id="stock_minimo" name="stock_minimo" type="number" min="0" class="block mt-1 w-full"
                  :value="old('stock_minimo', $producto?->stock_minimo ?? 0)" required />
    <p class="mt-1 text-xs text-text-200">
        {{ __('Cantidad a partir de la cual se considera stock bajo.') }}
    </p>
    <x-input-error :messages="$errors->get('stock_minimo')" class="mt-2" />
</div>

@if ($producto)
    <div class="text-sm text-text-200">
        {{ __('Stock actual:') }} <span class="font-medium text-text-100">{{ $producto->stock_actual }}</span>
        <p class="mt-1 text-xs">{{ __('El stock solo cambia mediante entradas, ventas o ajustes de inventario.') }}</p>
    </div>
@endif

<!-- Estado -->
<div>
    <label for="activo" class="inline-flex items-center">
        <input id="activo" type="checkbox" name="activo" value="1"
               class="rounded border-bg-300 bg-bg-100 text-primary-100 shadow-sm focus:ring-primary-300"
               @checked(old('activo', $producto?->activo ?? true))>
        <span class="ms-2 text-sm text-text-200">{{ __('Producto activo') }}</span>
    </label>
</div>