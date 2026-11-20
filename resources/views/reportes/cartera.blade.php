<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h2 class="font-semibold text-xl text-text-100 leading-tight">
                {{ __('Reporte de cartera') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-bg-200 border border-bg-300 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    <p class="text-sm text-text-200 mb-6">
                        {{ __('Clientes con saldo pendiente, ordenados de mayor a menor saldo.') }}
                    </p>

                    <!-- Cartera de clientes -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-bg-300 text-sm">
                            <thead>
                                <tr class="text-left text-text-200">
                                    <th class="py-2 pe-4 font-medium">{{ __('Nombre') }}</th>
                                    <th class="py-2 pe-4 font-medium">{{ __('Identificación') }}</th>
                                    <th class="py-2 pe-4 font-medium">{{ __('Teléfono') }}</th>
                                    <th class="py-2 pe-4 font-medium text-end">{{ __('Límite de crédito') }}</th>
                                    <th class="py-2 pe-4 font-medium text-end">{{ __('Saldo') }}</th>
                                    <th class="py-2 pe-4 font-medium text-end">{{ __('Crédito disponible') }}</th>
                                    <th class="py-2 pe-4 font-medium text-end">{{ __('Ventas fiadas pendientes') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-bg-300">
                                @forelse ($clientes as $cliente)
                                    <tr class="hover:bg-bg-300/40">
                                        <td class="py-3 pe-4 font-medium text-text-100">{{ $cliente->nombre }}</td>
                                        <td class="py-3 pe-4 text-text-200">{{ $cliente->identificacion }}</td>
                                        <td class="py-3 pe-4 text-text-200">{{ $cliente->telefono ?? '—' }}</td>
                                        <td class="py-3 pe-4 text-text-200 text-end">{{ number_format($cliente->limite_credito, 2) }}</td>
                                        <td class="py-3 pe-4 text-text-100 text-end">{{ number_format($cliente->saldo, 2) }}</td>
                                        <td class="py-3 pe-4 text-text-200 text-end">{{ number_format($cliente->creditoDisponible(), 2) }}</td>
                                        <td class="py-3 pe-4 text-text-200 text-end">{{ $cliente->ventas_con_saldo_pendiente }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-6 text-center text-text-200">
                                            {{ __('No hay clientes con saldo pendiente.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
