<x-guest-layout>
    <div class="w-full max-w-md mx-auto">
        <!-- Marca -->
        <div class="flex flex-col items-center gap-3 mb-8">
            <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" class="w-16 h-16 rounded-2xl object-cover">
            <h1 class="text-xl font-semibold text-text-100">{{ config('app.name', 'Minimercado El Ahorro') }}</h1>
            <p class="text-sm text-text-200">Inicia sesión para continuar</p>
        </div>

        <!-- Estado de la sesión -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="bg-bg-200 border border-bg-300 rounded-2xl p-8 space-y-5">
            @csrf

            <!-- Correo o identificación -->
            <div>
                <x-input-label for="login" :value="__('Correo electrónico o identificación')" />
                <x-input id="login" class="block mt-1 w-full" type="text" name="login" :value="old('login')" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('login')" class="mt-2" />
            </div>

            <!-- Contraseña -->
            <div>
                <x-input-label for="password" :value="__('Contraseña')" />
                <x-input id="password" class="block mt-1 w-full"
                                type="password"
                                name="password"
                                required autocomplete="current-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Recordarme + olvidé contraseña -->
            <div class="flex items-center justify-between text-sm">
                <label for="remember_me" class="inline-flex items-center gap-2 text-text-200">
                    <input id="remember_me" type="checkbox" class="rounded border-bg-300 bg-bg-100 text-primary-100 shadow-sm focus:ring-primary-300" name="remember">
                    {{ __('Recordarme') }}
                </label>

                @if (Route::has('password.request'))
                    <a class="text-text-200 hover:text-text-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-300" href="{{ route('password.request') }}">
                        {{ __('¿Olvidaste tu contraseña?') }}
                    </a>
                @endif
            </div>

            <x-button-primary class="w-full justify-center">
                {{ __('Iniciar sesión') }}
            </x-button-primary>
        </form>

        <p class="text-center text-xs text-text-200 mt-6">
            {{ __('¿Problemas para ingresar? Contacta al administrador.') }}
        </p>
    </div>
</x-guest-layout>