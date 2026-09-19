@extends('layouts.app')

@section('title', 'Page not found')

@section('content')
    <div class="flex min-h-screen flex-col items-center justify-center px-4 text-center">
        <h1 class="text-6xl font-bold text-gray-900">404</h1>
        <p class="mt-4 text-lg text-gray-600">Page not found.</p>
        <p class="mt-1 text-sm text-gray-500">The page you are looking for does not exist or has been removed.</p>
        <a href="{{ url('/') }}" class="mt-6 rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
            Go home
        </a>
    </div>
@endsection