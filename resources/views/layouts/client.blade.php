@extends('layouts.app')

@section('content')
    <header class="border-b border-gray-200 bg-white">
        <div class="mx-auto flex h-16 max-w-3xl items-center justify-between px-4 sm:px-6">
            <a href="{{ url('/') }}" class="flex items-center gap-2">
                <svg class="size-6 text-accent-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                </svg>
                <span class="text-lg font-semibold text-gray-900">{{ config('app.name') }}</span>
            </a>
        </div>
    </header>

    <main class="mx-auto max-w-3xl px-4 py-10 sm:px-6">
        @yield('client-content')
    </main>

    <footer class="mx-auto max-w-3xl px-4 pb-8 text-center text-xs text-gray-400 sm:px-6">
        {{ config('app.name') }} &middot; {{ date('Y') }}
    </footer>
@endsection