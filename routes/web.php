<?php

use App\Models\Product;
use App\Models\StockItem;
use App\Models\Location;

use App\Http\Controllers\BlogController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicPageController;
use App\Http\Controllers\RobotsController;
use App\Http\Controllers\StockItemController;
use App\Http\Controllers\KitchenShareController;
use App\Http\Controllers\KitchenSelectionController;
use App\Http\Controllers\BarcodeLookupController;
use App\Http\Controllers\SitemapController;

// ✅ Admin
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminDatabaseController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [PublicPageController::class, 'home'])->name('home');
Route::get('/features', [PublicPageController::class, 'features'])->name('features');
Route::get('/faq', [PublicPageController::class, 'faq'])->name('faq');
Route::get('/pricing', [PublicPageController::class, 'pricing'])->name('pricing');
Route::get('/contact', [PublicPageController::class, 'contact'])->name('contact');
Route::get('/privacy-policy', [PublicPageController::class, 'privacy'])->name('privacy');
Route::get('/terms', [PublicPageController::class, 'terms'])->name('terms');
Route::get('/cookies-policy', [PublicPageController::class, 'cookiesPolicy'])->name('cookies-policy');
Route::get('/legal/aviso-legal', [PublicPageController::class, 'legalNotice'])->name('legal.notice');
Route::get('/legal/terminos', [PublicPageController::class, 'legalTerms'])->name('legal.terms');
Route::get('/legal/privacidad', [PublicPageController::class, 'legalPrivacy'])->name('legal.privacy');
Route::get('/legal/cookies', [PublicPageController::class, 'legalCookies'])->name('legal.cookies');

Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

Route::get('/robots.txt', RobotsController::class)->name('robots');
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

/**
 * ✅ ADMIN: Dashboard + DB Browser (solo admin)
 */
Route::middleware(['auth', 'verified', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::get('/db', [AdminDatabaseController::class, 'index'])->name('db');
        Route::get('/db/row', [AdminDatabaseController::class, 'showRow'])->name('db.row');
    });

/**
 * DEBUG: ver qué productos devuelve la búsqueda por código de barras
 * ✅ Protegido: SOLO admin (evita que quede público)
 */
Route::get('/debug/barcode', function (Request $request) {
    $raw = trim($request->query('barcode', ''));
    $numeric = preg_replace('/\D+/', '', $raw);

    $candidates = array_values(array_unique(array_filter([
        $raw,
        $numeric,
    ])));

    $products = Product::withoutGlobalScopes()
        ->where(function ($q) use ($candidates) {
            foreach ($candidates as $code) {
                $q->orWhere('barcode', $code);
            }
        })
        ->get([
            'id',
            'name',
            'barcode',
            'default_quantity',
            'default_unit',
            'default_pack_size',
            'user_id',
        ]);

    return response()->json([
        'input' => [
            'raw'        => $raw,
            'numeric'    => $numeric,
            'candidates' => $candidates,
        ],
        'count' => $products->count(),
        'items' => $products,
    ]);
})->middleware(['auth', 'verified', 'admin'])->name('debug.barcode');

/**
 * Lookup de código de barras (AJAX)
 */
Route::post('/barcode/lookup', [BarcodeLookupController::class, 'lookup'])
    ->middleware(['auth', 'verified', 'throttle:30,1'])
    ->name('barcode.lookup');

