<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-text-100 leading-tight">
            {{ __('Ajuste de inventario #') }}{{ $ajuste->id }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-bg-200 border border-bg-300 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-text-200">{{ __('Motivo:') }}</span>
                            <span class="text-text-100 font-medium">{{ $ajuste->motivo }}</span>
                        </div>
                        <div>
                            <span class="text-text-200">{{ __('Fecha:') }}</span>
                            <span class="text-text-100 font-medium">{{ $ajuste->fecha->format('Y-m-d') }}</span>
                        </div>
                        <div>
                            <span class="text-text-200">{{ __('Registrado por:') }}</span>
                            <span class="text-text-100 font-medium">{{ $ajuste->user?->name ?? '—' }}</span>
                        </div>
                        <div>
                            <span class="text-text-200">{{ __('Estado:') }}</span>
                            <span class="text-text-100 font-medium">{{ $ajuste->estado }}</span>
                        </div>
                        <div class="sm:col-span-2">
                            <span class="text-text-200">{{ __('Observación:') }}</span>
                            <span class="text-text-100 font-medium">{{ $ajuste->observacion ?? '—' }}</span>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-bg-300 text-sm">
                            <thead>
                                <tr class="text-left text-text-200">
                                    <th class="py-2 pe-4 font-medium">{{ __('Producto') }}</th>
                                    <th class="py-2 pe-4 font-medium text-end">{{ __('Cantidad') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-bg-300">
                                @foreach ($ajuste->detalles as $detalle)
                                    <tr>
                                        <td class="py-3 pe-4 text-text-100">{{ $detalle->producto?->nombre ?? '—' }}</td>
                                        <td class="py-3 pe-4 text-text-200 text-end">{{ $detalle->cantidad }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        @if ($ajuste->estado === \App\Models\Ajuste::ESTADO_EN_PROGRESO)
                            <a href="{{ route('ajustes.edit', $ajuste) }}"
                               class="inline-flex items-center px-4 py-2 bg-primary-100 text-text-100 text-sm font-medium rounded-md hover:bg-primary-200">
                                {{ __('Modificar') }}
                            </a>

                            <form method="POST" action="{{ route('ajustes.finalizar', $ajuste) }}" class="inline">
                                @csrf
                                <button type="submit"
                                        class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-500">
                                    {{ __('Finalizar') }}
                                </button>
                            </form>

                            <button type="button" onclick="document.getElementById('inactivar-ajuste-{{ $ajuste->id }}').style.display='flex'"
                                    class="px-4 py-2 bg-bg-300 text-text-100 text-sm font-medium rounded-md hover:bg-bg-300/80">
                                {{ __('Inactivar') }}
                            </button>

                            <div id="inactivar-ajuste-{{ $ajuste->id }}" style="display:none;"
                                 class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
                                 onclick="if(event.target===this)this.style.display='none'">
                                <div class="bg-bg-200 border border-bg-300 rounded-lg shadow-xl p-6 w-full max-w-sm mx-auto">
                                    <h3 class="text-text-100 font-semibold text-center mb-3">
                                        {{ __('Inactivar ajuste') }}
                                    </h3>
                                    <form method="POST" action="{{ route('ajustes.inactivar', $ajuste) }}">
                                        @csrf
                                        @method('PATCH')
                                        <textarea name="motivo" rows="3" required
                                                  placeholder="{{ __('Razón de inactivación...') }}"
                                                  class="bg-bg-100 border-bg-300 text-text-100 focus:border-accent-200 focus:ring-accent-200 rounded-md shadow-sm w-full mb-4"></textarea>
                                        <div class="flex justify-center gap-3">
                                            <button type="button" onclick="document.getElementById('inactivar-ajuste-{{ $ajuste->id }}').style.display='none'"
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
                        @elseif ($ajuste->estado === \App\Models\Ajuste::ESTADO_ACTIVO && auth()->user()->isAdmin())
                            <button type="button" onclick="document.getElementById('inactivar-ajuste-{{ $ajuste->id }}').style.display='flex'"
                                    class="px-4 py-2 bg-bg-300 text-text-100 text-sm font-medium rounded-md hover:bg-bg-300/80">
                                {{ __('Inactivar') }}
                            </button>

                            <div id="inactivar-ajuste-{{ $ajuste->id }}" style="display:none;"
                                 class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
                                 onclick="if(event.target===this)this.style.display='none'">
                                <div class="bg-bg-200 border border-bg-300 rounded-lg shadow-xl p-6 w-full max-w-sm mx-auto">
                                    <h3 class="text-text-100 font-semibold text-center mb-3">
                                        {{ __('Inactivar ajuste') }}
                                    </h3>
                                    <form method="POST" action="{{ route('ajustes.inactivar', $ajuste) }}">
                                        @csrf
                                        @method('PATCH')
                                        <textarea name="motivo" rows="3" required
                                                  placeholder="{{ __('Razón de inactivación...') }}"
                                                  class="bg-bg-100 border-bg-300 text-text-100 focus:border-accent-200 focus:ring-accent-200 rounded-md shadow-sm w-full mb-4"></textarea>
                                        <div class="flex justify-center gap-3">
                                            <button type="button" onclick="document.getElementById('inactivar-ajuste-{{ $ajuste->id }}').style.display='none'"
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

                        <a href="{{ route('ajustes.index') }}"
                           class="inline-flex items-center px-4 py-2 bg-bg-200 border border-bg-300 text-sm font-medium rounded-md text-text-100 hover:bg-bg-300">
                            {{ __('Volver') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>