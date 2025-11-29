<?php

use App\Models\Product;
use App\Models\StockItem;
use App\Models\Location;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StockItemController;
use App\Http\Controllers\KitchenShareController;
use App\Http\Controllers\BarcodeLookupController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin'       => Route::has('login'),
        'canRegister'    => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion'     => PHP_VERSION,
    ]);
});

// Todo lo privado va con auth + verified para garantizar correos validados
Route::middleware(['auth', 'verified'])->group(function () {
    // === DASHBOARD ===
    Route::get('/dashboard', function () {
        $user    = auth()->user();
        $ownerId = $user->kitchenOwnerId();

        $today       = now()->startOfDay();
        $soonLimit   = $today->copy()->addDays(7);
        $urgentLimit = $today->copy()->addDays(2);

        $stats = [
            'products_count'       => Product::where('user_id', $ownerId)->count(),
            'locations_count'      => Location::where('user_id', $ownerId)->count(),
            'stock_items_count'    => StockItem::where('user_id', $ownerId)->count(),

            'low_stock_count' => StockItem::where('user_id', $ownerId)
                ->whereNotNull('min_quantity')
                ->whereColumn('quantity', '<', 'min_quantity')
                ->count(),

            'soon_expiring_count' => StockItem::where('user_id', $ownerId)
                ->whereNotNull('expires_at')
                ->whereDate('expires_at', '>=', $today)
                ->whereDate('expires_at', '<=', $soonLimit)
                ->count(),

            'urgent_expiring_count' => StockItem::where('user_id', $ownerId)
                ->whereNotNull('expires_at')
                ->whereDate('expires_at', '>=', $today)
                ->whereDate('expires_at', '<=', $urgentLimit)
                ->count(),
        ];

        $lowStockItems = StockItem::with(['product', 'location'])
            ->where('user_id', $ownerId)
            ->whereNotNull('min_quantity')
            ->whereColumn('quantity', '<', 'min_quantity')
            ->orderByDesc('updated_at')
            ->take(5)
            ->get();

        $soonExpiringItems = StockItem::with(['product', 'location'])
            ->where('user_id', $ownerId)
            ->whereNotNull('expires_at')
            ->whereDate('expires_at', '>=', $today)
            ->whereDate('expires_at', '<=', $soonLimit)
            ->orderBy('expires_at')
            ->take(10)
            ->get();

        return Inertia::render('Dashboard', [
            'stats'             => $stats,
            'lowStockItems'     => $lowStockItems,
            'soonExpiringItems' => $soonExpiringItems,
        ]);
    })->name('dashboard');

    // === PRODUCTOS ===
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

    // === LOOKUP CÓDIGO DE BARRAS (AJAX) ===
    Route::post('/barcode/lookup', [BarcodeLookupController::class, 'lookup'])
        ->name('barcode.lookup');

    // === STOCK ===
    Route::get('/stock', [StockItemController::class, 'index'])->name('stock.index');
    Route::get('/stock/create', [StockItemController::class, 'create'])->name('stock.create');
    Route::get('/stock/{stockItem}/edit', [StockItemController::class, 'edit'])->name('stock.edit');
    Route::post('/stock', [StockItemController::class, 'store'])->name('stock.store');
    Route::put('/stock/{stockItem}', [StockItemController::class, 'update'])->name('stock.update');
    Route::delete('/stock/{stockItem}', [StockItemController::class, 'destroy'])->name('stock.destroy');
    Route::get('/stock/replenishment/export', [StockItemController::class, 'exportReplenishment'])
        ->name('stock.replenishment.export');

    // === UBICACIONES ===
    Route::get('/locations', [LocationController::class, 'index'])->name('locations.index');
    Route::get('/locations/create', [LocationController::class, 'create'])->name('locations.create');
    Route::get('/locations/{location}/edit', [LocationController::class, 'edit'])->name('locations.edit');
    Route::post('/locations', [LocationController::class, 'store'])->name('locations.store');
    Route::put('/locations/{location}', [LocationController::class, 'update'])->name('locations.update');
    Route::delete('/locations/{location}', [LocationController::class, 'destroy'])->name('locations.destroy');

    // === COMPARTIR COCINA ===
    Route::post('/kitchen/share', [KitchenShareController::class, 'store'])
        ->name('kitchen.share.store');
    Route::delete('/kitchen/share/{share}', [KitchenShareController::class, 'destroy'])
        ->name('kitchen.share.destroy');

    // === PERFIL ===
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
