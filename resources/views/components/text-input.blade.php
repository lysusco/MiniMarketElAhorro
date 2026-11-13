@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'bg-bg-100 border-bg-300 text-text-100 placeholder-text-200 focus:border-accent-200 focus:ring-accent-200 rounded-md shadow-sm']) }}>
