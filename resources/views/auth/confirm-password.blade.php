<x-guest-layout>
    <div class="w-full max-w-md mx-auto">
        <div class="flex flex-col items-center gap-3 mb-8">
            <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" class="w-16 h-16 rounded-2xl object-cover">
            <h1 class="text-xl font-semibold text-text-100">{{ __('Confirmar contraseña') }}</h1>
            <p class="text-sm text-text-200 text-center">{{ __('Esta es un área segura. Confirma tu contraseña para continuar.') }}</p>
        </div>

        <form method="POST" action="{{ route('password.confirm') }}" class="bg-bg-200 border border-bg-300 rounded-2xl p-8 space-y-5">
            @csrf

            <div>
                <x-input-label for="password" :value="__('Contraseña')" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <x-button-primary class="w-full justify-center">
                {{ __('Confirmar') }}
            </x-button-primary>
        </form>
    </div>
</x-guest-layout>