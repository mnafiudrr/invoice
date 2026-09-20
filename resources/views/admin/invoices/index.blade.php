@extends('layouts.admin')

@section('title', 'Invoices')

@section('content')
    <x-page-header title="Invoices" subtitle="All invoices across projects">
        <x-slot:actions>
            <x-button href="{{ route('admin.invoices.create') }}">New Invoice</x-button>
        </x-slot:actions>
    </x-page-header>

    <form method="GET" action="{{ route('admin.invoices.index') }}" class="mt-6 flex flex-wrap items-end gap-3">
        <x-select name="project_id" :label="'Project'" :options="$projects->pluck('name', 'id')->all()"
                  placeholder="All projects" :value="request('project_id')" />
        <x-select name="status" :label="'Status'" :options="$statuses" placeholder="All statuses"
                  :value="request('status')" />
        <x-button type="submit" variant="secondary">Filter</x-button>
    </form>

    <div class="mt-6">
        @if ($invoices->isEmpty())
            <x-empty-state title="No invoices yet" body="Create your first invoice.">
                <x-slot:action>
                    <x-button href="{{ route('admin.invoices.create') }}">New Invoice</x-button>
                </x-slot:action>
            </x-empty-state>
        @else
            <x-card>
                <x-table responsive>
                    <x-slot:head>
                        <tr>
                            <th class="px-6 py-3 text-xs font-medium uppercase tracking-wider text-gray-500">Number</th>
                            <th class="px-6 py-3 text-xs font-medium uppercase tracking-wider text-gray-500">Project</th>
                            <th class="px-6 py-3 text-xs font-medium uppercase tracking-wider text-gray-500">Issued</th>
                            <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Total</th>
                            <th class="px-6 py-3 text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Actions</th>
                        </tr>
                    </x-slot:head>
                    @foreach ($invoices as $invoice)
                        <tr class="table-responsive-row">
                            <td data-label="Number" class="px-6 py-4">
                                <a href="{{ route('admin.invoices.show', $invoice) }}" class="font-medium text-accent-600 hover:underline">
                                    {{ $invoice->invoice_number }}
                                </a>
                            </td>
                            <td data-label="Project" class="px-6 py-4 text-sm text-gray-600">{{ $invoice->project->name }}</td>
                            <td data-label="Issued" class="px-6 py-4 text-sm text-gray-600">{{ format_date($invoice->issued_at) }}</td>
                            <td data-label="Total" class="px-6 py-4 text-right text-sm text-gray-900">{{ format_money($invoice->total, $invoice->currency) }}</td>
                            <td data-label="Status" class="px-6 py-4"><x-status-badge :status="$invoice->status" /></td>
                            <td data-label="Actions" class="px-6 py-4 text-right text-sm">
                                <a href="{{ route('admin.invoices.show', $invoice) }}" class="text-accent-600 hover:underline">View</a>
                            </td>
                        </tr>
                    @endforeach
                </x-table>
            </x-card>
            <x-pagination :paginator="$invoices" />
        @endif
    </div>
@endsection