<?php

namespace App\Services;

use App\Contracts\PdfGenerator;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;

class DompdfPdfGenerator implements PdfGenerator
{
    public function generate(Invoice $invoice): string
    {
        $previousLocale = app()->getLocale();
        app()->setLocale($invoice->language);

        try {
            $pdf = Pdf::loadView('pdf.invoice', [
                'invoice' => $invoice->load('project', 'items'),
            ]);

            return $pdf->output();
        } finally {
            app()->setLocale($previousLocale);
        }
    }
}
