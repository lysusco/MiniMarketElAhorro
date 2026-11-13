@props(['label' => null])

<span {{ $attributes->merge(['class' => 'block px-4 py-2 text-sm text-text-200']) }}>
    {{ $slot }} <span class="text-xs">· {{ __('próximamente') }}</span>
</span>
