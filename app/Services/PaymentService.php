<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PaymentService
{
    public function recordPayment(Invoice $invoice, array $data): Payment
    {
        abort_if($invoice->isCancelled(), 422, 'Cannot record a payment on a cancelled invoice.');

        $amount = (float) ($data['amount'] ?? 0);
        $remaining = $invoice->remainingAmount();

        if ($amount <= 0) {
            throw ValidationException::withMessages(['amount' => 'The payment amount must be greater than zero.']);
        }

        if ($amount > $remaining) {
            throw ValidationException::withMessages([
                'amount' => 'The payment amount exceeds the remaining balance of '.number_format($remaining, 2, ',', '.').'.',
            ]);
        }

        return DB::transaction(function () use ($invoice, $data, $amount) {
            $payment = Payment::create([
                'invoice_id' => $invoice->id,
                'amount' => $amount,
                'paid_at' => $data['paid_at'],
                'method' => $data['method'],
                'reference' => $data['reference'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            $this->recomputeStatus($invoice);

            return $payment;
        });
    }

    public function deletePayment(Invoice $invoice, Payment $payment): void
    {
        DB::transaction(function () use ($invoice, $payment) {
            $payment->delete();
            $this->recomputeStatus($invoice);
        });
    }

    /**
     * Derive the payment status from the sum of recorded payments.
     */
    public function recomputeStatus(Invoice $invoice): Invoice
    {
        if ($invoice->isCancelled()) {
            return $invoice;
        }

        $paid = $invoice->paidAmount();
        $total = (float) $invoice->total;

        $status = match (true) {
            $paid >= $total => Invoice::STATUS_PAID,
            $paid > 0 => Invoice::STATUS_PARTIALLY_PAID,
            default => $invoice->status === Invoice::STATUS_DRAFT ? Invoice::STATUS_DRAFT : Invoice::STATUS_SENT,
        };

        if ($invoice->status !== $status) {
            $invoice->update(['status' => $status]);
        }

        return $invoice->refresh();
    }
}
