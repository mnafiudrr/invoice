@extends('layouts.admin')

@section('title', 'Edit Invoice')

@section('content')
    <h1 class="text-2xl font-semibold text-gray-900">Edit Invoice</h1>

    <form method="POST" action="{{ route('admin.invoices.update', $invoice) }}" class="mt-6 max-w-3xl space-y-6"
          x-data="invoiceForm(@js($invoice->items->map(fn ($item) => [
              'description' => $item->description,
              'quantity' => (float) $item->quantity,
              'unit_price' => (float) $item->unit_price,
          ])->values()))">
        @csrf
        @method('PUT')

        <div class="rounded-lg bg-white p-6 shadow">
            <h2 class="text-lg font-semibold">Invoice</h2>
            <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="project_id" class="block text-sm font-medium text-gray-700">Project</label>
                    <select id="project_id" name="project_id" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @foreach ($projects as $project)
                            <option value="{{ $project->id }}" @selected(old('project_id', $invoice->project_id) == $project->id)>{{ $project->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="invoice_number" class="block text-sm font-medium text-gray-700">Invoice number</label>
                    <input type="text" id="invoice_number" name="invoice_number" value="{{ old('invoice_number', $invoice->invoice_number) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="language" class="block text-sm font-medium text-gray-700">Language</label>
                    <select id="language" name="language" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="en" @selected(old('language', $invoice->language) === 'en')>English</option>
                        <option value="id" @selected(old('language', $invoice->language) === 'id')>Bahasa Indonesia</option>
                    </select>
                </div>
                <div>
                    <label for="currency" class="block text-sm font-medium text-gray-700">Currency</label>
                    <input type="text" id="currency" name="currency" value="{{ old('currency', $invoice->currency) }}" maxlength="3"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="issued_at" class="block text-sm font-medium text-gray-700">Invoice date</label>
                    <input type="date" id="issued_at" name="issued_at" value="{{ old('issued_at', $invoice->issued_at?->format('Y-m-d')) }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="due_at" class="block text-sm font-medium text-gray-700">Due date</label>
                    <input type="date" id="due_at" name="due_at" value="{{ old('due_at', $invoice->due_at?->format('Y-m-d')) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>
        </div>

        <div class="rounded-lg bg-white p-6 shadow">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold">Items</h2>
                <button type="button" @click="addItem()"
                        class="rounded-md border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Add Item
                </button>
            </div>

            <template x-for="(item, index) in items" :key="index">
                <div class="mt-4 grid grid-cols-12 gap-3">
                    <div class="col-span-12 sm:col-span-5">
                        <input type="text" :name="`items[${index}][description]`" x-model="item.description"
                               placeholder="Description"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div class="col-span-4 sm:col-span-2">
                        <input type="number" step="0.01" min="0" :name="`items[${index}][quantity]`" x-model="item.quantity"
                               placeholder="Qty" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div class="col-span-4 sm:col-span-2">
                        <input type="number" step="0.01" min="0" :name="`items[${index}][unit_price]`" x-model="item.unit_price"
                               placeholder="Unit price" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div class="col-span-3 sm:col-span-2 flex items-center justify-end text-sm text-gray-700"
                         x-text="formatMoney(item.quantity * item.unit_price)">
                    </div>
                    <div class="col-span-1 flex items-center justify-end">
                        <button type="button" @click="removeItem(index)" class="text-red-500 hover:text-red-700">×</button>
                    </div>
                </div>
            </template>

            <p x-show="items.length === 0" class="mt-4 text-sm text-gray-500">No items yet. Add at least one.</p>
        </div>

        <div class="rounded-lg bg-white p-6 shadow">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div>
                    <label for="tax" class="block text-sm font-medium text-gray-700">Tax</label>
                    <input type="number" step="0.01" min="0" id="tax" name="tax" value="{{ old('tax', $invoice->tax) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div class="sm:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                    <textarea id="notes" name="notes" rows="2"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes', $invoice->notes) }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit"
                    class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                Update Invoice
            </button>
            <a href="{{ route('admin.invoices.show', $invoice) }}" class="text-sm text-gray-600 hover:text-gray-900">Cancel</a>
        </div>
    </form>
@endsection