@extends('layouts.app')

@section('title', 'Forbidden')

@section('content')
    <div class="flex min-h-screen flex-col items-center justify-center px-4 text-center">
        <h1 class="text-6xl font-bold text-gray-900">403</h1>
        <p class="mt-4 text-lg text-gray-600">Forbidden.</p>
        <p class="mt-1 text-sm text-gray-500">You do not have permission to access this page.</p>
        <a href="{{ url('/') }}" class="mt-6 rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
            Go home
        </a>
    </div>
@endsection