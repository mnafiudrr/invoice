@extends('layouts.app')

@section('title', 'Sign in')

@section('content')
    <div class="flex min-h-screen items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">
            <div class="mb-8 text-center">
                <div class="mx-auto flex size-12 items-center justify-center rounded-xl bg-accent-600 text-white">
                    <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                    </svg>
                </div>
                <h1 class="mt-4 text-2xl font-semibold text-gray-900">{{ config('app.name') }}</h1>
                <p class="mt-1 text-sm text-gray-500">Sign in to the admin dashboard.</p>
            </div>

            <x-card>
                <form method="POST" action="{{ route('login.attempt') }}" class="space-y-4">
                    @csrf
                    <x-input name="email" type="email" :label="'Email'" :required="true" autofocus />
                    <x-input name="password" type="password" :label="'Password'" :required="true" />

                    <div class="flex items-center justify-between">
                        <label class="flex items-center text-sm text-gray-600">
                            <input type="checkbox" name="remember"
                                   class="rounded border-gray-300 text-accent-600 focus:ring-accent-500">
                            <span class="ml-2">Remember me</span>
                        </label>
                    </div>

                    <x-button type="submit" class="w-full">Sign in</x-button>
                </form>
            </x-card>
        </div>
    </div>
@endsection