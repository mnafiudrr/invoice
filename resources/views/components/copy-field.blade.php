@props([
    'value' => null,
    'label' => null,
    'monospace' => false,
])

<div {{ $attributes->merge(['class' => 'flex items-center gap-2']) }} x-data="{ copied: false }">
    <div class="min-w-0 flex-1">
        @if ($label)
            <p class="text-xs font-medium text-gray-500">{{ $label }}</p>
        @endif
        <code @class(['block truncate rounded bg-gray-50 px-3 py-1.5 text-sm text-gray-800', 'font-mono' => $monospace])>{{ $value }}</code>
    </div>
    <button type="button"
            @click="navigator.clipboard.writeText('{{ $value }}'); copied = true; setTimeout(() => copied = false, 2000)"
            class="shrink-0 rounded-md bg-accent-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-accent-700"
            x-text="copied ? 'Copied!' : 'Copy'"></button>
</div>