<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-text-100 leading-tight">
            {{ __('Nueva categoría') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-bg-200 border border-bg-300 overflow-hidden shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('categorias.store') }}" class="p-6 space-y-6">
                    @csrf

                    @include('categorias.form', ['categoria' => null])

                    <div class="flex items-center gap-3">
                        <x-button-primary>{{ __('Guardar') }}</x-button-primary>
                        <a href="{{ route('categorias.index') }}"
                           class="inline-flex items-center px-4 py-2 bg-bg-200 border border-bg-300 text-sm font-medium rounded-md text-text-100 hover:bg-bg-300">
                            {{ __('Cancelar') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>