<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Event;
use App\Models\Invoice;
use App\Models\Plugin;
use App\Models\PluginConfig;

class StripeWebhookTest extends TestCase
{
    public function test_stripe_webhook_marks_invoice_paid()
    {
        Event::fake();

        // Create plugin record and config
        $plugin = Plugin::updateOrCreate(['key' => 'stripe'], ['name' => 'Stripe', 'type' => 'payment', 'enabled' => true]);
        PluginConfig::updateOrCreate(['plugin_id' => $plugin->id, 'key' => 'webhook_secret'], ['value' => 'whsec_test', 'encrypted' => false]);

        $invoice = Invoice::factory()->create(['status' => 'pending']);

        $payload = json_encode([
            'type' => 'charge.succeeded',
            'data' => ['object' => ['metadata' => ['invoice_id' => $invoice->id]]]
        ]);

        $timestamp = time();
        $sig = hash_hmac('sha256', $timestamp . '.' . $payload, 'whsec_test');
        $sigHeader = 't=' . $timestamp . ',v1=' . $sig;

        $response = $this->postJson('/api/webhooks/payment/stripe', [], ['Stripe-Signature' => $sigHeader, 'CONTENT_TYPE' => 'application/json'], $payload);

        $response->assertStatus(200);

        $invoice->refresh();
        $this->assertEquals('paid', $invoice->status);
    }
}
