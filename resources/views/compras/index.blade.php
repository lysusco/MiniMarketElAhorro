<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h2 class="font-semibold text-xl text-text-100 leading-tight">
                {{ __('Entradas de mercancía') }}
            </h2>

            <a href="{{ route('compras.create') }}"
               class="inline-flex items-center px-4 py-2 bg-primary-100 text-text-100 text-sm font-medium rounded-md hover:bg-primary-200">
                {{ __('Nueva entrada') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-bg-200 border border-bg-300 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    @if (session('success'))
                        <div class="mb-4">
                            <x-alert type="success">{{ session('success') }}</x-alert>
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-bg-300 text-sm">
                            <thead>
                                <tr class="text-left text-text-200">
                                    <th class="py-2 pe-4 font-medium">{{ __('ID') }}</th>
                                    <th class="py-2 pe-4 font-medium">{{ __('Fecha') }}</th>
                                    <th class="py-2 pe-4 font-medium">{{ __('Proveedor') }}</th>
                                    <th class="py-2 pe-4 font-medium">{{ __('Registrado por') }}</th>
                                    <th class="py-2 pe-4 font-medium">{{ __('Estado') }}</th>
                                    <th class="py-2 pe-4 font-medium text-end">{{ __('Total') }}</th>
                                    <th class="py-2 pe-4 font-medium text-end">{{ __('Acciones') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-bg-300">
                                @forelse ($compras as $compra)
                                    <tr class="hover:bg-bg-300/40">
                                        <td class="py-3 pe-4 font-medium text-text-100">
                                            <a href="{{ route('compras.show', $compra) }}" class="text-accent-200 hover:text-text-100">
                                                #{{ $compra->id }}
                                            </a>
                                        </td>
                                        <td class="py-3 pe-4 text-text-200">{{ $compra->fecha->format('Y-m-d') }}</td>
                                        <td class="py-3 pe-4 text-text-200">{{ $compra->proveedor?->nombre ?? '—' }}</td>
                                        <td class="py-3 pe-4 text-text-200">{{ $compra->user?->name ?? '—' }}</td>
                                        <td class="py-3 pe-4">
                                            @php
                                                $estadoClases = match ($compra->estado) {
                                                    \App\Models\Compra::ESTADO_ACTIVO => 'bg-green-900/40 text-green-300',
                                                    \App\Models\Compra::ESTADO_INACTIVO => 'bg-bg-300 text-text-200',
                                                    default => 'bg-amber-900/40 text-amber-300',
                                                };
                                                $estadoLabel = match ($compra->estado) {
                                                    \App\Models\Compra::ESTADO_ACTIVO => 'Activa',
                                                    \App\Models\Compra::ESTADO_INACTIVO => 'Inactiva',
                                                    default => 'En progreso',
                                                };
                                            @endphp
                                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $estadoClases }}">
                                                {{ __($estadoLabel) }}
                                            </span>
                                        </td>
                                        <td class="py-3 pe-4 text-text-200 text-end">{{ number_format($compra->total, 2) }}</td>
                                        <td class="py-3 pe-4 text-end whitespace-nowrap">
                                            @if ($compra->estado === \App\Models\Compra::ESTADO_EN_PROGRESO)
                                                <a href="{{ route('compras.edit', $compra) }}"
                                                   class="ms-3 text-accent-200 hover:text-text-100 font-medium">
                                                    {{ __('Modificar') }}
                                                </a>

                                                <form method="POST" action="{{ route('compras.inactivar', $compra) }}"
                                                      class="inline ms-3">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="text-amber-300 hover:text-amber-200 font-medium">
                                                        {{ __('Inactivar') }}
                                                    </button>
                                                </form>
                                            @elseif ($compra->estado === \App\Models\Compra::ESTADO_ACTIVO && auth()->user()->isAdmin())
                                                @php $modalId = 'inactivar-modal-'.$compra->id; @endphp
                                                <button type="button" onclick="document.getElementById('{{ $modalId }}').style.display='flex'"
                                                        class="ms-3 text-amber-300 hover:text-amber-200 font-medium">
                                                    {{ __('Inactivar') }}
                                                </button>

                                                <div id="{{ $modalId }}" style="display:none;"
                                                     class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
                                                     onclick="if(event.target===this)this.style.display='none'">
                                                    <div class="bg-bg-200 border border-bg-300 rounded-lg shadow-xl p-6 w-full max-w-sm mx-auto">
                                                        <div class="flex justify-center mb-4">
                                                            <div class="w-12 h-12 rounded-full bg-amber-500/15 flex items-center justify-center">
                                                                <svg class="w-7 h-7 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                          d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4a2 2 0 00-3.46 0l-6.93 12c-.77 1.33.19 3 1.73 3z" />
                                                                </svg>
                                                            </div>
                                                        </div>
                                                        <h3 class="text-text-100 font-semibold text-center mb-3">
                                                            {{ __('Inactivar entrada') }}
                                                        </h3>
                                                        <form method="POST" action="{{ route('compras.inactivar', $compra) }}">
                                                            @csrf
                                                            @method('PATCH')
                                                            <textarea name="motivo" rows="3" required
                                                                      placeholder="{{ __('Razón de inactivación...') }}"
                                                                      class="bg-bg-100 border-bg-300 text-text-100 focus:border-accent-200 focus:ring-accent-200 rounded-md shadow-sm w-full mb-4"></textarea>
                                                            <div class="flex justify-center gap-3">
                                                                <button type="button" onclick="document.getElementById('{{ $modalId }}').style.display='none'"
                                                                        class="px-4 py-2 bg-bg-300 text-text-100 text-sm font-medium rounded-md hover:bg-bg-300/80">
                                                                    {{ __('Cancelar') }}
                                                                </button>
                                                                <button type="submit"
                                                                        class="px-4 py-2 bg-amber-600 text-white text-sm font-medium rounded-md hover:bg-amber-500">
                                                                    {{ __('Inactivar') }}
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-6 text-center text-text-200">
                                            {{ __('No se encontraron entradas.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $compras->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>