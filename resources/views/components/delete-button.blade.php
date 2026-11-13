@props([
    'action',
    'message' => '¿Está seguro de realizar esta acción?',
    'label' => 'Eliminar',
    'confirmLabel' => 'Eliminar',
])

@php $modalId = 'delete-modal-'.uniqid(); @endphp

<div class="inline">
    <button type="button" onclick="document.getElementById('{{ $modalId }}').style.display='flex'"
            class="text-red-400 hover:text-red-300 font-medium">
        {{ $label }}
    </button>

    <div id="{{ $modalId }}" style="display:none;"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
         onclick="if(event.target===this)this.style.display='none'">
        <div class="bg-bg-200 border border-bg-300 rounded-lg shadow-xl p-6 w-full max-w-xs mx-auto">
            <div class="flex justify-center mb-4">
                <div class="w-12 h-12 rounded-full bg-amber-500/15 flex items-center justify-center">
                    <svg class="w-7 h-7 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4a2 2 0 00-3.46 0l-6.93 12c-.77 1.33.19 3 1.73 3z" />
                    </svg>
                </div>
            </div>
            <p class="text-text-100 text-center mb-4">{{ $message }}</p>
            <div class="flex justify-center gap-3">
                <button type="button" onclick="document.getElementById('{{ $modalId }}').style.display='none'"
                        class="px-4 py-2 bg-bg-300 text-text-100 text-sm font-medium rounded-md hover:bg-bg-300/80">
                    Cancelar
                </button>
                <form method="POST" action="{{ $action }}" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-500">
                        {{ $confirmLabel }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>