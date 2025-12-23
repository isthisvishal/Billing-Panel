<?php

namespace App\Console;

use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\ScanInvoices;

class Kernel extends ConsoleKernel
{
    protected function schedule(\Illuminate\Console\Scheduling\Schedule $schedule)
    {
        $schedule->command('invoices:scan')->daily();
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
    }
}
