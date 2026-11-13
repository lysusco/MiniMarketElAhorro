<x-guest-layout>
    <div class="w-full max-w-md mx-auto">
        <div class="flex flex-col items-center gap-3 mb-8">
            <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" class="w-16 h-16 rounded-2xl object-cover">
            <h1 class="text-xl font-semibold text-text-100">{{ __('Recuperar contraseña') }}</h1>
            <p class="text-sm text-text-200 text-center">{{ __('Ingresa tu correo y te enviaremos un enlace para restablecer tu contraseña.') }}</p>
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="bg-bg-200 border border-bg-300 rounded-2xl p-8 space-y-5">
            @csrf

            <div>
                <x-input-label for="email" :value="__('Correo electrónico')" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <x-button-primary class="w-full justify-center">
                {{ __('Enviar enlace de restablecimiento') }}
            </x-button-primary>

            <p class="text-center text-xs text-text-200">
                <a href="{{ route('login') }}" class="text-primary-300 hover:text-text-100">{{ __('Volver a iniciar sesión') }}</a>
            </p>
        </form>
    </div>
</x-guest-layout>