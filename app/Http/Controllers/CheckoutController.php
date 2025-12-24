<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Plan;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    /**
     * Show checkout page for a plan
     */
    public function show(Request $request, $planId)
    {
        $plan = Plan::findOrFail($planId);

        if (!$plan->is_active) {
            abort(404);
        }

        $billingCycle = $request->query('cycle', 'monthly');
        if (!in_array($billingCycle, ['monthly', 'yearly', 'lifetime'])) {
            $billingCycle = 'monthly';
        }

        $price = $plan->getPrice($billingCycle);

        return view('checkout.show', [
            'plan' => $plan,
            'billingCycle' => $billingCycle,
            'price' => $price,
        ]);
    }

    /**
     * Process the checkout
     */
    public function process(Request $request, $planId)
    {
        $request->validate([
            'billing_cycle' => 'required|in:monthly,yearly,lifetime',
        ]);

        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login')->with('redirect', route('checkout.show', $planId));
        }

        $plan = Plan::findOrFail($planId);

        if (!$plan->is_active) {
            abort(404);
        }

        $billingCycle = $request->input('billing_cycle');
        $price = $plan->getPrice($billingCycle);

        // Create an order
        $order = Order::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'billing_cycle' => $billingCycle,
            'amount' => $price,
            'status' => Order::STATUS_PENDING,
        ]);

        // TODO: Redirect to payment gateway
        // For now, mark as active (in production, integrate Stripe/PayPal)
        $order->update(['status' => Order::STATUS_ACTIVE]);

        return redirect()->route('dashboard.orders')
            ->with('success', "Order placed successfully! You now have access to {$plan->name}.");
    }
}
