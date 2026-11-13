@props(['variant' => null, 'color' => null, 'type' => null])

@php
$variant = $variant ?? $color ?? $type ?? 'neutral';
$variantClasses = match ($variant) {
    'success', 'green' => 'bg-green-500/15 text-green-400 border border-green-500/30',
    'warning', 'amber' => 'bg-amber-500/15 text-amber-400 border border-amber-500/30',
    'danger', 'red', 'error' => 'bg-red-500/15 text-red-400 border border-red-500/30',
    'info', 'blue' => 'bg-blue-500/15 text-blue-400 border border-blue-500/30',
    default => 'bg-bg-300 text-text-200 border border-bg-300',
};
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' . $variantClasses]) }}>
    {{ $slot }}
</span>