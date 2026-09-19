@props([
    'title' => 'Nothing here yet',
    'body' => null,
])

<div {{ $attributes->merge(['class' => 'rounded-lg border border-dashed border-gray-300 bg-gray-50/50 px-6 py-16 text-center']) }}>
    <div class="mx-auto flex size-12 items-center justify-center rounded-full bg-gray-100 text-gray-400">
        <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 0a9 9 0 1 1 16.5 0m-16.5 0a9 9 0 1 0 16.5 0" />
        </svg>
    </div>
    <h3 class="mt-4 text-sm font-semibold text-gray-900">{{ $title }}</h3>
    @if ($body)
        <p class="mt-1 text-sm text-gray-500">{{ $body }}</p>
    @endif
    @isset($action)
        <div class="mt-4">{{ $action }}</div>
    @endisset
</div>