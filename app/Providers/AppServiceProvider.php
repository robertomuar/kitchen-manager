<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

 // app/Providers/AppServiceProvider.php

public function boot()
{
    \URL::forceScheme('https');
    // ... cualquier otro boot
}

}
