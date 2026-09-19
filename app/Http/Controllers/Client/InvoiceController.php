<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\File;
use App\Models\Invoice;
use App\Models\Project;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InvoiceController extends Controller
{
    public function show(Project $project, Invoice $invoice): StreamedResponse
    {
        abort_if($invoice->project_id !== $project->id, 404);

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

    public function file(Project $project, Invoice $invoice, File $file): StreamedResponse
    {
        abort_if($invoice->project_id !== $project->id, 404);
        abort_if($file->invoice_id !== $invoice->id, 404);

        return Storage::disk('private')->response($file->path, $file->original_filename);
    }
}
