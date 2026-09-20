@extends('layouts.client')

@section('title', $invoice->invoice_number)

@section('client-content')
    @include('partials.invoice.document', ['invoice' => $invoice])

    <div class="mt-6 flex flex-wrap items-center gap-3 print:hidden">
        @if ($invoice->files->where('type', 'invoice')->isNotEmpty())
            <x-button href="{{ route('admin.invoices.download-pdf', $invoice) }}">View PDF</x-button>
        @endif
        @foreach ($invoice->files as $file)
            @if ($file->type !== 'invoice')
                <x-button href="{{ route('admin.invoices.files.download', [$invoice, $file]) }}" variant="secondary">
                    {{ $file->type === 'payment_receipt' ? 'Payment Receipt' : ($file->type === 'payment_proof' ? 'Payment Proof' : $file->original_filename) }}
                </x-button>
            @endif
        @endforeach
        <x-button href="{{ route('admin.invoices.show', $invoice) }}" variant="ghost" class="ml-auto">Back to invoice</x-button>
        <x-button variant="secondary" onclick="window.print()">Print</x-button>
    </div>
@endsection