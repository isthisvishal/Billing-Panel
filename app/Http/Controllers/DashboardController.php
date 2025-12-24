<?php

namespace App\Http\Controllers;

use App\Models\Order;

class DashboardController extends Controller
{
    /**
     * Show user dashboard
     */
    public function index()
    {
        $user = auth()->user();
        $orders = $user->orders()->with('plan.category')->latest()->get();

        return view('dashboard.index', [
            'user' => $user,
            'orders' => $orders,
        ]);
    }

    /**
     * Show user orders
     */
    public function orders()
    {
        $user = auth()->user();
        $orders = $user->orders()
            ->with('plan.category')
            ->latest()
            ->paginate(15);

        return view('dashboard.orders', [
            'orders' => $orders,
        ]);
    }
}
