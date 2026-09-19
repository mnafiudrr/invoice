@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <h1 class="text-2xl font-semibold text-gray-900">Dashboard</h1>

    <div class="mt-6 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-lg bg-white p-6 shadow">
            <p class="text-sm font-medium text-gray-500">Projects</p>
            <p class="mt-2 text-3xl font-semibold">{{ $projectCount }}</p>
        </div>
        <div class="rounded-lg bg-white p-6 shadow">
            <p class="text-sm font-medium text-gray-500">Invoices</p>
            <p class="mt-2 text-3xl font-semibold">{{ $invoiceCount }}</p>
        </div>
        <div class="rounded-lg bg-white p-6 shadow">
            <p class="text-sm font-medium text-gray-500">Unpaid</p>
            <p class="mt-2 text-3xl font-semibold">{{ $unpaidCount }}</p>
        </div>
        <div class="rounded-lg bg-white p-6 shadow">
            <p class="text-sm font-medium text-gray-500">Paid</p>
            <p class="mt-2 text-3xl font-semibold">{{ $paidCount }}</p>
        </div>
    </div>

    <div class="mt-8 rounded-lg bg-white p-6 shadow">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold">Recent Invoices</h2>
            <a href="{{ route('admin.invoices.index') }}" class="text-sm text-indigo-600 hover:underline">View all</a>
        </div>

        @if ($recentInvoices->isEmpty())
            <p class="mt-4 text-sm text-gray-500">No invoices yet.</p>
        @else
            <div class="mt-4 overflow-hidden rounded-lg border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Number</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Project</th>
                            <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @foreach ($recentInvoices as $invoice)
                            <tr>
                                <td class="px-6 py-3">
                                    <a href="{{ route('admin.invoices.show', $invoice) }}" class="font-medium text-indigo-600 hover:underline">
                                        {{ $invoice->invoice_number }}
                                    </a>
                                </td>
                                <td class="px-6 py-3 text-sm text-gray-600">{{ $invoice->project->name }}</td>
                                <td class="px-6 py-3 text-right text-sm text-gray-900">{{ format_money($invoice->total, $invoice->currency) }}</td>
                                <td class="px-6 py-3"><x-status-badge :status="$invoice->status" /></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection