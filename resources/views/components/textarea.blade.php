@props([
    'label' => null,
    'name' => null,
    'hint' => null,
    'error' => null,
    'required' => false,
    'value' => null,
])

@php
    $error = $error ?? ($name ? $errors->first($name) : null);
    $id = $attributes->get('id', $name);
    $aria = $error ? ['aria-describedby' => $id.'-error'] : [];
    $value = $value ?? old($name);
@endphp

<x-field :label="$label" :name="$name" :hint="$hint" :error="$error" :required="$required" :id="$id">
    <textarea
        {{ $attributes->merge(['class' => 'field-base', 'id' => $id, 'name' => $name, 'required' => $required])->merge($aria) }}
    >{{ $value }}</textarea>
</x-field>