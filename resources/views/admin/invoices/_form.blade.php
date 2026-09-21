@php
    $invoice = $invoice ?? null;
    $project = $project ?? ($invoice?->project ?? null);
    $items = old('items', $invoice?->items?->map(fn ($item) => [
        'description' => $item->description,
        'quantity' => (float) $item->quantity,
        'unit_price' => (float) $item->unit_price,
    ])->values()->all() ?? []);
    $initialItems = $items ?: [['quantity' => 1, 'unit_price' => 0]];
@endphp

<div class="space-y-6" x-data="invoiceForm(@js($initialItems))">
    <x-card title="Invoice">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="sm:col-span-2">
                @if ($project)
                    <input type="hidden" name="project_id" value="{{ $project->id }}">
                    <x-input :label="'Project'" :value="$project->name" disabled />
                @else
                    <x-select name="project_id" :label="'Project'" :required="true"
                              :options="$projects->pluck('name', 'id')->all()"
                              placeholder="Select project"
                              :value="old('project_id', $invoice?->project_id)" />
                @endif
            </div>
            <x-input name="invoice_number" :label="'Invoice number'"
                     :hint="$invoice ? null : 'INV-2026-001 (auto if blank)'"
                     :value="old('invoice_number', $invoice?->invoice_number)" />
            <x-select name="language" :label="'Language'" :required="true"
                      :options="['en' => 'English', 'id' => 'Bahasa Indonesia']"
                      :value="old('language', $invoice?->language ?? 'en')" />
            <x-input name="currency" :label="'Currency'" :required="true" maxlength="3"
                     :value="old('currency', $invoice?->currency ?? 'IDR')" />
            <x-input name="issued_at" type="date" :label="'Invoice date'" :required="true"
                     :value="old('issued_at', $invoice?->issued_at?->format('Y-m-d') ?? now()->format('Y-m-d'))" />
            <x-input name="due_at" type="date" :label="'Due date'"
                     :value="old('due_at', $invoice?->due_at?->format('Y-m-d') ?? now()->addDays(7)->format('Y-m-d'))" />
        </div>
    </x-card>

    <x-card title="Items">
        <x-slot:actions>
            <x-button type="button" variant="secondary" size="sm" @click="addItem()">Add Item</x-button>
        </x-slot:actions>

        <div class="space-y-3">
            <template x-for="(item, index) in items" :key="index">
                <div class="grid grid-cols-12 items-center gap-3">
                    <div class="col-span-12 sm:col-span-5">
                        <input type="text" :name="`items[${index}][description]`" x-model="item.description"
                               placeholder="Description" class="field-base">
                    </div>
                    <div class="col-span-4 sm:col-span-2">
                        <input type="number" step="0.01" min="0" :name="`items[${index}][quantity]`" x-model="item.quantity"
                               placeholder="Qty" class="field-base">
                    </div>
                    <div class="col-span-4 sm:col-span-2">
                        <input type="number" step="0.01" min="0" :name="`items[${index}][unit_price]`" x-model="item.unit_price"
                               placeholder="Unit price" class="field-base">
                    </div>
                    <div class="col-span-3 sm:col-span-2 text-right text-sm text-gray-700"
                         x-text="formatMoney(item.quantity * item.unit_price)"></div>
                    <div class="col-span-1 text-right">
                        <button type="button" @click="removeItem(index)" class="text-red-500 hover:text-red-700" aria-label="Remove item">
                            &times;
                        </button>
                    </div>
                </div>
            </template>
            <p x-show="items.length === 0" class="text-sm text-gray-500">No items yet. Add at least one.</p>
        </div>
    </x-card>

    <x-card title="Tax, Notes & Payment">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <x-input name="tax" type="number" step="0.01" min="0" :label="'Tax'"
                     :value="old('tax', $invoice?->tax ?? 0)" />
            <div class="sm:col-span-2">
                <x-textarea name="notes" :label="'Notes'" rows="2"
                            :value="old('notes', $invoice?->notes)" />
            </div>
            <div class="sm:col-span-3">
                <x-textarea name="payment_terms" :label="'Payment Terms'" rows="3"
                            hint="e.g. Down payment 50% (Rp ...); Bank Transfer: BCA ..."
                            :value="old('payment_terms', $invoice?->payment_terms)" />
            </div>
        </div>
    </x-card>
</div>