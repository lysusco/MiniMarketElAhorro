{{-- Formulario compartido para crear y editar categorías --}}

<!-- Nombre -->
<div>
    <x-input-label for="nombre" :value="__('Nombre')" />
    <x-text-input id="nombre" name="nombre" type="text" class="block mt-1 w-full"
                  :value="old('nombre', $categoria?->nombre)" required autofocus />
    <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
</div>

<!-- Descripción -->
<div>
    <x-input-label for="descripcion" :value="__('Descripción')" />
    <x-text-input id="descripcion" name="descripcion" type="text" class="block mt-1 w-full"
                  :value="old('descripcion', $categoria?->descripcion)" />
    <x-input-error :messages="$errors->get('descripcion')" class="mt-2" />
</div>

<!-- Estado -->
<div>
    <label for="activo" class="inline-flex items-center">
        <input id="activo" type="checkbox" name="activo" value="1"
               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
               @checked(old('activo', $categoria?->activo ?? true))>
        <span class="ms-2 text-sm text-gray-600">{{ __('Categoría activa') }}</span>
    </label>
    <p class="mt-1 text-xs text-gray-400">
        {{ __('Las categorías inactivas no estarán disponibles para nuevos productos.') }}
    </p>
</div>
