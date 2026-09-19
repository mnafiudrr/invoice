@props([
    'type' => 'info',
    'dismissible' => false,
])

@php
    $styles = [
        'success' => 'border-green-200 bg-green-50 text-green-800',
        'error' => 'border-red-200 bg-red-50 text-red-800',
        'warning' => 'border-amber-200 bg-amber-50 text-amber-800',
        'info' => 'border-blue-200 bg-blue-50 text-blue-800',
    ];
    $class = $styles[$type] ?? $styles['info'];
@endphp

<div
    {{ $attributes->merge(['class' => 'rounded-md border px-4 py-3 text-sm '.$class]) }}
    @if ($dismissible) x-data="{ show: true }" x-show="show" x-cloak @endif
    role="alert"
>
    <div class="flex items-start justify-between gap-3">
        <div>{{ $slot }}</div>
        @if ($dismissible)
            <button type="button" @click="show = false" class="shrink-0 opacity-60 hover:opacity-100" aria-label="Dismiss">
                &times;
            </button>
        @endif
    </div>
</div>