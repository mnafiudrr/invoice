<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function markAsPaid(Invoice $invoice, array $data): Payment
    {
        abort_if($invoice->isPaid(), 422, 'Invoice is already marked as paid.');

        return DB::transaction(function () use ($invoice, $data) {
            $payment = Payment::create([
                'invoice_id' => $invoice->id,
                'amount' => $data['amount'] ?? $invoice->total,
                'paid_at' => $data['paid_at'],
                'method' => $data['method'],
                'reference' => $data['reference'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            $invoice->update(['status' => Invoice::STATUS_PAID]);

            return $payment;
        });
    }
}
