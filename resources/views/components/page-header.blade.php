@props([
    'title' => null,
    'subtitle' => null,
])

<div {{ $attributes->merge(['class' => 'flex flex-wrap items-start justify-between gap-4']) }}>
    <div>
        @if (isset($breadcrumbs))
            <div class="mb-1">{{ $breadcrumbs }}</div>
        @endif
        <h1 class="text-2xl font-semibold text-gray-900">{{ $title }}</h1>
        @if ($subtitle)
            <p class="mt-1 text-sm text-gray-600">{{ $subtitle }}</p>
        @endif
    </div>
    @isset($actions)
        <div class="flex flex-wrap items-center gap-3">{{ $actions }}</div>
    @endisset
</div>