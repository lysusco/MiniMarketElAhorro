<div {{ $attributes->merge(['class' => 'overflow-x-auto rounded-lg border border-bg-300 bg-bg-200']) }}>
    <table class="min-w-full divide-y divide-bg-300 text-text-100">
        {{ $slot }}
    </table>
</div>
