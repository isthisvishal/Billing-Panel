<?php

namespace App\Listeners;

use App\Events\InvoiceGraceWarning;
use App\Jobs\SuspendServiceJob;
use App\Models\Audit;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\InvoiceGraceWarningNotification;

class HandleInvoiceGraceWarning implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(InvoiceGraceWarning $event)
    {
        $invoice = $event->invoice;

        // Audit
        Audit::log($invoice->id, 'invoice.grace_warning', []);

        // Notify the customer
        Notification::send($invoice->user, new InvoiceGraceWarningNotification($invoice));

        // Optionally, schedule suspension after grace period via cron - not immediate
        Log::info('HandleInvoiceGraceWarning notified customer', ['invoice_id' => $invoice->id]);
    }
}
