@extends('layouts.admin')

@section('title', $invoice->invoice_number)

@section('content')
    <x-page-header
        :title="$invoice->invoice_number"
        :subtitle="$invoice->project->name . ' · ' . $invoice->project->client_name">
        <x-slot:actions>
            <form method="POST" action="{{ route('admin.invoices.share', $invoice) }}">
                @csrf
                <x-button type="submit" variant="primary">Share Invoice</x-button>
            </form>
            <x-button href="{{ route('admin.invoices.preview', $invoice) }}" variant="secondary" target="_blank">Preview</x-button>
            <form method="POST" action="{{ route('admin.invoices.generate-pdf', $invoice) }}">
                @csrf
                <x-button type="submit">Generate PDF</x-button>
            </form>
            @if ($invoice->files->where('type', 'invoice')->isNotEmpty())
                <x-button href="{{ route('admin.invoices.download-pdf', $invoice) }}" variant="secondary">View PDF</x-button>
            @endif
            <x-button href="{{ route('admin.invoices.edit', $invoice) }}" variant="secondary">Edit</x-button>
            <x-modal
                :action="route('admin.invoices.destroy', $invoice)"
                method="DELETE"
                title="Delete invoice?"
                message="This invoice and its data will be permanently deleted."
                confirm-label="Delete">
                <x-slot:trigger>
                    <x-button variant="danger" type="button">Delete</x-button>
                </x-slot:trigger>
            </x-modal>
        </x-slot:actions>
    </x-page-header>

    @if (session('share_link'))
        <x-alert type="success" class="mt-6">
            <p class="font-medium">Invoice Share Link</p>
            <p class="mt-1">The password is shown only once.</p>
            <div class="mt-3 space-y-2">
                <x-copy-field :value="route('shares.show', session('share_link'))" />
                <x-copy-field :value="session('share_password')" monospace />
            </div>
        </x-alert>
    @endif

    @if ($invoice->shareLinks->isNotEmpty())
        <x-card title="Active Share Links" class="mt-6">
            <div class="space-y-3">
                @foreach ($invoice->shareLinks as $shareLink)
                    <div class="flex items-center justify-between gap-4 rounded border border-gray-200 p-3">
                        <div class="min-w-0">
                            <code class="block truncate text-sm text-gray-700">{{ route('shares.show', $shareLink) }}</code>
                            <p class="mt-1 text-xs text-gray-500">
                                {{ $shareLink->expires_at ? 'Expires ' . format_date($shareLink->expires_at) : 'No expiration' }}
                            </p>
                        </div>
                        <x-modal
                            :action="route('admin.invoices.share.destroy', [$invoice, $shareLink])"
                            method="DELETE"
                            title="Revoke share link?"
                            message="Clients using this link will no longer be able to access the invoice."
                            confirm-label="Revoke">
                            <x-slot:trigger>
                                <x-button variant="ghost" type="button" size="sm">Revoke</x-button>
                            </x-slot:trigger>
                        </x-modal>
                    </div>
                @endforeach
            </div>
        </x-card>
    @endif

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <x-card title="Items">
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

                @if ($invoice->notes)
                    <div class="mt-6 rounded bg-gray-50 p-4">
                        <p class="text-xs font-medium uppercase tracking-wider text-gray-500">Notes</p>
                        <p class="mt-1 text-sm text-gray-700">{{ $invoice->notes }}</p>
                    </div>
                @endif
            </x-card>

            <x-card title="Payments">
                <x-slot:actions>
                    @if ($invoice->remainingAmount() > 0 && ! $invoice->isCancelled())
                        <button type="button" @click="$refs.payForm.classList.toggle('hidden')"
                                class="rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700">
                            Add Payment
                        </button>
                    @endif
                </x-slot:actions>

                <div class="mb-4 grid grid-cols-3 gap-4 rounded border border-gray-200 bg-gray-50 p-4">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wider text-gray-500">Total</p>
                        <p class="mt-1 font-semibold text-gray-900">{{ format_money($invoice->total, $invoice->currency) }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wider text-gray-500">Paid</p>
                        <p class="mt-1 font-semibold text-green-700">{{ format_money($invoice->paidAmount(), $invoice->currency) }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wider text-gray-500">Remaining</p>
                        <p class="mt-1 font-semibold text-gray-900">{{ format_money($invoice->remainingAmount(), $invoice->currency) }}</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.invoices.payments.store', $invoice) }}" x-ref="payForm" class="hidden space-y-4 rounded border border-green-200 bg-green-50 p-4">
                    @csrf
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <x-input name="amount" type="number" step="0.01" min="0.01" :label="'Amount'" :required="true"
                                 :value="$invoice->remainingAmount()"
                                 hint="Remaining: {{ format_money($invoice->remainingAmount(), $invoice->currency) }}" />
                        <x-input name="paid_at" type="date" :label="'Paid date'" :required="true" :value="now()->format('Y-m-d')" />
                        <x-input name="method" :label="'Method'" :required="true" placeholder="Bank transfer" />
                        <x-input name="reference" :label="'Reference'" />
                    </div>
                    <x-textarea name="notes" :label="'Notes'" rows="2" />
                    <x-button type="submit" class="bg-green-600 hover:bg-green-700 focus-visible:ring-green-500">Record Payment</x-button>
                </form>

                @if ($invoice->payments->isEmpty())
                    <x-empty-state title="No payments recorded" />
                @else
                    <div class="mt-4 space-y-3">
                        @foreach ($invoice->payments as $payment)
                            <div class="flex items-center justify-between gap-4 rounded border border-gray-200 p-4">
                                <div>
                                    <p class="font-medium">{{ format_money($payment->amount, $invoice->currency) }}</p>
                                    <p class="text-sm text-gray-500">{{ $payment->method }} &middot; {{ format_date($payment->paid_at) }}</p>
                                    @if ($payment->reference)
                                        <p class="text-sm text-gray-400">Ref: {{ $payment->reference }}</p>
                                    @endif
                                </div>
                                <x-modal
                                    :action="route('admin.invoices.payments.destroy', [$invoice, $payment])"
                                    method="DELETE"
                                    title="Delete payment?"
                                    message="The payment record will be removed and the invoice status recalculated."
                                    confirm-label="Delete">
                                    <x-slot:trigger>
                                        <x-button variant="ghost" type="button" size="sm">Delete</x-button>
                                    </x-slot:trigger>
                                </x-modal>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-card>

            <x-card title="Documents">
                <form method="POST" action="{{ route('admin.invoices.files.store', $invoice) }}" enctype="multipart/form-data"
                      class="flex flex-wrap items-end gap-3 rounded border border-gray-200 p-4">
                    @csrf
                    <div class="min-w-[200px] flex-1">
                        <x-select name="type" :label="'Type'"
                                  :options="['payment_receipt' => 'Payment receipt', 'payment_proof' => 'Payment proof', 'other' => 'Other']" />
                    </div>
                    <div class="min-w-[200px] flex-1">
                        <label class="block text-sm font-medium text-gray-700">File</label>
                        <input type="file" name="file" required class="mt-1 block w-full text-sm text-gray-700">
                    </div>
                    <x-button type="submit">Upload</x-button>
                </form>

                @if ($invoice->files->isEmpty())
                    <div class="mt-4"><x-empty-state title="No documents attached" /></div>
                @else
                    <div class="mt-3 space-y-3">
                        @foreach ($invoice->files as $file)
                            <div class="flex items-center justify-between gap-4 rounded border border-gray-200 p-3">
                                <div class="flex items-center gap-3">
                                    <span class="rounded bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600">{{ $file->type }}</span>
                                    <span class="text-sm text-gray-800">{{ $file->original_filename }}</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="text-xs text-gray-400">{{ round($file->size / 1024) }} KB</span>
                                    <x-modal
                                        :action="route('admin.invoices.files.destroy', [$invoice, $file])"
                                        method="DELETE"
                                        title="Delete document?"
                                        message="The file will be permanently removed."
                                        confirm-label="Delete">
                                        <x-slot:trigger>
                                            <x-button variant="ghost" type="button" size="sm">Delete</x-button>
                                        </x-slot:trigger>
                                    </x-modal>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-card>
        </div>

        <div class="space-y-6">
            <x-card title="Details">
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Issued</dt>
                        <dd>{{ format_date($invoice->issued_at) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Due</dt>
                        <dd>{{ format_date($invoice->due_at) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Language</dt>
                        <dd>{{ strtoupper($invoice->language) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Currency</dt>
                        <dd>{{ $invoice->currency }}</dd>
                    </div>
                    <div class="flex justify-between items-center">
                        <dt class="text-gray-500">Status</dt>
                        <dd><x-status-badge :status="$invoice->status" /></dd>
                    </div>
                </dl>
            </x-card>

            @if (! $invoice->isPaid() && ! $invoice->isCancelled())
                <x-card title="Change Status">
                    <form method="POST" action="{{ route('admin.invoices.status', $invoice) }}" class="flex items-center gap-2">
                        @csrf
                        <x-select name="status" :options="$invoice::$manualStatuses" :value="$invoice->status" />
                        <x-button type="submit">Update</x-button>
                    </form>
                    <p class="mt-3 text-xs text-gray-500">Paid / partially paid are set automatically from recorded payments.</p>
                </x-card>
            @endif
        </div>
    </div>
@endsection