@extends('layouts.pdf')

@section('content')
    <div class="sheet" style="position: relative;">
        @if ($invoice->isPaid())
            <div class="paid-overlay">
                <div class="paid-stamp-cell">
                    <div class="paid-stamp">{{ __('invoice.paid') }}</div>
                </div>
            </div>
        @endif

        <div class="top-line"></div>

        <div class="parties">
            <div class="from-block">
                <p class="company">{{ config('app.owner.company') }}</p>
                <p>{{ config('app.owner.name') }}</p>
                <p>{{ config('app.owner.address') }}</p>
                <p>{{ config('app.owner.phone') }}</p>
                <p>{{ config('app.owner.email') }}</p>
            </div>
            <div class="bill-to">
                <p class="label">{{ __('invoice.bill_to') }}</p>
                <p>{{ $invoice->project->client_company ?: $invoice->project->client_name }}</p>
                <p>{{ $invoice->project->client_name }}</p>
                @if ($invoice->project->client_address)
                    <p>{{ $invoice->project->client_address }}</p>
                @endif
                <p>{{ $invoice->project->client_email }}</p>
            </div>
        </div>

        <div class="meta">
            <div class="col">
                <p class="k">{{ __('invoice.invoice') }}</p>
                <p class="v">{{ $invoice->invoice_number }}</p>
            </div>
            <div class="col">
                <p class="k">{{ __('invoice.issued_date') }}</p>
                <p class="v">{{ $invoice->issued_at?->format('d M Y') }}</p>
            </div>
            @if ($invoice->due_at)
                <div class="col">
                    <p class="k">{{ __('invoice.due_date') }}</p>
                    <p class="v">{{ $invoice->due_at?->format('d M Y') }}</p>
                </div>
            @endif
        </div>

        <table class="items">
            <thead>
                <tr>
                    <th class="desc">{{ __('invoice.description') }}</th>
                    <th class="qty num">{{ __('invoice.quantity') }}</th>
                    <th class="price num">{{ __('invoice.unit_price') }}</th>
                    <th class="amt num">{{ __('invoice.amount') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoice->items as $item)
                    <tr>
                        <td>{{ $item->description }}</td>
                        <td class="num">{{ $item->quantity }}</td>
                        <td class="num">{{ format_money($item->unit_price, $invoice->currency) }}</td>
                        <td class="num">{{ format_money($item->amount, $invoice->currency) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <div class="row">
                <span class="k">{{ __('invoice.subtotal') }}</span>
                <span class="v">{{ format_money($invoice->subtotal, $invoice->currency) }}</span>
            </div>
            <div class="row">
                <span class="k">{{ __('invoice.tax') }}</span>
                <span class="v">{{ format_money($invoice->tax, $invoice->currency) }}</span>
            </div>
            <div class="row total">
                <span class="k">{{ __('invoice.total') }}</span>
                <span class="v">{{ format_money($invoice->total, $invoice->currency) }}</span>
            </div>
        </div>

        @if ($invoice->notes)
            <div class="notes">
                <h3>{{ __('invoice.notes') }}</h3>
                <p>{{ $invoice->notes }}</p>
            </div>
        @endif

        @if ($invoice->payment_terms)
            <div class="terms">
                <h3>{{ __('invoice.payment_information') }}</h3>
                <p>{{ $invoice->payment_terms }}</p>
            </div>
        @endif
    </div>
@endsection