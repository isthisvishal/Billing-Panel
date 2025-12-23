<?php

namespace App\Observers;

use App\Models\Invoice;
use App\Events\InvoicePaid;
use App\Events\InvoiceOverdue;
use App\Events\InvoiceCancelled;
use App\Events\InvoiceExpired;
use Illuminate\Support\Facades\Log;

class InvoiceObserver
{
    public function updated(Invoice $invoice)
    {
        // detect status changes
        $original = $invoice->getOriginal('status');
        $current = $invoice->status;

        if ($original === $current) {
            return;
        }

        Log::info('InvoiceObserver status changed', ['invoice_id' => $invoice->id, 'from' => $original, 'to' => $current]);

        switch ($current) {
            case 'paid':
                InvoicePaid::dispatch($invoice);
                break;
            case 'overdue':
                InvoiceOverdue::dispatch($invoice);
                break;
            case 'cancelled':
                InvoiceCancelled::dispatch($invoice);
                break;
            case 'expired':
                InvoiceExpired::dispatch($invoice);
                break;
        }

        // update last_status_at
        $invoice->last_status_at = now();
        $invoice->saveQuietly();
    }
}
