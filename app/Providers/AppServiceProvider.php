<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        // Solo forzamos HTTPS en producción
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
