@extends('layouts.admin')

@section('title', 'Projects')

@section('content')
    <x-page-header title="Projects" subtitle="All client projects">
        <x-slot:actions>
            <x-button href="{{ route('admin.projects.create') }}">New Project</x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="mt-6">
        @if ($projects->isEmpty())
            <x-empty-state
                title="No projects yet"
                body="Create a project to start grouping invoices per client.">
                <x-slot:action>
                    <x-button href="{{ route('admin.projects.create') }}">New Project</x-button>
                </x-slot:action>
            </x-empty-state>
        @else
            <x-card>
                <x-table responsive>
                    <x-slot:head>
                        <tr>
                            <th class="px-6 py-3 text-xs font-medium uppercase tracking-wider text-gray-500">Name</th>
                            <th class="px-6 py-3 text-xs font-medium uppercase tracking-wider text-gray-500">Client</th>
                            <th class="px-6 py-3 text-xs font-medium uppercase tracking-wider text-gray-500">Invoices</th>
                            <th class="px-6 py-3 text-xs font-medium uppercase tracking-wider text-gray-500">Created</th>
                            <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Actions</th>
                        </tr>
                    </x-slot:head>
                    @foreach ($projects as $project)
                        <tr class="table-responsive-row">
                            <td data-label="Name" class="px-6 py-4">
                                <a href="{{ route('admin.projects.show', $project) }}" class="font-medium text-accent-600 hover:underline">
                                    {{ $project->name }}
                                </a>
                            </td>
                            <td data-label="Client" class="px-6 py-4 text-sm text-gray-600">{{ $project->client_name }}</td>
                            <td data-label="Invoices" class="px-6 py-4 text-sm text-gray-600">{{ $project->invoices_count }}</td>
                            <td data-label="Created" class="px-6 py-4 text-sm text-gray-600">{{ $project->created_at->format('d M Y') }}</td>
                            <td data-label="Actions" class="px-6 py-4 text-right text-sm">
                                <a href="{{ route('admin.projects.show', $project) }}" class="text-accent-600 hover:underline">View</a>
                            </td>
                        </tr>
                    @endforeach
                </x-table>
            </x-card>
            <x-pagination :paginator="$projects" />
        @endif
    </div>
@endsection