@extends('layouts.admin')

@section('title', 'Invoices')

@section('content')
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-gray-900">Invoices</h1>
        <a href="{{ route('admin.invoices.create') }}"
           class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
            New Invoice
        </a>
    </div>

    <form method="GET" action="{{ route('admin.invoices.index') }}" class="mt-6 flex flex-wrap items-center gap-3">
        <select name="project_id" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">All projects</option>
            @foreach ($projects as $project)
                <option value="{{ $project->id }}" @selected(request('project_id') == $project->id)>{{ $project->name }}</option>
            @endforeach
        </select>
        <select name="status" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">All statuses</option>
            @foreach ($statuses as $status)
                <option value="{{ $status }}" @selected(request('status') == $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
        <button type="submit" class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
            Filter
        </button>
    </form>

    <div class="mt-6 overflow-hidden rounded-lg bg-white shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Number</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Project</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Issued</th>
                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Total</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse ($invoices as $invoice)
                    <tr>
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.invoices.show', $invoice) }}" class="font-medium text-indigo-600 hover:underline">
                                {{ $invoice->invoice_number }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $invoice->project->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ format_date($invoice->issued_at) }}</td>
                        <td class="px-6 py-4 text-right text-sm text-gray-900">{{ format_money($invoice->total, $invoice->currency) }}</td>
                        <td class="px-6 py-4">
                            <x-status-badge :status="$invoice->status" />
                        </td>
                        <td class="px-6 py-4 text-right text-sm">
                            <a href="{{ route('admin.invoices.show', $invoice) }}" class="text-indigo-600 hover:underline">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500">
                            No invoices yet. Create your first invoice.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <x-pagination :paginator="$invoices" />
@endsection