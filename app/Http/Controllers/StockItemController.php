<?php

namespace App\Http\Controllers;

use App\Http\Requests\StockItemRequest;
use App\Models\Location;
use App\Models\Product;
use App\Models\StockItem;
use App\Models\StockMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Barryvdh\DomPDF\Facade\Pdf;

class StockItemController extends Controller
{
    /**
     * Listado de stock con filtros y ordenación.
     */
    public function index(Request $request): Response
    {
        [$owner, $ownerId, $kitchenId] = $this->resolveKitchenContext($request);

        $productId  = $request->input('product_id');
        $locationId = $request->input('location_id');
        $status     = $request->input('status');      // low / normal / null
        $sort       = $request->input('sort', 'expires_at');
        $direction  = $request->input('direction', 'asc');

        $availableSql = 'CASE WHEN (quantity - open_units) < 0 THEN 0 ELSE (quantity - open_units) END';

        $query = StockItem::with(['product', 'location'])
            ->where('user_id', $ownerId)
            ->where('kitchen_id', $kitchenId);

        if (!empty($productId)) {
            $query->where('product_id', $productId);
        }

        if (!empty($locationId)) {
            $query->where('location_id', $locationId);
        }

        if ($status === 'low') {
            $query->whereNotNull('min_quantity')
                ->whereRaw("{$availableSql} < min_quantity");
        } elseif ($status === 'normal') {
            $query->where(function ($q) use ($availableSql) {
                $q->whereNull('min_quantity')
                    ->orWhereRaw("{$availableSql} >= min_quantity");
            });
        }

        if (!in_array($sort, ['expires_at', 'quantity'], true)) {
            $sort = 'expires_at';
        }

        if (!in_array($direction, ['asc', 'desc'], true)) {
            $direction = 'asc';
        }

        if ($sort === 'quantity') {
            $query->orderByRaw("{$availableSql} {$direction}");
        } else {
            $query->orderBy($sort, $direction);
        }

        $stockItems = $query->orderBy('id')
            ->paginate(25)
            ->withQueryString();

        $products = Cache::remember("products.list.{$ownerId}.{$kitchenId}", 300, function () use ($owner, $kitchenId) {
            return $owner->products()
                ->where('kitchen_id', $kitchenId)
                ->orderBy('name')
                ->get();
        });

        $locations = Cache::remember("locations.list.{$ownerId}.{$kitchenId}", 300, function () use ($owner, $kitchenId) {
            return $owner->locations()
                ->where('kitchen_id', $kitchenId)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();
        });

        return Inertia::render('Stock/Index', [
            'stockItems' => $stockItems,
            'products'   => $products,
            'locations'  => $locations,
            'filters'    => [
                'product_id'  => $productId ? (string) $productId : '',
                'location_id' => $locationId ? (string) $locationId : '',
                'status'      => $status ?? '',
                'sort'        => $sort,
                'direction'   => $direction,
            ],
        ]);
    }

    /**
     * Formulario de creación.
     */
    public function create(Request $request): Response
    {
        [$owner, $ownerId, $kitchenId] = $this->resolveKitchenContext($request);

        $products = Cache::remember("products.list.{$ownerId}.{$kitchenId}", 300, function () use ($owner, $kitchenId) {
            return $owner->products()
                ->where('kitchen_id', $kitchenId)
                ->orderBy('name')
                ->get();
        });

        $locations = Cache::remember("locations.list.{$ownerId}.{$kitchenId}", 300, function () use ($owner, $kitchenId) {
            return $owner->locations()
                ->where('kitchen_id', $kitchenId)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();
        });

        return Inertia::render('Stock/Form', [
            'mode'      => 'create',
            'stockItem' => null,
            'products'  => $products,
            'locations' => $locations,
            'movements' => [],
        ]);
    }

    /**
     * Guardar nuevo registro.
     */
    public function store(StockItemRequest $request): RedirectResponse
    {
        [$owner, $ownerId, $kitchenId] = $this->resolveKitchenContext($request);

        $data = $request->validated();
        $product = Product::where('id', $data['product_id'])
            ->where('user_id', $ownerId)
            ->where('kitchen_id', $kitchenId)
            ->firstOrFail();

        if (!empty($data['location_id'])) {
            $location = Location::where('id', $data['location_id'])
                ->where('user_id', $ownerId)
                ->where('kitchen_id', $kitchenId)
                ->firstOrFail();

            $data['location_id'] = $location->id;
        }

        $data['open_units'] = (int) ($data['open_units'] ?? 0);
        $data['is_open'] = $data['open_units'] > 0;
        $data['user_id']    = $ownerId;
        $data['kitchen_id'] = $kitchenId;
        $data['product_id'] = $product->id;

        DB::transaction(function () use ($data, $kitchenId, $ownerId) {
            $stockItem = StockItem::create($data);

            StockMovement::create([
                'stock_item_id'    => $stockItem->id,
                'user_id'          => $ownerId,
                'kitchen_id'       => $kitchenId,
                'product_id'       => $stockItem->product_id,
                'location_id'      => $stockItem->location_id,
                'action'           => 'created',
                'quantity_before'  => null,
                'quantity_after'   => $stockItem->quantity,
            ]);
        });

        $this->forgetDashboardCache($ownerId, $kitchenId);

        return redirect()
            ->route('stock.index')
            ->with('success', 'Stock guardado correctamente.');
    }

