<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-text-100 leading-tight">
            {{ __('Historial de movimientos') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-bg-200 border border-bg-300 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    <form method="GET" action="{{ route('movimientos.index') }}" class="flex flex-col sm:flex-row flex-wrap gap-2 mb-6">
                        <x-select name="producto_id" class="w-full sm:max-w-xs">
                            <option value="">{{ __('Todos los productos') }}</option>
                            @foreach ($productos as $producto)
                                <option value="{{ $producto->id }}" @selected(request('producto_id') == $producto->id)>
                                    {{ $producto->nombre }}
                                </option>
                            @endforeach
                        </x-select>

                        <x-select name="tipo" class="w-full sm:max-w-xs">
                            <option value="">{{ __('Todos los tipos') }}</option>
                            <option value="entrada" @selected(request('tipo') === 'entrada')>{{ __('Entrada') }}</option>
                            <option value="salida" @selected(request('tipo') === 'salida')>{{ __('Salida') }}</option>
                            <option value="ajuste" @selected(request('tipo') === 'ajuste')>{{ __('Ajuste') }}</option>
                        </x-select>

                        <x-input type="date" name="desde" :value="request('desde')" class="w-full sm:max-w-xs" />
                        <x-input type="date" name="hasta" :value="request('hasta')" class="w-full sm:max-w-xs" />

                        <x-button-primary type="submit">{{ __('Filtrar') }}</x-button-primary>
                        @if (request('producto_id') || request('tipo') || request('desde') || request('hasta'))
                            <a href="{{ route('movimientos.index') }}"
                               class="inline-flex items-center px-4 py-2 bg-bg-200 border border-bg-300 text-sm font-medium rounded-md text-text-100 hover:bg-bg-300">
                                {{ __('Limpiar') }}
                            </a>
                        @endif
                    </form>

                    @if (session('success'))
                        <div class="mb-4">
                            <x-alert type="success">{{ session('success') }}</x-alert>
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-bg-300 text-sm">
                            <thead>
                                <tr class="text-left text-text-200">
                                    <th class="py-2 pe-4 font-medium">{{ __('Fecha') }}</th>
                                    <th class="py-2 pe-4 font-medium">{{ __('Producto') }}</th>
                                    <th class="py-2 pe-4 font-medium">{{ __('Tipo') }}</th>
                                    <th class="py-2 pe-4 font-medium text-end">{{ __('Cantidad') }}</th>
                                    <th class="py-2 pe-4 font-medium">{{ __('Usuario') }}</th>
                                    <th class="py-2 pe-4 font-medium">{{ __('Origen') }}</th>
                                    <th class="py-2 pe-4 font-medium">{{ __('Observación') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-bg-300">
                                @forelse ($movimientos as $movimiento)
                                    <tr class="hover:bg-bg-300/40">
                                        <td class="py-3 pe-4 text-text-200">{{ $movimiento->fecha->format('Y-m-d') }}</td>
                                        <td class="py-3 pe-4 text-text-100">{{ $movimiento->producto?->nombre ?? '—' }}</td>
                                        <td class="py-3 pe-4">
                                            @php
                                                $clase = match ($movimiento->tipo) {
                                                    'entrada' => 'bg-green-900/40 text-green-300',
                                                    'salida' => 'bg-blue-900/40 text-blue-300',
                                                    default => 'bg-amber-900/40 text-amber-300',
                                                };
                                            @endphp
                                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $clase }}">
                                                {{ __(ucfirst($movimiento->tipo)) }}
                                            </span>
                                        </td>
                                        <td class="py-3 pe-4 text-text-200 text-end">{{ $movimiento->cantidad }}</td>
                                        <td class="py-3 pe-4 text-text-200">{{ $movimiento->user?->name ?? '—' }}</td>
                                        <td class="py-3 pe-4 text-text-200">
                                            @if ($movimiento->compra_id)
                                                {{ __('Compra #') }}{{ $movimiento->compra_id }}
                                            @elseif ($movimiento->venta_id)
                                                {{ __('Venta #') }}{{ $movimiento->venta_id }}
                                            @else
                                                {{ __('Ajuste') }}
                                            @endif
                                        </td>
                                        <td class="py-3 pe-4 text-text-200">{{ $movimiento->observacion ?? '—' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-6 text-center text-text-200">
                                            {{ __('No se encontraron movimientos.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $movimientos->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>