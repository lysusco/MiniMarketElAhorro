<x-guest-layout>
    <div class="w-full max-w-md mx-auto">
        <div class="flex flex-col items-center gap-3 mb-8">
            <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" class="w-16 h-16 rounded-2xl object-cover">
            <h1 class="text-xl font-semibold text-text-100">{{ __('Verificar correo') }}</h1>
            <p class="text-sm text-text-200 text-center">{{ __('Gracias por registrarte. Antes de continuar, verifica tu correo electrónico con el enlace que te enviamos. Si no lo recibiste, podemos reenviártelo.') }}</p>
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="mb-4 bg-green-500/15 border border-green-500/30 text-green-300 rounded-lg p-4 text-sm">
                {{ __('Se ha enviado un nuevo enlace de verificación a tu correo.') }}
            </div>
        @endif

        <div class="bg-bg-200 border border-bg-300 rounded-2xl p-8 space-y-4">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <x-button-primary class="w-full justify-center">
                    {{ __('Reenviar correo de verificación') }}
                </x-button-primary>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-sm text-text-200 hover:text-text-100 underline">
                    {{ __('Cerrar sesión') }}
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>