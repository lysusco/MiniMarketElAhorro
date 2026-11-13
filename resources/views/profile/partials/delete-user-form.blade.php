<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-text-100">
            {{ __('Eliminar cuenta') }}
        </h2>

        <p class="mt-1 text-sm text-text-200">
            {{ __('Una vez que elimines tu cuenta, todos sus recursos y datos se eliminarán de forma permanente. Antes de eliminarla, descarga cualquier dato o información que desees conservar.') }}
        </p>
    </header>

    <x-button-danger
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >{{ __('Eliminar cuenta') }}</x-button-danger>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium text-text-100">
                {{ __('¿Estás seguro de que quieres eliminar tu cuenta?') }}
            </h2>

            <p class="mt-1 text-sm text-text-200">
                {{ __('Una vez que elimines tu cuenta, todos sus recursos y datos se eliminarán de forma permanente. Ingresa tu contraseña para confirmar que deseas eliminar tu cuenta de forma permanente.') }}
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="Contraseña" class="sr-only" />

                <x-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-3/4"
                    placeholder="Contraseña"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-button-secondary x-on:click="$dispatch('close')">
                    {{ __('Cancelar') }}
                </x-button-secondary>

                <x-button-danger class="ms-3">
                    {{ __('Eliminar cuenta') }}
                </x-button-danger>
            </div>
        </form>
    </x-modal>
</section>
