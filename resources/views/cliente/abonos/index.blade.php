<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-text-100 leading-tight">
            {{ __('Mis abonos') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-bg-200 border border-bg-300 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 overflow-x-auto">
                    <table class="min-w-full divide-y divide-bg-300 text-sm">
                        <thead>
                            <tr class="text-left text-text-200">
                                <th class="py-2 pe-4 font-medium">{{ __('Fecha') }}</th>
                                <th class="py-2 pe-4 font-medium">{{ __('Venta') }}</th>
                                <th class="py-2 pe-4 font-medium">{{ __('Método') }}</th>
                                <th class="py-2 pe-4 font-medium text-end">{{ __('Monto') }}</th>
                                <th class="py-2 pe-4 font-medium">{{ __('Observación') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-bg-300">
                            @forelse ($abonos as $abono)
                                <tr class="hover:bg-bg-300/40">
                                    <td class="py-2 pe-4 text-text-200">{{ $abono->fecha->format('d/m/Y') }}</td>
                                    <td class="py-2 pe-4 text-text-100">
                                        <a href="{{ route('cliente.ventas.show', $abono->venta) }}" class="text-accent-200 hover:text-text-100">#{{ $abono->venta_id }}</a>
                                    </td>
                                    <td class="py-2 pe-4 text-text-200">{{ $abono->metodo_pago }}</td>
                                    <td class="py-2 pe-4 text-end text-text-100">${{ number_format($abono->monto, 0, ',', '.') }}</td>
                                    <td class="py-2 pe-4 text-text-200">{{ $abono->observacion }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-4 text-center text-text-200">{{ __('Aún no tienes abonos registrados.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $abonos->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>