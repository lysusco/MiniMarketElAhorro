<x-guest-layout>
    <div class="w-full max-w-md mx-auto">
        <div class="flex flex-col items-center gap-3 mb-8">
            <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" class="w-16 h-16 rounded-2xl object-cover">
            <h1 class="text-xl font-semibold text-text-100">{{ __('Restablecer contraseña') }}</h1>
            <p class="text-sm text-text-200 text-center">{{ __('Define tu nueva contraseña para la cuenta.') }}</p>
        </div>

        <form method="POST" action="{{ route('password.store') }}" class="bg-bg-200 border border-bg-300 rounded-2xl p-8 space-y-5">
            @csrf

            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div>
                <x-input-label for="email" :value="__('Correo electrónico')" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password" :value="__('Nueva contraseña')" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password_confirmation" :value="__('Confirmar contraseña')" />
                <x-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <x-button-primary class="w-full justify-center">
                {{ __('Restablecer contraseña') }}
            </x-button-primary>
        </form>
    </div>
</x-guest-layout>