@extends('layouts.app')

@section('title', $project->name)

@section('content')
    <div class="mx-auto max-w-3xl px-4 py-16">
        <h1 class="text-2xl font-semibold text-gray-900">{{ $project->name }}</h1>
        <p class="mt-1 text-sm text-gray-600">{{ $project->client_company ?: $project->client_name }}</p>

        <div class="mt-8 space-y-4">
            @forelse ($invoices as $invoice)
                <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium text-gray-900">{{ $invoice->invoice_number }}</p>
                            <p class="mt-1 text-sm text-gray-500">{{ format_date($invoice->issued_at) }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-medium text-gray-900">{{ format_money($invoice->total, $invoice->currency) }}</p>
                            <div class="mt-1 flex items-center justify-end gap-2">
                                @if ($invoice->isPaid())
                                    <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700">
                                        PAID
                                    </span>
                                    @if ($invoice->payments->isNotEmpty())
                                        <span class="text-xs text-gray-500">
                                            on {{ format_date($invoice->payments->first()->paid_at) }}
                                        </span>
                                    @endif
                                @else
                                    <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-medium text-amber-700">
                                        UNPAID
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 flex flex-wrap items-center gap-3 border-t border-gray-100 pt-4">
                        <a href="{{ route('invoices.show', [$project, $invoice]) }}"
                           class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                            View Invoice
                        </a>
                        @foreach ($invoice->files as $file)
                            @if ($file->type !== 'invoice')
                                <a href="{{ route('invoices.file', [$project, $invoice, $file]) }}"
                                   class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                    {{ $file->type === 'payment_receipt' ? 'Payment Receipt' : ($file->type === 'payment_proof' ? 'Payment Proof' : $file->original_filename) }}
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="rounded-lg border border-dashed border-gray-300 bg-white p-12 text-center">
                    <p class="text-sm text-gray-500">No documents have been shared for this project yet.</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection