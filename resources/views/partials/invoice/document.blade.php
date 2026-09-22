{{-- Full invoice document — shared by the client share page and the admin preview. --}}
@props(['invoice'])

<div class="print-area rounded-lg border border-gray-200 bg-white p-6 shadow-sm sm:p-8">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wider text-gray-400">{{ __('invoice.invoice') }}</p>
            <h1 class="mt-1 text-2xl font-semibold text-gray-900">{{ $invoice->invoice_number }}</h1>
            <p class="mt-1 text-sm text-gray-500">{{ __('invoice.issued_date') }}: {{ format_date($invoice->issued_at) }}</p>
            @if ($invoice->due_at)
                <p class="mt-1 text-sm text-gray-500">{{ __('invoice.due_date') }}: {{ format_date($invoice->due_at) }}</p>
            @endif
        </div>
        <div class="text-right">
            <p class="text-lg font-semibold text-gray-900">{{ format_money($invoice->total, $invoice->currency) }}</p>
            <div class="mt-1 flex items-center justify-end gap-2">
                @if ($invoice->isPaid())
                    <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700">PAID</span>
                    @if ($invoice->payments->isNotEmpty())
                        <span class="text-xs text-gray-500">{{ __('invoice.paid') }} on {{ format_date($invoice->payments->last()->paid_at) }}</span>
                    @endif
                @elseif ($invoice->isPartiallyPaid())
                    <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-medium text-amber-700">PARTIALLY PAID</span>
                    <span class="text-xs text-gray-500">Paid {{ format_money($invoice->paidAmount(), $invoice->currency) }} of {{ format_money($invoice->total, $invoice->currency) }}</span>
                @else
                    <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-medium text-amber-700">UNPAID</span>
                @endif
            </div>
        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 border-t border-gray-100 pt-6 sm:grid-cols-2">
        <div>
            <p class="text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('invoice.from') }}</p>
            <p class="mt-2 text-sm font-semibold text-gray-900">{{ config('app.owner.company') }}</p>
            <p class="mt-1 text-sm text-gray-600">{{ config('app.owner.name') }}</p>
            <p class="mt-1 text-sm text-gray-600">{{ config('app.owner.address') }}</p>
            <p class="mt-1 text-sm text-gray-600">{{ config('app.owner.phone') }}</p>
            <p class="mt-1 text-sm text-gray-600">{{ config('app.owner.email') }}</p>
        </div>
        <div>
            <p class="text-xs font-medium uppercase tracking-wider text-gray-400">{{ __('invoice.bill_to') }}</p>
            <p class="mt-2 text-sm font-semibold text-gray-900">{{ $invoice->project->client_company ?: $invoice->project->client_name }}</p>
            <p class="mt-1 text-sm text-gray-600">{{ $invoice->project->client_name }}</p>
            @if ($invoice->project->client_address)
                <p class="mt-1 text-sm text-gray-600">{{ $invoice->project->client_address }}</p>
            @endif
            <p class="mt-1 text-sm text-gray-600">{{ $invoice->project->client_email }}</p>
        </div>
    </div>

    <div class="mt-6 border-t border-gray-100 pt-6">
        <x-table responsive>
            <x-slot:head>
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('invoice.description') }}</th>
                    <th class="px-4 py-2 text-right text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('invoice.quantity') }}</th>
                    <th class="px-4 py-2 text-right text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('invoice.unit_price') }}</th>
                    <th class="px-4 py-2 text-right text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('invoice.amount') }}</th>
                </tr>
            </x-slot:head>
            @foreach ($invoice->items as $item)
                <tr class="table-responsive-row">
                    <td data-label="{{ __('invoice.description') }}" class="px-4 py-3 text-sm text-gray-900">{{ $item->description }}</td>
                    <td data-label="{{ __('invoice.quantity') }}" class="px-4 py-3 text-right text-sm text-gray-600">{{ $item->quantity }}</td>
                    <td data-label="{{ __('invoice.unit_price') }}" class="px-4 py-3 text-right text-sm text-gray-600">{{ format_money($item->unit_price, $invoice->currency) }}</td>
                    <td data-label="{{ __('invoice.amount') }}" class="px-4 py-3 text-right text-sm text-gray-900">{{ format_money($item->amount, $invoice->currency) }}</td>
                </tr>
            @endforeach
        </x-table>

        <div class="mt-4 flex justify-end">
            <dl class="w-full max-w-xs space-y-2 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-500">{{ __('invoice.subtotal') }}</dt>
                    <dd class="font-medium">{{ format_money($invoice->subtotal, $invoice->currency) }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">{{ __('invoice.tax') }}</dt>
                    <dd class="font-medium">{{ format_money($invoice->tax, $invoice->currency) }}</dd>
                </div>
                <div class="flex justify-between border-t pt-2">
                    <dt class="font-semibold">{{ __('invoice.total') }}</dt>
                    <dd class="font-semibold">{{ format_money($invoice->total, $invoice->currency) }}</dd>
                </div>
            </dl>
        </div>
    </div>

    @if ($invoice->notes)
        <div class="mt-6 rounded bg-gray-50 p-4">
            <p class="text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('invoice.notes') }}</p>
            <p class="mt-1 text-sm whitespace-pre-line text-gray-700">{{ $invoice->notes }}</p>
        </div>
    @endif

    @if ($invoice->payment_terms)
        <div class="mt-6 rounded bg-gray-50 p-4">
            <p class="text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('invoice.payment_information') }}</p>
            <p class="mt-1 text-sm whitespace-pre-line text-gray-700">{{ $invoice->payment_terms }}</p>
        </div>
    @endif

    <p class="mt-6 text-sm text-gray-400">{{ __('invoice.thank_you') }}</p>
</div>