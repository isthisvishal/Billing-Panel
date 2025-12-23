<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Plugins\PluginManager;

class PluginServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(PluginManager::class, function ($app) {
            return new PluginManager();
        });
    }

    public function boot()
    {
        //
    }
}
