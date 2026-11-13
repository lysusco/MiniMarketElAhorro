@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-text-200']) }}>
    {{ $value ?? $slot }}
</label>
