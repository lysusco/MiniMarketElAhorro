<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-text-100 leading-tight">
            {{ __('Catálogo de productos') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <form method="GET" action="{{ route('cliente.catalogo') }}" class="bg-bg-200 border border-bg-300 overflow-hidden shadow-sm sm:rounded-lg p-4 flex flex-col sm:flex-row gap-3">
                <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="{{ __('Buscar por nombre o código') }}"
                    class="flex-1 rounded-md bg-bg-100 border-bg-300 text-text-100 shadow-sm focus:border-accent-200 focus:ring-accent-200">
                <select name="categoria" class="rounded-md bg-bg-100 border-bg-300 text-text-100 shadow-sm focus:border-accent-200 focus:ring-accent-200">
                    <option value="">{{ __('Todas las categorías') }}</option>
                    @foreach ($categorias as $categoria)
                        <option value="{{ $categoria->nombre }}" @selected(request('categoria') === $categoria->nombre)>{{ $categoria->nombre }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-4 py-2 bg-primary-100 text-text-100 rounded-md hover:bg-primary-200">{{ __('Buscar') }}</button>
            </form>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse ($productos as $producto)
                    <div class="bg-bg-200 border border-bg-300 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-5">
                            <div class="text-xs text-text-200">{{ $producto->categoria->nombre ?? __('Sin categoría') }}</div>
                            <div class="mt-1 text-lg font-semibold text-text-100">{{ $producto->nombre }}</div>
                            <div class="mt-1 text-sm text-text-200">{{ __('Código:') }} {{ $producto->codigo }}</div>
                            <div class="mt-3 text-xl font-bold text-accent-200">${{ number_format($producto->precio_venta, 0, ',', '.') }}</div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-bg-200 border border-bg-300 overflow-hidden shadow-sm sm:rounded-lg p-6 text-center text-text-200">
                        {{ __('No se encontraron productos.') }}
                    </div>
                @endforelse
            </div>

            <div>
                {{ $productos->links() }}
            </div>
        </div>
    </div>
</x-app-layout>