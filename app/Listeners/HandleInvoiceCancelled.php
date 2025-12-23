<?php

namespace App\Listeners;

use App\Events\InvoiceCancelled;
use App\Jobs\TerminateServiceJob;
use App\Models\Audit;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class HandleInvoiceCancelled implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(InvoiceCancelled $event)
    {
        $invoice = $event->invoice;

        Audit::log($invoice->id, 'invoice.cancelled', []);

        TerminateServiceJob::dispatch($invoice);

        Log::info('HandleInvoiceCancelled dispatched TerminateServiceJob', ['invoice_id' => $invoice->id]);
    }
}
