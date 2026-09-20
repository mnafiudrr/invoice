@props([
    'title' => 'Protected document',
    'subtitle' => 'This content is password protected.',
    'action' => null,
])

<div class="flex min-h-screen items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">
        <div class="mb-8 text-center">
            <div class="mx-auto flex size-12 items-center justify-center rounded-xl bg-accent-600 text-white">
                <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                </svg>
            </div>
            <h1 class="mt-4 text-2xl font-semibold text-gray-900">{{ $title }}</h1>
            <p class="mt-1 text-sm text-gray-500">{{ $subtitle }}</p>
        </div>

        <x-card>
            <form method="POST" action="{{ $action }}" class="space-y-4">
                @csrf
                <x-input name="password" type="password" :label="'Password'" :required="true" autofocus />
                <x-button type="submit" class="w-full">Continue</x-button>
            </form>
        </x-card>
    </div>
</div>