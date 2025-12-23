<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Gate::define('access-admin', function ($user) {
            return $user->is_admin ?? false;
        });

        Gate::define('manage-settings', function ($user) {
            return $user->is_admin ?? false;
        });

        Gate::define('manage-plugins', function ($user) {
            return $user->is_admin ?? false;
        });
    }
}
