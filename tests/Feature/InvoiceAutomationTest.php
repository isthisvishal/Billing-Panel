<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Event;
use App\Models\Invoice;
use App\Events\InvoicePaid;

class InvoiceAutomationTest extends TestCase
{
    public function test_invoice_paid_triggers_event()
    {
        Event::fake();

        $invoice = Invoice::factory()->create(['status' => 'pending']);

        $invoice->status = 'paid';
        $invoice->save();

        Event::assertDispatched(InvoicePaid::class);
    }
}
