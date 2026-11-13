@props(['type' => null, 'variant' => null])

@php
$type = $variant ?? $type ?? 'info';
$alertClasses = match ($type) {
    'success', 'green' => 'bg-green-500/15 text-green-300 border border-green-500/30',
    'error', 'danger', 'red' => 'bg-red-500/15 text-red-300 border border-red-500/30',
    'warning', 'amber' => 'bg-amber-500/15 text-amber-300 border border-amber-500/30',
    default => 'bg-blue-500/15 text-blue-300 border border-blue-500/30',
};
@endphp

<div {{ $attributes->merge(['class' => 'rounded-md p-4 text-sm ' . $alertClasses]) }} role="alert">
    {{ $slot }}
</div>