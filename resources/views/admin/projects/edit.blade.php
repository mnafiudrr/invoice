@extends('layouts.admin')

@section('title', 'Edit Project')

@section('content')
    <x-page-header title="Edit Project" subtitle="Update project details">
        <x-slot:actions>
            <x-button href="{{ route('admin.projects.show', $project) }}" variant="secondary">View Project</x-button>
        </x-slot:actions>
    </x-page-header>

    <form method="POST" action="{{ route('admin.projects.update', $project) }}" class="mt-6 max-w-2xl space-y-6"
          x-data="{ submitting: false }" @submit="submitting = true">
        @csrf
        @method('PUT')
        @include('admin.projects._form', ['project' => $project])

        <div class="flex items-center gap-3">
            <x-button type="submit" x-bind:disabled="submitting">
                <span x-show="submitting" x-cloak>Updating…</span>
                <span x-show="!submitting">Update Project</span>
            </x-button>
            <a href="{{ route('admin.projects.show', $project) }}" class="text-sm text-gray-600 hover:text-gray-900">Cancel</a>
        </div>
    </form>
@endsection