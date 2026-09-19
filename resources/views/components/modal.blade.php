@props([
    'id' => null,
    'title' => null,
    'confirmLabel' => 'Confirm',
    'confirmVariant' => 'primary',
])

<div
    {{ $attributes->merge(['class' => 'relative z-50']) }}
    x-data="{ open: false }"
    @confirm-open.window="if ($event.detail?.id === '{{ $id }}') open = true"
    @keydown.escape.window="open = false"
    x-cloak
>
    <div x-show="open" x-transition.opacity
         class="fixed inset-0 z-40 bg-gray-900/50"
         @click="open = false"></div>

    <div x-show="open" x-transition role="dialog" aria-modal="true"
         class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-xl">
            @if ($title)
                <h2 class="text-lg font-semibold text-gray-900">{{ $title }}</h2>
            @endif
            <div class="mt-2 text-sm text-gray-600">{{ $slot }}</div>
            <div class="mt-6 flex items-center justify-end gap-3">
                <button type="button" @click="open = false"
                        class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>
                <button type="button" @click="open = false; $dispatch('confirm-accepted', { id: '{{ $id }}' })"
                        class="inline-flex items-center justify-center rounded-md px-4 py-2 text-sm font-medium text-white disabled:opacity-60 {{ $confirmVariant === 'danger' ? 'bg-red-600 hover:bg-red-700' : 'bg-accent-600 hover:bg-accent-700' }}">
                    {{ $confirmLabel }}
                </button>
            </div>
        </div>
    </div>
</div>