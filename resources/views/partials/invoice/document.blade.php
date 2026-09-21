{{-- Full invoice document — shared by the client share page and the admin preview. --}}
@props(['invoice'])

<div class="print-area rounded-lg border border-gray-200 bg-white p-6 shadow-sm sm:p-8">
    <div class="h-0.5 w-full bg-black"></div>

    <div class="mt-6 flex flex-wrap items-start justify-between gap-6">
        <div>
            <p class="text-lg font-semibold text-gray-900">{{ config('app.owner.company') }}</p>
            <div class="mt-1 space-y-0.5 text-sm text-gray-600">
                <p>{{ config('app.owner.name') }}</p>
                <p>{{ config('app.owner.address') }}</p>
                <p>{{ config('app.owner.phone') }}</p>
                <p>{{ config('app.owner.email') }}</p>
            </div>
        </div>
        <div class="text-right">
            <p class="text-sm font-semibold text-gray-900">{{ __('invoice.bill_to') }}</p>
            <div class="mt-1 space-y-0.5 text-sm text-gray-600">
                <p>{{ $invoice->project->client_company ?: $invoice->project->client_name }}</p>
                <p>{{ $invoice->project->client_name }}</p>
                @if ($invoice->project->client_address)
                    <p>{{ $invoice->project->client_address }}</p>
                @endif
                <p>{{ $invoice->project->client_email }}</p>
            </div>
        </div>
    </div>

    <div class="mt-8 flex flex-wrap justify-end gap-8 text-sm">
        <div class="text-right">
            <p class="font-semibold text-gray-900">{{ __('invoice.invoice') }}</p>
            <p class="text-gray-600">{{ $invoice->invoice_number }}</p>
        </div>
        <div class="text-right">
            <p class="font-semibold text-gray-900">{{ __('invoice.issued_date') }}</p>
            <p class="text-gray-600">{{ format_date($invoice->issued_at) }}</p>
        </div>
        @if ($invoice->due_at)
            <div class="text-right">
                <p class="font-semibold text-gray-900">{{ __('invoice.due_date') }}</p>
                <p class="text-gray-600">{{ format_date($invoice->due_at) }}</p>
            </div>
        @endif
        <div class="text-right">
            <p class="font-semibold text-gray-900">{{ __('invoice.total') }}</p>
            <p class="text-lg font-semibold text-gray-900">{{ format_money($invoice->total, $invoice->currency) }}</p>
        </div>
    </div>

    <div class="mt-6 overflow-hidden rounded-lg border border-gray-300">
        <x-table responsive>
            <x-slot:head>
                <tr class="bg-gray-100">
                    <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">{{ __('invoice.description') }}</th>
                    <th class="px-4 py-2 text-right text-xs font-semibold uppercase tracking-wider text-gray-600">{{ __('invoice.quantity') }}</th>
                    <th class="px-4 py-2 text-right text-xs font-semibold uppercase tracking-wider text-gray-600">{{ __('invoice.unit_price') }}</th>
                    <th class="px-4 py-2 text-right text-xs font-semibold uppercase tracking-wider text-gray-600">{{ __('invoice.amount') }}</th>
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
    </div>

    <div class="mt-4 flex justify-end">
        <dl class="w-full max-w-xs space-y-2 text-sm">
            <div class="flex justify-between">
                <dt class="font-medium text-gray-900">{{ __('invoice.subtotal') }}</dt>
                <dd class="text-gray-700">{{ format_money($invoice->subtotal, $invoice->currency) }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="font-medium text-gray-900">{{ __('invoice.tax') }}</dt>
                <dd class="text-gray-700">{{ format_money($invoice->tax, $invoice->currency) }}</dd>
            </div>
            <div class="flex justify-between border-t pt-2">
                <dt class="font-semibold text-gray-900">{{ __('invoice.total') }}</dt>
                <dd class="font-semibold text-gray-900">{{ format_money($invoice->total, $invoice->currency) }}</dd>
            </div>
        </dl>
    </div>

    @if ($invoice->notes)
        <div class="mt-6 rounded bg-gray-50 p-4">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('invoice.notes') }}</p>
            <p class="mt-1 text-sm whitespace-pre-line text-gray-700">{{ $invoice->notes }}</p>
        </div>
    @endif

    @if ($invoice->payment_terms)
        <div class="mt-6 rounded bg-gray-50 p-4">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('invoice.payment_information') }}</p>
            <p class="mt-1 text-sm whitespace-pre-line text-gray-700">{{ $invoice->payment_terms }}</p>
        </div>
    @endif

    <div class="mt-6 flex items-center justify-between">
        <p class="text-sm text-gray-400">{{ __('invoice.thank_you') }}</p>
        @if ($invoice->isPaid())
            <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700">PAID</span>
        @else
            <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-medium text-amber-700">UNPAID</span>
        @endif
    </div>
</div>