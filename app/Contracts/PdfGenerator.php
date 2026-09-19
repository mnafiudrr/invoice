<?php

namespace App\Contracts;

use App\Models\Invoice;

interface PdfGenerator
{
    /**
     * Render an invoice to PDF and return the generated file contents as a string.
     */
    public function generate(Invoice $invoice): string;
}
