<?php

namespace App\Listeners;

use App\Events\InvoiceExpired;
use App\Jobs\TerminateServiceJob;
use App\Models\Audit;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class HandleInvoiceExpired implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(InvoiceExpired $event)
    {
        $invoice = $event->invoice;

        Audit::log($invoice->id, 'invoice.expired', []);

        TerminateServiceJob::dispatch($invoice);

        Log::info('HandleInvoiceExpired dispatched TerminateServiceJob', ['invoice_id' => $invoice->id]);
    }
}
