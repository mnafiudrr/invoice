@extends('layouts.client')

@section('title', $invoice->invoice_number)

@section('client-content')
    <div class="print-area rounded-lg border border-gray-200 bg-white p-6 shadow-sm sm:p-8">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-sm text-gray-500">{{ $invoice->project->client_company ?: $invoice->project->client_name }}</p>
                <h1 class="mt-1 text-2xl font-semibold text-gray-900">{{ $invoice->invoice_number }}</h1>
                <p class="mt-1 text-sm text-gray-500">{{ format_date($invoice->issued_at) }}</p>
            </div>
            <div class="text-right">
                <p class="text-lg font-semibold text-gray-900">{{ format_money($invoice->total, $invoice->currency) }}</p>
                <div class="mt-1 flex items-center justify-end gap-2">
                    @if ($invoice->isPaid())
                        <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700">PAID</span>
                        @if ($invoice->payments->isNotEmpty())
                            <span class="text-xs text-gray-500">on {{ format_date($invoice->payments->first()->paid_at) }}</span>
                        @endif
                    @else
                        <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-medium text-amber-700">
                            UNPAID
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="mt-6 border-t border-gray-100 pt-6">
            <x-table responsive>
                <x-slot:head>
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Description</th>
                        <th class="px-4 py-2 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Qty</th>
                        <th class="px-4 py-2 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Unit Price</th>
                        <th class="px-4 py-2 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Amount</th>
                    </tr>
                </x-slot:head>
                @foreach ($invoice->items as $item)
                    <tr class="table-responsive-row">
                        <td data-label="Description" class="px-4 py-3 text-sm text-gray-900">{{ $item->description }}</td>
                        <td data-label="Qty" class="px-4 py-3 text-right text-sm text-gray-600">{{ $item->quantity }}</td>
                        <td data-label="Unit Price" class="px-4 py-3 text-right text-sm text-gray-600">{{ format_money($item->unit_price, $invoice->currency) }}</td>
                        <td data-label="Amount" class="px-4 py-3 text-right text-sm text-gray-900">{{ format_money($item->amount, $invoice->currency) }}</td>
                    </tr>
                @endforeach
            </x-table>

            <div class="mt-4 flex justify-end">
                <dl class="w-full max-w-xs space-y-2 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Subtotal</dt>
                        <dd class="font-medium">{{ format_money($invoice->subtotal, $invoice->currency) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Tax</dt>
                        <dd class="font-medium">{{ format_money($invoice->tax, $invoice->currency) }}</dd>
                    </div>
                    <div class="flex justify-between border-t pt-2">
                        <dt class="font-semibold">Total</dt>
                        <dd class="font-semibold">{{ format_money($invoice->total, $invoice->currency) }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        @if ($invoice->notes)
            <div class="mt-6 rounded bg-gray-50 p-4">
                <p class="text-xs font-medium uppercase tracking-wider text-gray-500">Notes</p>
                <p class="mt-1 text-sm text-gray-700">{{ $invoice->notes }}</p>
            </div>
        @endif
    </div>

    <div class="mt-6 flex flex-wrap items-center gap-3 print:hidden">
        @if ($invoice->files->where('type', 'invoice')->isNotEmpty())
            <x-button href="{{ route('shares.pdf', $shareLink) }}">View Invoice PDF</x-button>
        @endif
        @foreach ($invoice->files as $file)
            @if ($file->type !== 'invoice')
                <x-button href="{{ route('shares.file', [$shareLink, $file]) }}" variant="secondary">
                    {{ $file->type === 'payment_receipt' ? 'Payment Receipt' : ($file->type === 'payment_proof' ? 'Payment Proof' : $file->original_filename) }}
                </x-button>
            @endif
        @endforeach
        <x-button variant="secondary" onclick="window.print()" class="ml-auto">Print</x-button>
    </div>
@endsection