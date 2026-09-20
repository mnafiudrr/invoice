@extends('layouts.app')

@section('title', $title)

@section('content')
    <div class="flex min-h-screen flex-col items-center justify-center px-4 py-12 text-center">
        <p class="text-7xl font-bold text-gray-200">{{ $code }}</p>
        <h1 class="mt-4 text-xl font-semibold text-gray-900">{{ $title }}</h1>
        <p class="mt-2 max-w-md text-sm text-gray-500">{{ $message }}</p>
        <div class="mt-6">
            @if (isset($back))
                <x-button :href="$back" variant="secondary">Go back</x-button>
            @else
                <x-button href="{{ url('/') }}">Go home</x-button>
            @endif
        </div>
    </div>
@endsection