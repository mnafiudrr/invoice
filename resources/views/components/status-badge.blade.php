@props(['status'])

@php
    $tones = [
        'draft' => 'bg-gray-100 text-gray-700',
        'sent' => 'bg-blue-100 text-blue-700',
        'paid' => 'bg-green-100 text-green-700',
        'cancelled' => 'bg-red-100 text-red-700',
    ];
    $labels = [
        'draft' => 'Draft',
        'sent' => 'Sent',
        'paid' => 'Paid',
        'cancelled' => 'Cancelled',
    ];
    $class = $tones[$status] ?? 'bg-gray-100 text-gray-700';
    $label = $labels[$status] ?? ucfirst((string) $status);
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium '.$class]) }}>
    {{ $label }}
</span>