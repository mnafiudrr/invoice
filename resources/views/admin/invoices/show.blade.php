@extends('layouts.admin')

@section('title', $invoice->invoice_number)

@section('content')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">{{ $invoice->invoice_number }}</h1>
            <p class="mt-1 text-sm text-gray-600">
                {{ $invoice->project->name }} &middot; <x-status-badge :status="$invoice->status" />
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.invoices.preview', $invoice) }}" target="_blank"
               class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                Preview
            </a>
            <form method="POST" action="{{ route('admin.invoices.generate-pdf', $invoice) }}">
                @csrf
                <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                    Generate PDF
                </button>
            </form>
            @if ($invoice->files->where('type', 'invoice')->isNotEmpty())
                <a href="{{ route('admin.invoices.download-pdf', $invoice) }}"
                   class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    View PDF
                </a>
            @endif
            <a href="{{ route('admin.invoices.edit', $invoice) }}"
               class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                Edit
            </a>
            <form method="POST" action="{{ route('admin.invoices.destroy', $invoice) }}"
                  onsubmit="return confirm('Delete this invoice?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="rounded-md border border-red-300 bg-white px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50">
                    Delete
                </button>
            </form>
        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-6">
            <div class="rounded-lg bg-white p-6 shadow">
                <h2 class="text-lg font-semibold">Items</h2>
                <table class="mt-4 min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Description</th>
                            <th class="px-4 py-2 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Qty</th>
                            <th class="px-4 py-2 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Unit Price</th>
                            <th class="px-4 py-2 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($invoice->items as $item)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $item->description }}</td>
                                <td class="px-4 py-3 text-right text-sm text-gray-600">{{ $item->quantity }}</td>
                                <td class="px-4 py-3 text-right text-sm text-gray-600">{{ format_money($item->unit_price, $invoice->currency) }}</td>
                                <td class="px-4 py-3 text-right text-sm text-gray-900">{{ format_money($item->amount, $invoice->currency) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

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
            </div>

            <div class="rounded-lg bg-white p-6 shadow">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold">Payments</h2>
                    @if (! $invoice->isPaid())
                        <button type="button" x-data @click="$refs.payForm.classList.toggle('hidden')"
                                class="rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700">
                            Mark as Paid
                        </button>
                    @endif
                </div>

                <form method="POST" action="{{ route('admin.invoices.mark-paid', $invoice) }}" x-ref="payForm" class="mt-4 hidden space-y-4 rounded border border-green-200 bg-green-50 p-4">
                    @csrf
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Amount</label>
                            <input type="number" step="0.01" min="0" name="amount" value="{{ $invoice->total }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Paid date</label>
                            <input type="date" name="paid_at" value="{{ now()->format('Y-m-d') }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Method</label>
                            <input type="text" name="method" placeholder="Bank transfer" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Reference</label>
                            <input type="text" name="reference"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Notes</label>
                        <textarea name="notes" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"></textarea>
                    </div>
                    <button type="submit" class="rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700">
                        Confirm Payment
                    </button>
                </form>

                @forelse ($invoice->payments as $payment)
                    <div class="mt-4 flex items-center justify-between rounded border p-4">
                        <div>
                            <p class="font-medium">{{ format_money($payment->amount, $invoice->currency) }}</p>
                            <p class="text-sm text-gray-500">{{ $payment->method }} &middot; {{ format_date($payment->paid_at) }}</p>
                        </div>
                        <p class="text-sm text-gray-500">{{ $payment->reference }}</p>
                    </div>
                @empty
                    <p class="mt-4 text-sm text-gray-500">No payments recorded.</p>
                @endforelse
            </div>

            <div class="rounded-lg bg-white p-6 shadow">
                <h2 class="text-lg font-semibold">Documents</h2>

                <form method="POST" action="{{ route('admin.invoices.files.store', $invoice) }}" enctype="multipart/form-data"
                      class="mt-4 flex flex-wrap items-end gap-3 rounded border border-gray-200 p-4">
                    @csrf
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-sm font-medium text-gray-700">Type</label>
                        <select name="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="payment_receipt">Payment receipt</option>
                            <option value="payment_proof">Payment proof</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-sm font-medium text-gray-700">File</label>
                        <input type="file" name="file" required class="mt-1 block w-full text-sm text-gray-700">
                    </div>
                    <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                        Upload
                    </button>
                </form>

                @forelse ($invoice->files as $file)
                    <div class="mt-3 flex items-center justify-between rounded border p-3">
                        <div class="flex items-center gap-3">
                            <span class="rounded bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600">{{ $file->type }}</span>
                            <span class="text-sm text-gray-800">{{ $file->original_filename }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-xs text-gray-400">{{ round($file->size / 1024) }} KB</span>
                            <form method="POST" action="{{ route('admin.invoices.files.destroy', [$invoice, $file]) }}"
                                  onsubmit="return confirm('Delete this document?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm text-red-600 hover:text-red-800">Delete</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="mt-4 text-sm text-gray-500">No documents attached.</p>
                @endforelse
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-lg bg-white p-6 shadow">
                <h2 class="text-sm font-semibold text-gray-900">Details</h2>
                <dl class="mt-3 space-y-2 text-sm">
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
                </dl>
            </div>
        </div>
    </div>
@endsection