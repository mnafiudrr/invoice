@extends('layouts.admin')

@section('title', 'New Project')

@section('content')
    <x-page-header title="New Project" subtitle="Create a project to group invoices per client" />

    <form method="POST" action="{{ route('admin.projects.store') }}" class="mt-6 max-w-2xl space-y-6"
          x-data="{ submitting: false }" @submit="submitting = true">
        @csrf
        @include('admin.projects._form')

        <div class="flex items-center gap-3">
            <x-button type="submit" x-bind:disabled="submitting">
                <span x-show="submitting" x-cloak>Creating…</span>
                <span x-show="!submitting">Create Project</span>
            </x-button>
            <a href="{{ route('admin.projects.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Cancel</a>
        </div>
    </form>
@endsection