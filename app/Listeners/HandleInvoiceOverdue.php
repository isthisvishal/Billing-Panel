<?php

namespace App\Listeners;

use App\Events\InvoiceOverdue;
use App\Events\InvoiceGraceWarning;
use App\Models\Audit;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class HandleInvoiceOverdue implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(InvoiceOverdue $event)
    {
        $invoice = $event->invoice;

        Audit::log($invoice->id, 'invoice.overdue', []);

        // Emit a grace warning event so different listeners can handle notifications / UI
        InvoiceGraceWarning::dispatch($invoice);

        Log::info('HandleInvoiceOverdue dispatched InvoiceGraceWarning', ['invoice_id' => $invoice->id]);
    }
}
