{{-- Formulario compartido para crear y editar clientes --}}

<!-- Nombre -->
<div>
    <x-input-label for="nombre" :value="__('Nombre')" />
    <x-input id="nombre" name="nombre" type="text" class="block mt-1 w-full"
                  :value="old('nombre', $cliente?->nombre)" required autofocus />
    <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
</div>

<!-- Identificación -->
<div>
    <x-input-label for="identificacion" :value="__('Identificación')" />
    <x-input id="identificacion" name="identificacion" type="text" class="block mt-1 w-full"
                  :value="old('identificacion', $cliente?->identificacion)" required />
    <x-input-error :messages="$errors->get('identificacion')" class="mt-2" />
</div>

<!-- Teléfono -->
<div>
    <x-input-label for="telefono" :value="__('Teléfono')" />
    <x-input id="telefono" name="telefono" type="text" class="block mt-1 w-full"
                  :value="old('telefono', $cliente?->telefono)" />
    <x-input-error :messages="$errors->get('telefono')" class="mt-2" />
</div>

<!-- Correo -->
<div>
    <x-input-label for="correo" :value="__('Correo')" />
    <x-input id="correo" name="correo" type="email" class="block mt-1 w-full"
                  :value="old('correo', $cliente?->correo)" />
    <x-input-error :messages="$errors->get('correo')" class="mt-2" />
</div>

<!-- Dirección -->
<div>
    <x-input-label for="direccion" :value="__('Dirección')" />
    <x-input id="direccion" name="direccion" type="text" class="block mt-1 w-full"
                  :value="old('direccion', $cliente?->direccion)" />
    <x-input-error :messages="$errors->get('direccion')" class="mt-2" />
</div>

{{-- El límite de crédito solo lo define el administrador (RF-13). --}}
@if (auth()->user()->isAdmin())
    <!-- Límite de crédito -->
    <div>
        <x-input-label for="limite_credito" :value="__('Límite de crédito')" />
        <x-input id="limite_credito" name="limite_credito" type="number" step="0.01" min="0"
                      class="block mt-1 w-full"
                      :value="old('limite_credito', $cliente?->limite_credito)" />
        <p class="mt-1 text-xs text-text-200">
            {{ __('Cupo máximo de crédito (fiado) que puede usar el cliente.') }}
        </p>
        <x-input-error :messages="$errors->get('limite_credito')" class="mt-2" />
    </div>
@endif
