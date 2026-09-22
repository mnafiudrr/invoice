@extends('layouts.client')

@section('title', $project->name)

@section('client-content')
    @php
        $total = $invoices->sum(fn ($invoice) => (float) $invoice->total);
    @endphp

    <div class="mb-8">
        <h1 class="text-2xl font-semibold text-gray-900">{{ $project->name }}</h1>
        <p class="mt-1 text-sm text-gray-600">{{ $project->client_company ?: $project->client_name }}</p>
        @if ($invoices->isNotEmpty())
            <p class="mt-3 text-sm text-gray-500">
                {{ $invoices->count() }} {{ $invoices->count() === 1 ? 'document' : 'documents' }}
                &middot; total {{ format_money($total, $invoices->first()->currency) }}
            </p>
        @endif
    </div>

    @if ($invoices->isEmpty())
        <x-empty-state title="No documents yet" body="No documents have been shared for this project yet." />
    @else
        <div class="space-y-4">
            @foreach ($invoices as $invoice)
                <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <p class="font-medium text-gray-900">{{ $invoice->invoice_number }}</p>
                            <p class="mt-1 text-sm text-gray-500">{{ format_date($invoice->issued_at) }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-medium text-gray-900">{{ format_money($invoice->total, $invoice->currency) }}</p>
                            <div class="mt-1 flex items-center justify-end gap-2">
                                @if ($invoice->isPaid())
                                    <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700">PAID</span>
                                    @if ($invoice->payments->isNotEmpty())
                                        <span class="text-xs text-gray-500">on {{ format_date($invoice->payments->last()->paid_at) }}</span>
                                    @endif
                                @elseif ($invoice->isPartiallyPaid())
                                    <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-medium text-amber-700">PARTIALLY PAID</span>
                                    <span class="text-xs text-gray-500">Paid {{ format_money($invoice->paidAmount(), $invoice->currency) }} of {{ format_money($invoice->total, $invoice->currency) }}</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-medium text-amber-700">
                                        UNPAID
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 flex flex-wrap items-center gap-3 border-t border-gray-100 pt-4">
                        <x-button href="{{ route('invoices.show', [$project, $invoice]) }}">View Invoice</x-button>
                        @foreach ($invoice->files as $file)
                            @if ($file->type !== 'invoice')
                                <x-button href="{{ route('invoices.file', [$project, $invoice, $file]) }}" variant="secondary">
                                    {{ $file->type === 'payment_receipt' ? 'Payment Receipt' : ($file->type === 'payment_proof' ? 'Payment Proof' : $file->original_filename) }}
                                </x-button>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection