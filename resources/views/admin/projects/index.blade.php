@extends('layouts.admin')

@section('title', 'Projects')

@section('content')
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-gray-900">Projects</h1>
        <a href="{{ route('admin.projects.create') }}"
           class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
            New Project
        </a>
    </div>

    <div class="mt-6 overflow-hidden rounded-lg bg-white shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Client</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Invoices</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Created</th>
                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse ($projects as $project)
                    <tr>
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.projects.show', $project) }}" class="font-medium text-indigo-600 hover:underline">
                                {{ $project->name }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $project->client_name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $project->invoices_count }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $project->created_at->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-right text-sm">
                            <a href="{{ route('admin.projects.show', $project) }}" class="text-indigo-600 hover:underline">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500">
                            No projects yet. Create your first project.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <x-pagination :paginator="$projects" />
@endsection