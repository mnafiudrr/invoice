<?php

namespace App\Services;

use App\Models\File;
use App\Models\Invoice;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileService
{
    /**
     * @param  array<string, mixed>  $options
     */
    public function storeForInvoice(Invoice $invoice, UploadedFile $uploaded, string $type, array $options = []): File
    {
        $directory = match ($type) {
            File::TYPE_PAYMENT_RECEIPT => 'receipts',
            File::TYPE_PAYMENT_PROOF => 'proofs',
            default => 'other',
        };

        $filename = Str::uuid()->toString().'.'.$uploaded->getClientOriginalExtension();
        $path = $directory.'/'.now()->year.'/'.$filename;

        Storage::disk('private')->putFileAs(
            dirname($path),
            $uploaded,
            basename($path),
        );

        return File::create([
            'project_id' => $invoice->project_id,
            'invoice_id' => $invoice->id,
            'type' => $type,
            'path' => $path,
            'original_filename' => $uploaded->getClientOriginalName(),
            'mime_type' => $uploaded->getMimeType(),
            'size' => $uploaded->getSize(),
        ] + $options);
    }

    public function delete(File $file): void
    {
        Storage::disk('private')->delete($file->path);
        $file->delete();
    }
}
