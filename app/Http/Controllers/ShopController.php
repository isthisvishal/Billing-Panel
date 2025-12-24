<?php

namespace App\Http\Controllers;

use App\Models\ServiceCategory;

class ShopController extends Controller
{
    /**
     * Show the shop page with all categories
     */
    public function index()
    {
        $categories = ServiceCategory::active()->get();

        return view('shop.index', [
            'categories' => $categories,
        ]);
    }

    /**
     * Show plans for a specific category
     */
    public function showCategory($slug)
    {
        $category = ServiceCategory::findBySlug($slug);

        if (!$category) {
            abort(404);
        }

        $plans = $category->plans()
            ->active()
            ->ordered()
            ->get();

        return view('shop.category', [
            'category' => $category,
            'plans' => $plans,
        ]);
    }
}
