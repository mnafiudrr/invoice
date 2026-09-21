@extends('layouts.pdf')

@section('content')
    <div class="invoice">
        <div class="header">
            <div>
                <h1>{{ __('invoice.invoice') }}</h1>
                <p class="number">{{ $invoice->invoice_number }}</p>
                <p class="date">{{ __('invoice.issued_date') }}: {{ format_date($invoice->issued_at) }}</p>
                @if ($invoice->due_at)
                    <p class="date">{{ __('invoice.due_date') }}: {{ format_date($invoice->due_at) }}</p>
                @endif
            </div>
        </div>

        <div class="body">
            @if ($invoice->isPaid())
                <div class="paid-overlay">
                    <div class="paid-stamp-cell">
                        <div class="paid-stamp">{{ __('invoice.paid') }}</div>
                    </div>
                </div>
            @endif

            <div class="parties">
                <div>
                    <h2>{{ __('invoice.from') }}</h2>
                    <p class="company">{{ config('app.owner.company') }}</p>
                    <p>{{ config('app.owner.name') }}</p>
                    <p>{{ config('app.owner.address') }}</p>
                    <p>{{ config('app.owner.phone') }}</p>
                    <p>{{ config('app.owner.email') }}</p>
                </div>
                <div>
                    <h2>{{ __('invoice.bill_to') }}</h2>
                    <p class="company">{{ $invoice->project->client_company ?: $invoice->project->client_name }}</p>
                    <p>{{ $invoice->project->client_name }}</p>
                    @if ($invoice->project->client_address)
                        <p>{{ $invoice->project->client_address }}</p>
                    @endif
                    <p>{{ $invoice->project->client_email }}</p>
                </div>
            </div>

            <table class="items">
                <thead>
                    <tr>
                        <th>{{ __('invoice.description') }}</th>
                        <th class="num">{{ __('invoice.quantity') }}</th>
                        <th class="num">{{ __('invoice.unit_price') }}</th>
                        <th class="num">{{ __('invoice.amount') }}</th>
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
                    <span>{{ __('invoice.subtotal') }}</span>
                    <span>{{ format_money($invoice->subtotal, $invoice->currency) }}</span>
                </div>
                <div class="row">
                    <span>{{ __('invoice.tax') }}</span>
                    <span>{{ format_money($invoice->tax, $invoice->currency) }}</span>
                </div>
                <div class="row total">
                    <span>{{ __('invoice.total') }}</span>
                    <span>{{ format_money($invoice->total, $invoice->currency) }}</span>
                </div>
            </div>

            @if ($invoice->notes)
                <div class="notes">
                    <h2>{{ __('invoice.notes') }}</h2>
                    <p>{{ $invoice->notes }}</p>
                </div>
            @endif

            @if ($invoice->payment_terms)
                <div class="terms">
                    <h2>{{ __('invoice.payment_information') }}</h2>
                    <p>{{ $invoice->payment_terms }}</p>
                </div>
            @endif
        </div>

        <p class="thanks">{{ __('invoice.thank_you') }}</p>
    </div>
@endsection