<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Invoice;
use App\Models\AdminSetting;
use App\Jobs\SuspendServiceJob;
use App\Jobs\TerminateServiceJob;
use App\Events\InvoiceOverdue;
use App\Events\InvoiceGraceWarning;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ScanInvoices extends Command
{
    protected $signature = 'invoices:scan';

    protected $description = 'Scan invoices to send warnings, suspend or terminate services based on grace periods';

    public function handle()
    {
        $this->info('Running invoice scan...');

        $graceDays = (int) AdminSetting::get('grace_days', 7);
        $warningDays = (int) AdminSetting::get('warning_days', 3);
        $autoTerminateDays = (int) AdminSetting::get('auto_terminate_days', 30);

        $today = Carbon::now();

        // 1) Mark invoices overdue when past due date
        $overdueInvoices = Invoice::where('status', 'pending')
            ->whereNotNull('due_date')
            ->where('due_date', '<', $today)
            ->get();

        foreach ($overdueInvoices as $invoice) {
            $invoice->status = 'overdue';
            $invoice->save();
            InvoiceOverdue::dispatch($invoice);
            Log::info('Invoice marked overdue', ['invoice_id' => $invoice->id]);
        }

        // 2) Handle grace warnings
        $warningThreshold = $today->copy()->subDays($warningDays);
        $invoicesToWarn = Invoice::where('status', 'overdue')
            ->whereNull('grace_notified_at')
            ->where('due_date', '<', $today->copy()->subDays(0))
            ->get();

        foreach ($invoicesToWarn as $invoice) {
            // notify if within warningDays before full grace expires
            $daysOverdue = $invoice->due_date ? $today->diffInDays($invoice->due_date) : null;
            if ($daysOverdue !== null && $daysOverdue >= 0 && $daysOverdue <= $graceDays) {
                InvoiceGraceWarning::dispatch($invoice);
                $invoice->grace_notified_at = now();
                $invoice->save();
                Log::info('Invoice grace warning sent', ['invoice_id' => $invoice->id]);
            }
        }

        // 3) Suspend after grace period
        $suspendThreshold = $today->copy()->subDays($graceDays);
        $toSuspend = Invoice::where('status', 'overdue')
            ->where('due_date', '<', $suspendThreshold)
            ->get();

        foreach ($toSuspend as $invoice) {
            SuspendServiceJob::dispatch($invoice);
            Log::info('SuspendServiceJob dispatched due to grace expiry', ['invoice_id' => $invoice->id]);
        }

        // 4) Terminate after autoTerminateDays
        $terminateThreshold = $today->copy()->subDays($autoTerminateDays);
        $toTerminate = Invoice::where(function ($q) use ($terminateThreshold) {
            $q->where('status', 'overdue')->where('due_date', '<', $terminateThreshold);
        })->get();

        foreach ($toTerminate as $invoice) {
            TerminateServiceJob::dispatch($invoice);
            Log::info('TerminateServiceJob dispatched due to auto-terminate', ['invoice_id' => $invoice->id]);
        }

        $this->info('Invoice scan completed.');
    }
}
