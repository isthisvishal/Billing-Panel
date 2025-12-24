<?php

namespace App\Http\Controllers;

use App\Models\Page;

class PageController extends Controller
{
    /**
     * Show a published page by slug
     */
    public function show($slug)
    {
        $page = Page::findBySlug($slug);

        if (!$page) {
            abort(404);
        }

        return view('pages.show', [
            'page' => $page,
        ]);
    }
}
