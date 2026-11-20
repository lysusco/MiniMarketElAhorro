<x-guest-layout>
    <div class="w-full max-w-6xl mx-auto overflow-hidden rounded-3xl bg-bg-200 shadow-xl ring-1 ring-bg-300">
        <!-- Cabecera -->
        <div class="flex items-center justify-between px-8 py-5">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" class="w-9 h-9 rounded-lg object-cover">
                <span class="text-lg font-bold text-text-100">Minimercado El Ahorro</span>
                <span class="hidden sm:inline text-sm text-text-200 ms-2">Soporte · +57 300 000 0000</span>
            </div>
            <nav class="hidden md:flex items-center gap-6 text-sm text-text-200">
                <a href="#" class="hover:text-text-100">Inicio</a>
                <a href="#" class="hover:text-text-100">Soporte</a>
                <a href="#" class="hover:text-text-100">Ayuda</a>
            </nav>
        </div>

        <!-- Cuerpo -->
        <div class="grid grid-cols-1 md:grid-cols-2">
            <!-- Formulario (izquierda) -->
            <div class="px-8 sm:px-12 py-10">
                <h1 class="text-3xl sm:text-4xl font-bold text-text-100 leading-tight">
                    ¡Inicia sesión en<br>tu cuenta!
                </h1>

                <x-auth-session-status class="mt-6" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-6">
                    @csrf

                    <!-- Correo o identificación -->
                    <div>
                        <label for="login" class="block text-sm text-text-200">Correo electrónico o identificación</label>
                        <input id="login" type="text" name="login" :value="old('login')" required autofocus autocomplete="username"
                               class="mt-1 w-full bg-transparent border-0 border-b-2 border-bg-300 pb-2 text-lg font-semibold text-text-100 placeholder:text-text-200/50 focus:border-accent-200 focus:outline-none focus:ring-0" placeholder="admin@minimercado.com">
                        <x-input-error :messages="$errors->get('login')" class="mt-2" />
                    </div>

                    <!-- Contraseña -->
                    <div x-data="{ show: false }" class="relative">
                        <label for="password" class="block text-sm text-text-200">Contraseña</label>
                        <input id="password" :type="show ? 'text' : 'password'" name="password" required autocomplete="current-password"
                               class="mt-1 w-full bg-transparent border-0 border-b-2 border-bg-300 pb-2 pr-10 text-lg font-semibold text-text-100 placeholder:text-text-200/50 focus:border-accent-200 focus:outline-none focus:ring-0" placeholder="••••••••">
                        <button type="button" @click="show = !show" class="absolute right-0 bottom-2 text-text-200 hover:text-text-100 focus:outline-none" tabindex="-1">
                            <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg x-show="show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </button>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Recordarme + olvidé contraseña -->
                    <div class="flex items-center justify-between text-sm">
                        <label for="remember_me" class="inline-flex items-center gap-2 text-text-200 cursor-pointer">
                            <input id="remember_me" type="checkbox" class="rounded-full border-bg-300 bg-bg-100 text-accent-200 focus:ring-accent-200" name="remember">
                            {{ __('Recordarme') }}
                        </label>

                        @if (Route::has('password.request'))
                            <a class="text-text-200 hover:text-text-100 font-medium" href="{{ route('password.request') }}">
                                {{ __('¿Olvidaste tu contraseña?') }}
                            </a>
                        @endif
                    </div>

                    <!-- Botón CTA -->
                    <button type="submit"
                            class="inline-flex items-center justify-center px-8 py-3 rounded-lg bg-accent-200 text-white font-semibold text-sm uppercase tracking-widest hover:bg-accent-200/90 focus:outline-none focus:ring-2 focus:ring-accent-200 focus:ring-offset-2 focus:ring-offset-bg-200 transition ease-in-out duration-150">
                        {{ __('Iniciar sesión') }}
                    </button>

                    <p class="text-sm text-text-200">
                        {{ __('¿Problemas para ingresar?') }} <a href="#" class="font-semibold text-text-100 hover:text-accent-200">{{ __('Contacta al administrador') }}</a>
                    </p>
                </form>
            </div>

            <!-- Panel visual (derecha) -->
            <div class="relative hidden md:flex flex-col items-center justify-center gap-8 bg-primary-100 p-12 overflow-hidden">
                <!-- Formas decorativas -->
                <div class="absolute -top-10 -right-10 w-56 h-56 rounded-full bg-accent-100/40"></div>
                <div class="absolute bottom-0 left-0 w-40 h-40 rounded-full bg-primary-200/50 translate-y-10 -translate-x-10"></div>

                <!-- Marca -->
                <div class="relative text-center">
                    <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" class="w-32 h-32 rounded-3xl object-cover shadow-lg mx-auto">
                    <h2 class="mt-6 text-3xl font-bold text-text-100">Minimercado El Ahorro</h2>
                    <p class="mt-2 text-text-200">Gestión de inventario, ventas y crédito en un solo lugar.</p>
                </div>

                <!-- Tarjetas flotantes -->
                <div class="relative w-full max-w-xs space-y-4">
                    <div class="bg-bg-100 rounded-2xl shadow-lg p-4 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-accent-100/60 flex items-center justify-center">
                            <svg class="w-5 h-5 text-accent-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-text-100">Control de inventario</p>
                            <p class="text-xs text-text-200">Stock, categorías y movimientos</p>
                        </div>
                    </div>

                    <div class="bg-bg-100 rounded-2xl shadow-lg p-4 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-primary-200/60 flex items-center justify-center">
                            <svg class="w-5 h-5 text-accent-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 9v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-text-100">Ventas y crédito</p>
                            <p class="text-xs text-text-200">Fiado, abonos y cartera al día</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>