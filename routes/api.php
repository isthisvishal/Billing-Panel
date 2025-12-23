<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Webhook\PaymentWebhookController;

// Payment webhooks
Route::post('webhooks/payment/{plugin}', [PaymentWebhookController::class, 'handle']);
