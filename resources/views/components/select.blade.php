@props([
    'label' => null,
    'name' => null,
    'hint' => null,
    'error' => null,
    'required' => false,
    'options' => [],
    'placeholder' => null,
])

@php
    $error = $error ?? ($name ? $errors->first($name) : null);
    $id = $attributes->get('id', $name);
    $aria = $error ? ['aria-describedby' => $id.'-error'] : [];
    $value = old($name, $attributes->get('value'));
@endphp

<x-field :label="$label" :name="$name" :hint="$hint" :error="$error" :required="$required" :id="$id">
    <select
        {{ $attributes->merge(['class' => 'field-base', 'id' => $id, 'name' => $name, 'required' => $required])->merge($aria) }}
    >
        @if ($placeholder)
            <option value="" @selected($value === null || $value === '')>{{ $placeholder }}</option>
        @endif
        @foreach ($options as $optionValue => $optionLabel)
            @if (is_int($optionValue))
                <option value="{{ $optionLabel }}" @selected($value == $optionLabel)>{{ $optionLabel }}</option>
            @else
                <option value="{{ $optionValue }}" @selected($value == $optionValue)>{{ $optionLabel }}</option>
            @endif
        @endforeach
        {{ $slot }}
    </select>
</x-field>