// Todo lo privado va con auth + verified
Route::middleware(['auth', 'verified'])->group(function () {

    // === DASHBOARD ===
    Route::get('/dashboard', function () {
        $user    = auth()->user();
        $owner   = $user->kitchenOwner();
        $ownerId = $user->kitchenOwnerId();
        $kitchenId = $owner->currentKitchen()?->id;

        abort_if($kitchenId === null, 403, 'No hay una cocina activa seleccionada.');

        $today         = now()->startOfDay();
        $soonLimit     = $today->copy()->addDays(7);
        $urgentLimit   = $today->copy()->addDays(2);
        $availableSql  = 'CASE WHEN (quantity - COALESCE(open_units, 0)) < 0 THEN 0 ELSE (quantity - COALESCE(open_units, 0)) END';

        $stats = Cache::remember("dashboard.stats.{$ownerId}.{$kitchenId}", 300, function () use ($ownerId, $kitchenId, $today, $soonLimit, $urgentLimit) {
            return [
                'products_count'       => Product::where('user_id', $ownerId)->where('kitchen_id', $kitchenId)->count(),
                'locations_count'      => Location::where('user_id', $ownerId)->where('kitchen_id', $kitchenId)->count(),
                'stock_items_count'    => StockItem::where('user_id', $ownerId)->where('kitchen_id', $kitchenId)->count(),

                'low_stock_count' => StockItem::where('user_id', $ownerId)
                    ->where('kitchen_id', $kitchenId)
                    ->whereNotNull('min_quantity')
                    ->whereRaw("{$availableSql} <= min_quantity")
                    ->count(),

                'soon_expiring_count' => StockItem::where('user_id', $ownerId)
                    ->where('kitchen_id', $kitchenId)
                    ->whereNotNull('expires_at')
                    ->whereDate('expires_at', '>=', $today)
                    ->whereDate('expires_at', '<=', $soonLimit)
                    ->count(),

                'urgent_expiring_count' => StockItem::where('user_id', $ownerId)
                    ->where('kitchen_id', $kitchenId)
                    ->whereNotNull('expires_at')
                    ->whereDate('expires_at', '>=', $today)
                    ->whereDate('expires_at', '<=', $urgentLimit)
                    ->count(),
            ];
        });

        $lowStockItems = Cache::remember("dashboard.low-stock.{$ownerId}.{$kitchenId}", 300, function () use ($ownerId, $kitchenId) {
            return StockItem::with(['product', 'location'])
                ->where('user_id', $ownerId)
                ->where('kitchen_id', $kitchenId)
                ->whereNotNull('min_quantity')
                ->whereRaw("{$availableSql} <= min_quantity")
                ->orderByDesc('updated_at')
                ->take(5)
                ->get();
        });

        $soonExpiringItems = Cache::remember("dashboard.soon-expiring.{$ownerId}.{$kitchenId}", 300, function () use ($ownerId, $kitchenId, $today, $soonLimit) {
            return StockItem::with(['product', 'location'])
                ->where('user_id', $ownerId)
                ->where('kitchen_id', $kitchenId)
                ->whereNotNull('expires_at')
                ->whereDate('expires_at', '>=', $today)
                ->whereDate('expires_at', '<=', $soonLimit)
                ->orderBy('expires_at')
                ->take(10)
                ->get();
        });

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
    Route::get('/products/options', [ProductController::class, 'options'])->name('products.options');

    // ✅ NUEVO: evita 405 en GET/HEAD /products/{id}
    Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

    // === STOCK ===
    Route::get('/stock', [StockItemController::class, 'index'])->name('stock.index');
    Route::get('/stock/create', [StockItemController::class, 'create'])->name('stock.create');

    // OJO: rutas fijas ANTES del {stockItem}
    Route::get('/stock/export/missing.csv', [StockItemController::class, 'exportMissingToCsv'])
        ->name('stock.export.missing');

    // ✅ NUEVO: PDF de reposición bajo mínimo
    Route::get('/stock/export/missing.pdf', [StockItemController::class, 'exportMissingToPdf'])
        ->name('stock.export.missing.pdf');

    Route::patch('/stock/{stockItem}/open', [StockItemController::class, 'open'])
        ->name('stock.open');
    Route::patch('/stock/{stockItem}/close', [StockItemController::class, 'close'])
        ->name('stock.close');

    Route::get('/stock/{stockItem}/edit', [StockItemController::class, 'edit'])->name('stock.edit');

    // GET /stock/{id} -> para XHR o para compatibilidad (evita 405)
    Route::get('/stock/{stockItem}', [StockItemController::class, 'show'])->name('stock.show');

    Route::post('/stock', [StockItemController::class, 'store'])->name('stock.store');
    Route::put('/stock/{stockItem}', [StockItemController::class, 'update'])->name('stock.update');
    Route::delete('/stock/{stockItem}', [StockItemController::class, 'destroy'])->name('stock.destroy');

    // === UBICACIONES ===
    Route::get('/locations', [LocationController::class, 'index'])->name('locations.index');
    Route::get('/locations/create', [LocationController::class, 'create'])->name('locations.create');
    Route::get('/locations/{location}/edit', [LocationController::class, 'edit'])->name('locations.edit');
    Route::get('/locations/options', [LocationController::class, 'options'])->name('locations.options');

    // ✅ NUEVO: evita 405 en GET/HEAD /locations/{id}
    Route::get('/locations/{location}', [LocationController::class, 'show'])->name('locations.show');

    Route::post('/locations', [LocationController::class, 'store'])->name('locations.store');
    Route::put('/locations/{location}', [LocationController::class, 'update'])->name('locations.update');
    Route::delete('/locations/{location}', [LocationController::class, 'destroy'])->name('locations.destroy');

    // === COMPARTIR COCINA ===
    Route::post('/kitchen/share', [KitchenShareController::class, 'store'])
        ->name('kitchen.share.store');
    Route::delete('/kitchen/share/{share}', [KitchenShareController::class, 'destroy'])
        ->name('kitchen.share.destroy');

    // === SELECCIONAR COCINA ===
    Route::post('/kitchen/select', KitchenSelectionController::class)
        ->name('kitchen.select');

    // === PERFIL ===
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
