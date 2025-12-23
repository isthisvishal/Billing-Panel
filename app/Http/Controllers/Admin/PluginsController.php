<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plugin;
use Illuminate\Http\Request;

class PluginsController extends Controller
{
    public function index()
    {
        return Plugin::all();
    }

    public function update(Request $request, Plugin $plugin)
    {
        $this->authorize('manage-plugins');

        $plugin->enabled = (bool)$request->input('enabled');
        $plugin->save();

        return $plugin;
    }
}
