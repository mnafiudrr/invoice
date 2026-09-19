<?php

namespace App\Services;

use App\Contracts\PdfGenerator;
use App\Models\File;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InvoiceService
{
    public function __construct(private PdfGenerator $pdfGenerator) {}

    public function create(array $data): Invoice
    {
        $items = $this->normalizeItems($data['items'] ?? []);

        $subtotal = $this->calculateSubtotal($items);
        $tax = $data['tax'] ?? 0;
        $total = $subtotal + $tax;

        return DB::transaction(function () use ($data, $items, $subtotal, $tax, $total) {
            $invoice = Invoice::create([
                'project_id' => $data['project_id'],
                'invoice_number' => $data['invoice_number'] ?? $this->generateNumber(),
                'language' => $data['language'],
                'currency' => $data['currency'],
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'issued_at' => $data['issued_at'],
                'due_at' => $data['due_at'] ?? null,
                'status' => $data['status'] ?? Invoice::STATUS_DRAFT,
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($items as $item) {
                $invoice->items()->create($item);
            }

            return $invoice;
        });
    }

    public function update(Invoice $invoice, array $data): Invoice
    {
        $items = $this->normalizeItems($data['items'] ?? []);

        $subtotal = $this->calculateSubtotal($items);
        $tax = $data['tax'] ?? 0;
        $total = $subtotal + $tax;

        return DB::transaction(function () use ($invoice, $data, $items, $subtotal, $tax, $total) {
            $invoice->update([
                'project_id' => $data['project_id'],
                'invoice_number' => $data['invoice_number'],
                'language' => $data['language'],
                'currency' => $data['currency'],
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'issued_at' => $data['issued_at'],
                'due_at' => $data['due_at'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            $invoice->items()->delete();

            foreach ($items as $item) {
                $invoice->items()->create($item);
            }

            return $invoice->refresh();
        });
    }

    public function changeStatus(Invoice $invoice, string $status): Invoice
    {
        $invoice->update(['status' => $status]);

        return $invoice->refresh();
    }

    public function delete(Invoice $invoice): void
    {
        $invoice->delete();
    }

    public function generatePdf(Invoice $invoice): File
    {
        return DB::transaction(function () use ($invoice) {
            $filename = Str::uuid()->toString().'.pdf';
            $path = 'invoices/'.now()->year.'/'.$filename;

            Storage::disk('private')->put($path, $this->pdfGenerator->generate($invoice));

            return File::updateOrCreate(
                ['invoice_id' => $invoice->id, 'type' => File::TYPE_INVOICE],
                [
                    'project_id' => $invoice->project_id,
                    'path' => $path,
                    'original_filename' => $invoice->invoice_number.'.pdf',
                    'mime_type' => 'application/pdf',
                    'size' => Storage::disk('private')->size($path),
                ]
            );
        });
    }

    public function streamPdf(Invoice $invoice): StreamedResponse
    {
        $file = $invoice->files()
            ->where('type', File::TYPE_INVOICE)
            ->latest()
            ->first();

        abort_if($file === null, 404, 'No PDF generated for this invoice yet.');

        return Storage::disk('private')->response($file->path, $file->original_filename, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$file->original_filename.'"',
        ]);
    }

    public function generateNumber(): string
    {
        $year = now()->year;

        $lastNumber = Invoice::query()
            ->where('invoice_number', 'like', "INV-{$year}-%")
            ->orderByDesc('invoice_number')
            ->value('invoice_number');

        $seq = 1;

        if ($lastNumber !== null) {
            $seq = (int) substr($lastNumber, strrpos($lastNumber, '-') + 1) + 1;
        }

        return sprintf('INV-%d-%03d', $year, $seq);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function normalizeItems(array $items): array
    {
        return array_map(function (array $item) {
            $quantity = (float) ($item['quantity'] ?? 1);
            $unitPrice = (float) ($item['unit_price'] ?? 0);

            return [
                'description' => $item['description'],
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'amount' => round($quantity * $unitPrice, 2),
            ];
        }, $items);
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     */
    private function calculateSubtotal(array $items): float
    {
        return round(array_sum(array_column($items, 'amount')), 2);
    }
}
