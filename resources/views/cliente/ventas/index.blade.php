<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-text-100 leading-tight">
            {{ __('Mis ventas') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-bg-200 border border-bg-300 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 overflow-x-auto">
                    <table class="min-w-full divide-y divide-bg-300 text-sm">
                        <thead>
                            <tr class="text-left text-text-200">
                                <th class="py-2 pe-4 font-medium">{{ __('N.º') }}</th>
                                <th class="py-2 pe-4 font-medium">{{ __('Fecha') }}</th>
                                <th class="py-2 pe-4 font-medium">{{ __('Tipo') }}</th>
                                <th class="py-2 pe-4 font-medium">{{ __('Estado') }}</th>
                                <th class="py-2 pe-4 font-medium text-end">{{ __('Total') }}</th>
                                <th class="py-2 pe-4 font-medium text-end">{{ __('Saldo pendiente') }}</th>
                                <th class="py-2 pe-4 font-medium"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-bg-300">
                            @forelse ($ventas as $venta)
                                <tr class="hover:bg-bg-300/40">
                                    <td class="py-2 pe-4 text-text-100">#{{ $venta->id }}</td>
                                    <td class="py-2 pe-4 text-text-200">{{ $venta->fecha->format('d/m/Y') }}</td>
                                    <td class="py-2 pe-4">
                                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $venta->tipo_venta === 'fiado' ? 'bg-amber-500/15 text-amber-400' : 'bg-green-500/15 text-green-400' }}">
                                            {{ $venta->tipo_venta === 'fiado' ? __('Fiado') : __('Contado') }}
                                        </span>
                                    </td>
                                    <td class="py-2 pe-4 text-text-200">{{ $venta->estado }}</td>
                                    <td class="py-2 pe-4 text-text-100 text-end">${{ number_format($venta->total, 0, ',', '.') }}</td>
                                    <td class="py-2 pe-4 text-end {{ $venta->saldoPendiente() > 0 ? 'text-amber-400' : 'text-text-200' }}">
                                        ${{ number_format($venta->saldoPendiente(), 0, ',', '.') }}
                                    </td>
                                    <td class="py-2 pe-4 text-end">
                                        <a href="{{ route('cliente.ventas.show', $venta) }}" class="text-accent-200 hover:text-text-100">{{ __('Ver') }}</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-4 text-center text-text-200">{{ __('Aún no tienes ventas registradas.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $ventas->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>