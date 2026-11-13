{{-- Formulario compartido para crear y editar proveedores --}}

<!-- Nombre -->
<div>
    <x-input-label for="nombre" :value="__('Nombre')" />
    <x-input id="nombre" name="nombre" type="text" class="block mt-1 w-full"
                  :value="old('nombre', $proveedor?->nombre)" required autofocus />
    <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
</div>

<!-- NIT -->
<div>
    <x-input-label for="nit" :value="__('NIT')" />
    <x-input id="nit" name="nit" type="text" class="block mt-1 w-full"
                  :value="old('nit', $proveedor?->nit)" />
    <x-input-error :messages="$errors->get('nit')" class="mt-2" />
</div>

<!-- Teléfono -->
<div>
    <x-input-label for="telefono" :value="__('Teléfono')" />
    <x-input id="telefono" name="telefono" type="text" class="block mt-1 w-full"
                  :value="old('telefono', $proveedor?->telefono)" />
    <x-input-error :messages="$errors->get('telefono')" class="mt-2" />
</div>

<!-- Correo -->
<div>
    <x-input-label for="correo" :value="__('Correo')" />
    <x-input id="correo" name="correo" type="email" class="block mt-1 w-full"
                  :value="old('correo', $proveedor?->correo)" />
    <x-input-error :messages="$errors->get('correo')" class="mt-2" />
</div>

<!-- Dirección -->
<div>
    <x-input-label for="direccion" :value="__('Dirección')" />
    <x-textarea id="direccion" name="direccion" class="block mt-1 w-full" rows="3">{{ old('direccion', $proveedor?->direccion) }}</x-textarea>
    <x-input-error :messages="$errors->get('direccion')" class="mt-2" />
</div>

<!-- Estado -->
<div>
    <label for="activo" class="inline-flex items-center">
        <input id="activo" type="checkbox" name="activo" value="1"
               class="rounded border-bg-300 bg-bg-100 text-primary-100 shadow-sm focus:ring-primary-300"
               @checked(old('activo', $proveedor?->activo ?? true))>
        <span class="ms-2 text-sm text-text-200">{{ __('Proveedor activo') }}</span>
    </label>
</div>