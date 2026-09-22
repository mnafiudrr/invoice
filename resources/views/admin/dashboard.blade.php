@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <x-page-header title="Dashboard" subtitle="Overview of your projects and invoices" />

    <div class="mt-6 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-5">
        @php
            $stats = [
                ['label' => 'Projects', 'value' => $projectCount],
                ['label' => 'Invoices', 'value' => $invoiceCount],
                ['label' => 'Unpaid', 'value' => $unpaidCount],
                ['label' => 'Partial', 'value' => $partialCount],
                ['label' => 'Paid', 'value' => $paidCount],
            ];
        @endphp
        @foreach ($stats as $stat)
            <x-card>
                <p class="text-sm font-medium text-gray-500">{{ $stat['label'] }}</p>
                <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $stat['value'] }}</p>
            </x-card>
        @endforeach
    </div>

    <div class="mt-8">
        <x-card title="Recent Invoices">
            @if ($recentInvoices->isEmpty())
                <x-empty-state
                    title="No invoices yet"
                    body="Create your first invoice to get started.">
                    <x-slot:action>
                        <x-button href="{{ route('admin.invoices.create') }}">New Invoice</x-button>
                    </x-slot:action>
                </x-empty-state>
            @else
                <x-table responsive>
                    <x-slot:head>
                        <tr>
                            <th class="px-6 py-3 text-xs font-medium uppercase tracking-wider text-gray-500">Number</th>
                            <th class="px-6 py-3 text-xs font-medium uppercase tracking-wider text-gray-500">Project</th>
                            <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Total</th>
                            <th class="px-6 py-3 text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                        </tr>
                    </x-slot:head>
                    @foreach ($recentInvoices as $invoice)
                        <tr class="table-responsive-row">
                            <td data-label="Number" class="px-6 py-3">
                                <a href="{{ route('admin.invoices.show', $invoice) }}" class="font-medium text-accent-600 hover:underline">
                                    {{ $invoice->invoice_number }}
                                </a>
                            </td>
                            <td data-label="Project" class="px-6 py-3 text-sm text-gray-600">{{ $invoice->project->name }}</td>
                            <td data-label="Total" class="px-6 py-3 text-right text-sm text-gray-900">{{ format_money($invoice->total, $invoice->currency) }}</td>
                            <td data-label="Status" class="px-6 py-3"><x-status-badge :status="$invoice->status" /></td>
                        </tr>
                    @endforeach
                </x-table>
            @endif
        </x-card>
    </div>
@endsection