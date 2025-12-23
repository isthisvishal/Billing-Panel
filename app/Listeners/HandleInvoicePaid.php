<?php

namespace App\Listeners;

use App\Events\InvoicePaid;
use App\Jobs\CreateServiceJob;
use App\Jobs\UnsuspendServiceJob;
use App\Models\Audit;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class HandleInvoicePaid implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(InvoicePaid $event)
    {
        $invoice = $event->invoice;

        // Audit
        Audit::log($invoice->id, 'invoice.paid', ['amount' => $invoice->amount]);

        if ($invoice->service_id) {
            Log::info('InvoicePaid: dispatching UnsuspendServiceJob', ['invoice_id' => $invoice->id]);
            UnsuspendServiceJob::dispatch($invoice);
        } else {
            Log::info('InvoicePaid: dispatching CreateServiceJob', ['invoice_id' => $invoice->id]);
            CreateServiceJob::dispatch($invoice);
        }
    }
}
