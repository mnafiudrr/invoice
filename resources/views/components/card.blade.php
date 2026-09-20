@props([
    'title' => null,
    'subtitle' => null,
    'padding' => true,
])

<div {{ $attributes->merge(['class' => 'rounded-lg border border-gray-200 bg-white shadow-card']) }}>
    @if ($title || isset($actions))
        <div class="flex items-center justify-between gap-4 border-b border-gray-100 px-6 py-4">
            <div>
                <h2 class="text-base font-semibold text-gray-900">{{ $title }}</h2>
                @if ($subtitle)
                    <p class="mt-0.5 text-sm text-gray-500">{{ $subtitle }}</p>
                @endif
            </div>
            @isset($actions)
                <div class="shrink-0">{{ $actions }}</div>
            @endisset
        </div>
    @endif

    <div @class(['px-6 py-5' => $padding])>
        {{ $slot }}
    </div>

    @isset($footer)
        <div class="border-t border-gray-100 px-6 py-4">
            {{ $footer }}
        </div>
    @endisset
</div>