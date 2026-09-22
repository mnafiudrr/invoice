@extends('layouts.pdf')

@section('content')
    <div class="invoice">
        @if ($invoice->isPaid())
            <div class="paid-overlay">
                <div class="paid-stamp-cell">
                    <div class="paid-stamp">{{ __('invoice.paid') }}</div>
                </div>
            </div>
        @endif

        <table class="head" style="width:100%;">
            <tr>
                <td style="width:55%; vertical-align: top;">
                    <p class="label">{{ __('invoice.invoice') }}</p>
                    <p class="number">{{ $invoice->invoice_number }}</p>
                    <p class="date">{{ __('invoice.issued_date') }}: {{ format_date($invoice->issued_at) }}</p>
                    @if ($invoice->due_at)
                        <p class="date">{{ __('invoice.due_date') }}: {{ format_date($invoice->due_at) }}</p>
                    @endif
                </td>
                <td style="width:45%; text-align: right; vertical-align: top;">
                    <p style="font-size:18px; font-weight:600; margin:0; color:#111827;">{{ format_money($invoice->total, $invoice->currency) }}</p>
                    <div style="margin-top:4px;">
                        @if ($invoice->isPaid())
                            <span style="display:inline-block; background:#dcfce7; color:#15803d; font-size:12px; font-weight:500; padding:2px 10px; border-radius:9999px;">PAID</span>
                            @if ($invoice->payments->isNotEmpty())
                                <span style="font-size:12px; color:#6b7280; margin-left:8px;">{{ __('invoice.paid') }} on {{ format_date($invoice->payments->last()->paid_at) }}</span>
                            @endif
                        @elseif ($invoice->isPartiallyPaid())
                            <span style="display:inline-block; background:#fef3c7; color:#b45309; font-size:12px; font-weight:500; padding:2px 10px; border-radius:9999px;">PARTIALLY PAID</span>
                            <span style="font-size:12px; color:#6b7280; margin-left:8px;">Paid {{ format_money($invoice->paidAmount(), $invoice->currency) }} of {{ format_money($invoice->total, $invoice->currency) }}</span>
                        @else
                            <span style="display:inline-block; background:#fef3c7; color:#b45309; font-size:12px; font-weight:500; padding:2px 10px; border-radius:9999px;">UNPAID</span>
                        @endif
                    </div>
                </td>
            </tr>
        </table>

        <table class="parties" style="width:100%;">
            <tr>
                <td style="width:50%; vertical-align: top;">
                    <p class="col-label">{{ __('invoice.from') }}</p>
                    <p class="company">{{ config('app.owner.company') }}</p>
                    <p class="line">{{ config('app.owner.name') }}</p>
                    <p class="line">{{ config('app.owner.address') }}</p>
                    <p class="line">{{ config('app.owner.phone') }}</p>
                    <p class="line">{{ config('app.owner.email') }}</p>
                </td>
                <td style="width:50%; vertical-align: top;">
                    <p class="col-label">{{ __('invoice.bill_to') }}</p>
                    <p class="company">{{ $invoice->project->client_company ?: $invoice->project->client_name }}</p>
                    <p class="line">{{ $invoice->project->client_name }}</p>
                    @if ($invoice->project->client_address)
                        <p class="line">{{ $invoice->project->client_address }}</p>
                    @endif
                    <p class="line">{{ $invoice->project->client_email }}</p>
                </td>
            </tr>
        </table>

        <div class="items-wrap">
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
                            <td class="desc">{{ $item->description }}</td>
                            <td class="num">{{ $item->quantity }}</td>
                            <td class="num">{{ format_money($item->unit_price, $invoice->currency) }}</td>
                            <td class="amt num">{{ format_money($item->amount, $invoice->currency) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <table class="totals">
                <tr class="row">
                    <td class="k">{{ __('invoice.subtotal') }}</td>
                    <td class="v">{{ format_money($invoice->subtotal, $invoice->currency) }}</td>
                </tr>
                <tr class="row">
                    <td class="k">{{ __('invoice.tax') }}</td>
                    <td class="v">{{ format_money($invoice->tax, $invoice->currency) }}</td>
                </tr>
                <tr class="row total">
                    <td class="k">{{ __('invoice.total') }}</td>
                    <td class="v">{{ format_money($invoice->total, $invoice->currency) }}</td>
                </tr>
            </table>
        </div>

        @if ($invoice->notes)
            <div class="box">
                <p class="box-label">{{ __('invoice.notes') }}</p>
                <p class="box-body">{{ $invoice->notes }}</p>
            </div>
        @endif

        @if ($invoice->payment_terms)
            <div class="box">
                <p class="box-label">{{ __('invoice.payment_information') }}</p>
                <p class="box-body">{{ $invoice->payment_terms }}</p>
            </div>
        @endif

        <p class="thanks">{{ __('invoice.thank_you') }}</p>
    </div>
@endsection