    /**
     * SHOW: evita 405 en GET/HEAD /stock/{id}
     */
    public function show(Request $request, StockItem $stockItem)
    {
        [, $ownerId, $kitchenId] = $this->resolveKitchenContext($request);

        $this->authorize('view', $stockItem);

        if ($request->header('X-Inertia')) {
            return Inertia::location(route('stock.edit', $stockItem->id));
        }

        if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
            return response()->json(
                $stockItem->load(['product', 'location'])
            );
        }

        return redirect()->route('stock.edit', $stockItem->id);
    }

    /**
     * Formulario de edición.
     */
    public function edit(Request $request, StockItem $stockItem): Response
    {
        [$owner, $ownerId, $kitchenId] = $this->resolveKitchenContext($request);

        $this->authorize('view', $stockItem);

        $products = Cache::remember("products.list.{$ownerId}.{$kitchenId}", 300, function () use ($owner, $kitchenId) {
            return $owner->products()
                ->where('kitchen_id', $kitchenId)
                ->orderBy('name')
                ->get();
        });

        $locations = Cache::remember("locations.list.{$ownerId}.{$kitchenId}", 300, function () use ($owner, $kitchenId) {
            return $owner->locations()
                ->where('kitchen_id', $kitchenId)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();
        });

        $movements = $stockItem->movements()
            ->orderByDesc('created_at')
            ->take(10)
            ->get([
                'id',
                'action',
                'quantity_before',
                'quantity_after',
                'created_at',
            ]);

        return Inertia::render('Stock/Form', [
            'mode'      => 'edit',
            'stockItem' => $stockItem->load(['product', 'location']),
            'products'  => $products,
            'locations' => $locations,
            'movements' => $movements,
        ]);
    }

    /**
     * Actualizar registro.
     */
    public function update(StockItemRequest $request, StockItem $stockItem): RedirectResponse
    {
        [$owner, $ownerId, $kitchenId] = $this->resolveKitchenContext($request);

        $this->authorize('update', $stockItem);

        $data = $request->validated();
        $hasOpenUnits = array_key_exists('open_units', $data);

        $product = Product::where('id', $data['product_id'])
            ->where('user_id', $ownerId)
            ->where('kitchen_id', $kitchenId)
            ->firstOrFail();

        if (!empty($data['location_id'])) {
            $location = Location::where('id', $data['location_id'])
                ->where('user_id', $ownerId)
                ->where('kitchen_id', $kitchenId)
                ->firstOrFail();

            $data['location_id'] = $location->id;
        }

        $previousQuantity = $stockItem->quantity;
        $data['open_units'] = $hasOpenUnits
            ? (int) ($data['open_units'] ?? 0)
            : (int) ($stockItem->open_units ?? 0);
        $data['is_open'] = $data['open_units'] > 0;

        DB::transaction(function () use ($data, $ownerId, $kitchenId, $product, $stockItem, $previousQuantity) {
            $stockItem->update($data + [
                'user_id'    => $ownerId,
                'kitchen_id' => $kitchenId,
                'product_id' => $product->id,
            ]);

            StockMovement::create([
                'stock_item_id'    => $stockItem->id,
                'user_id'          => $ownerId,
                'kitchen_id'       => $kitchenId,
                'product_id'       => $stockItem->product_id,
                'location_id'      => $stockItem->location_id,
                'action'           => 'updated',
                'quantity_before'  => $previousQuantity,
                'quantity_after'   => $stockItem->quantity,
            ]);
        });

        $this->forgetDashboardCache($ownerId, $kitchenId);

        return redirect()
            ->route('stock.index')
            ->with('success', 'Stock actualizado correctamente.');
    }

    /**
     * Borrar registro.
     */
    public function destroy(Request $request, StockItem $stockItem): RedirectResponse
    {
        [, $ownerId, $kitchenId] = $this->resolveKitchenContext($request);

        $this->authorize('delete', $stockItem);

        $movementData = [
            'stock_item_id'    => $stockItem->id,
            'user_id'          => $ownerId,
            'kitchen_id'       => $kitchenId,
            'product_id'       => $stockItem->product_id,
            'location_id'      => $stockItem->location_id,
            'action'           => 'deleted',
            'quantity_before'  => $stockItem->quantity,
            'quantity_after'   => null,
        ];

        DB::transaction(function () use ($stockItem, $movementData) {
            StockMovement::create($movementData);
            $stockItem->delete();
        });

        $this->forgetDashboardCache($ownerId, $kitchenId);

        return redirect()
            ->route('stock.index')
            ->with('success', 'Stock eliminado correctamente.');
    }

    public function open(Request $request, StockItem $stockItem): RedirectResponse
    {
        [, $ownerId, $kitchenId] = $this->resolveKitchenContext($request);

        $this->authorize('update', $stockItem);
        abort_if($stockItem->user_id !== $ownerId || $stockItem->kitchen_id !== $kitchenId, 403);

        $newOpenUnits = min((float) $stockItem->quantity, (int) $stockItem->open_units + 1);

        $stockItem->update([
            'open_units' => (int) $newOpenUnits,
            'is_open'    => $newOpenUnits > 0,
        ]);

        $this->forgetDashboardCache($ownerId, $kitchenId);

        return back()->with('success', 'Marcado como abierto.');
    }

    public function close(Request $request, StockItem $stockItem): RedirectResponse
    {
        [, $ownerId, $kitchenId] = $this->resolveKitchenContext($request);

        $this->authorize('update', $stockItem);
        abort_if($stockItem->user_id !== $ownerId || $stockItem->kitchen_id !== $kitchenId, 403);

        $newOpenUnits = max(0, (int) $stockItem->open_units - 1);

        $stockItem->update([
            'open_units' => $newOpenUnits,
            'is_open'    => $newOpenUnits > 0,
        ]);

        $this->forgetDashboardCache($ownerId, $kitchenId);

        return back()->with('success', 'Marcado como cerrado.');
    }

    /**
     * Exportar CSV de reposición (bajo mínimo).
     */
    public function exportMissingToCsv(Request $request): StreamedResponse
    {
        [, $ownerId, $kitchenId] = $this->resolveKitchenContext($request);

        $productId  = $request->query('product_id');
        $locationId = $request->query('location_id');

        $availableSql = 'CASE WHEN (quantity - open_units) < 0 THEN 0 ELSE (quantity - open_units) END';

        $query = StockItem::with(['product', 'location'])
            ->where('user_id', $ownerId)
            ->where('kitchen_id', $kitchenId)
            ->whereNotNull('min_quantity')
            ->whereRaw("{$availableSql} < min_quantity");

        if (!empty($productId)) {
            $query->where('product_id', $productId);
        }

        if (!empty($locationId)) {
            $query->where('location_id', $locationId);
        }

        $filename = 'reposicion_bajo_minimo.csv';

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');

            fputcsv($out, [
                'Producto',
                'Ubicación',
                'Cantidad',
                'Unidad',
                'Mínimo',
                'Falta aprox.',
                'Caducidad',
                'Abierto',
                'Notas',
            ], ';');

            $query->orderBy('id')->chunk(200, function ($itemsChunk) use ($out) {
                foreach ($itemsChunk as $item) {
                    $missing = max(0, (float)($item->min_quantity ?? 0) - (float)($item->available_units ?? 0));

                    fputcsv($out, [
                        $item->product?->name ?? '',
                        $item->location?->name ?? '',
                        $item->quantity,
                        $item->unit,
                        $item->min_quantity,
                        number_format($missing, 2, ',', ''),
                        $item->expires_at ? substr((string)$item->expires_at, 0, 10) : '',
                        $item->is_open ? 'sí' : 'no',
                        $item->notes ?? '',
                    ], ';');
                }
            });

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * ✅ Exportar PDF de reposición (bajo mínimo).
     */
    public function exportMissingToPdf(Request $request)
    {
        [, $ownerId, $kitchenId] = $this->resolveKitchenContext($request);

        $productId  = $request->query('product_id');
        $locationId = $request->query('location_id');

        $availableSql = 'CASE WHEN (quantity - open_units) < 0 THEN 0 ELSE (quantity - open_units) END';

        $query = StockItem::with(['product', 'location'])
            ->where('user_id', $ownerId)
            ->where('kitchen_id', $kitchenId)
            ->whereNotNull('min_quantity')
            ->whereRaw("{$availableSql} < min_quantity");

        if (!empty($productId)) {
            $query->where('product_id', $productId);
        }

        if (!empty($locationId)) {
            $query->where('location_id', $locationId);
        }

        $items = $query->orderBy('id')->lazy();

        $pdf = Pdf::loadView('pdf.replenishment', [
            'items'       => $items,
            'user'        => $request->user(),
            'generatedAt' => now(),
        ])->setPaper('a4', 'portrait');

        return $pdf->download('reposicion_bajo_minimo.pdf');
    }
}
