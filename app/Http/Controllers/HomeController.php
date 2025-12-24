<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\ServiceCategory;

class HomeController extends Controller
{
    /**
     * Show the application homepage
     */
    public function index()
    {
        $categories = ServiceCategory::active()->get();
        $featuredCategories = $categories->take(3);

        return view('index', [
            'categories' => $featuredCategories,
        ]);
    }
}
