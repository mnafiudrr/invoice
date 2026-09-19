@extends('layouts.admin')

@section('title', 'New Project')

@section('content')
    <h1 class="text-2xl font-semibold text-gray-900">New Project</h1>

    <form method="POST" action="{{ route('admin.projects.store') }}" class="mt-6 max-w-2xl space-y-6">
        @csrf

        <div class="rounded-lg bg-white p-6 shadow">
            <h2 class="text-lg font-semibold">Project</h2>
            <div class="mt-4 space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Project name</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea id="description" name="description" rows="3"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description') }}</textarea>
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Access password</label>
                    <input type="password" id="password" name="password" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <p class="mt-1 text-xs text-gray-500">Shared with clients to open this project.</p>
                </div>
            </div>
        </div>

        <div class="rounded-lg bg-white p-6 shadow">
            <h2 class="text-lg font-semibold">Client</h2>
            <div class="mt-4 space-y-4">
                <div>
                    <label for="client_name" class="block text-sm font-medium text-gray-700">Client name</label>
                    <input type="text" id="client_name" name="client_name" value="{{ old('client_name') }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="client_email" class="block text-sm font-medium text-gray-700">Client email</label>
                    <input type="email" id="client_email" name="client_email" value="{{ old('client_email') }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="client_company" class="block text-sm font-medium text-gray-700">Client company</label>
                    <input type="text" id="client_company" name="client_company" value="{{ old('client_company') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit"
                    class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                Create Project
            </button>
            <a href="{{ route('admin.projects.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Cancel</a>
        </div>
    </form>
@endsection