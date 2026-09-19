@props([
    'variant' => 'primary',
    'size' => 'md',
    'href' => null,
    'loading' => false,
    'type' => 'submit',
])

@php
    $variants = [
        'primary' => 'bg-accent-600 text-white hover:bg-accent-700 focus-visible:ring-accent-500',
        'secondary' => 'border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 focus-visible:ring-gray-400',
        'danger' => 'bg-red-600 text-white hover:bg-red-700 focus-visible:ring-red-500',
        'ghost' => 'text-gray-600 hover:text-gray-900 hover:bg-gray-100 focus-visible:ring-gray-400',
    ];

    $sizes = [
        'sm' => 'px-3 py-1.5 text-xs',
        'md' => 'px-4 py-2 text-sm',
        'lg' => 'px-5 py-2.5 text-base',
    ];

    $class = 'inline-flex items-center justify-center gap-2 rounded-md font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60 '.$variants[$variant].' '.$sizes[$size];
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $class]) }}>
        @if ($loading)
            <span class="size-4 animate-spin rounded-full border-2 border-current border-t-transparent"></span>
        @endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $class]) }} @if ($loading) disabled @endif>
        @if ($loading)
            <span class="size-4 animate-spin rounded-full border-2 border-current border-t-transparent"></span>
        @endif
        {{ $slot }}
    </button>
@endif