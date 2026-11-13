{{-- Formulario compartido para crear y editar categorías --}}

<!-- Nombre -->
<div>
    <x-input-label for="nombre" :value="__('Nombre')" />
    <x-input id="nombre" name="nombre" type="text" class="block mt-1 w-full"
                  :value="old('nombre', $categoria?->nombre)" required autofocus />
    <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
</div>

<!-- Descripción -->
<div>
    <x-input-label for="descripcion" :value="__('Descripción')" />
    <x-input id="descripcion" name="descripcion" type="text" class="block mt-1 w-full"
                  :value="old('descripcion', $categoria?->descripcion)" />
    <x-input-error :messages="$errors->get('descripcion')" class="mt-2" />
</div>

<!-- Estado -->
<div>
    <label for="activo" class="inline-flex items-center">
        <input id="activo" type="checkbox" name="activo" value="1"
               class="rounded border-bg-300 bg-bg-100 text-primary-100 shadow-sm focus:ring-primary-300"
               @checked(old('activo', $categoria?->activo ?? true))>
        <span class="ms-2 text-sm text-text-200">{{ __('Categoría activa') }}</span>
    </label>
    <p class="mt-1 text-xs text-text-200">
        {{ __('Las categorías inactivas no estarán disponibles para nuevos productos.') }}
    </p>
</div>