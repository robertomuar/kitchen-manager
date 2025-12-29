<?php

namespace App\Providers;

use App\Models\Location;
use App\Models\Product;
use App\Models\StockItem;
use App\Policies\LocationPolicy;
use App\Policies\ProductPolicy;
use App\Policies\StockItemPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Product::class   => ProductPolicy::class,
        Location::class  => LocationPolicy::class,
        StockItem::class => StockItemPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
