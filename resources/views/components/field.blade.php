@props([
    'label' => null,
    'name' => null,
    'hint' => null,
    'error' => null,
    'required' => false,
])

@php
    $error = $error ?? ($name ? $errors->first($name) : null);
    $id = $attributes->get('id', $name);
@endphp

<div>
    @if ($label)
        <label for="{{ $id }}" class="block text-sm font-medium text-gray-700">
            {{ $label }}
            @if ($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    {{ $slot }}

    @if ($hint && ! $error)
        <p class="mt-1 text-xs text-gray-500">{{ $hint }}</p>
    @endif

    @if ($error)
        <p id="{{ $id }}-error" class="mt-1 text-xs text-red-600">{{ $error }}</p>
    @endif
</div>