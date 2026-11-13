@props(['title' => null])

<div {{ $attributes->merge(['class' => 'bg-bg-200 border border-bg-300 rounded-lg overflow-hidden']) }}>
    @if (isset($header) || $title)
        <div class="px-4 py-3 border-b border-bg-300">
            @isset($header)
                {{ $header }}
            @else
                <span class="font-semibold text-text-100">{{ $title }}</span>
            @endisset
        </div>
    @endif
    <div class="p-4">
        {{ $slot }}
    </div>
</div>