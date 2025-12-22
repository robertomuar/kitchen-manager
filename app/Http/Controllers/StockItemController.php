<?php

namespace App\Http\Controllers;

use App\Http\Requests\StockItemRequest;
use App\Models\StockItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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
        $user    = $request->user();
        $ownerId = $user->kitchenOwnerId();

        $productId  = $request->input('product_id');
        $locationId = $request->input('location_id');
        $status     = $request->input('status');      // low / normal / null
        $sort       = $request->input('sort', 'expires_at');
        $direction  = $request->input('direction', 'asc');

        $query = StockItem::with(['product', 'location'])
            ->where('user_id', $ownerId);

        if (!empty($productId)) {
            $query->where('product_id', $productId);
        }

        if (!empty($locationId)) {
            $query->where('location_id', $locationId);
        }

        if ($status === 'low') {
            $query->whereNotNull('min_quantity')
                ->whereColumn('quantity', '<', 'min_quantity');
        } elseif ($status === 'normal') {
            $query->where(function ($q) {
                $q->whereNull('min_quantity')
                    ->orWhereColumn('quantity', '>=', 'min_quantity');
            });
        }

        if (!in_array($sort, ['expires_at', 'quantity'], true)) {
            $sort = 'expires_at';
        }

        if (!in_array($direction, ['asc', 'desc'], true)) {
            $direction = 'asc';
        }

        $stockItems = (clone $query)
            ->orderBy($sort, $direction)
            ->orderBy('id')
            ->paginate(25)
            ->withQueryString();

        $lowStockItems = (clone $query)
            ->whereNotNull('min_quantity')
            ->whereColumn('quantity', '<', 'min_quantity')
            ->orderBy('id')
            ->get();

        $owner = $user->kitchenOwner();

        $products = Cache::remember(
            "products:list:owner:{$ownerId}",
            300,
            function () use ($owner) {
                return $owner->products()
                    ->orderBy('name')
                    ->get();
            }
        );

        $locations = Cache::remember(
            "locations:list:owner:{$ownerId}",
            300,
            function () use ($owner) {
                return $owner->locations()
                    ->orderBy('sort_order')
                    ->orderBy('name')
                    ->get();
            }
        );

        return Inertia::render('Stock/Index', [
            'stockItems' => $stockItems,
            'lowStockItems' => $lowStockItems,
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
        $user  = $request->user();
        $owner = $user->kitchenOwner();
        $ownerId = $user->kitchenOwnerId();

        $products = Cache::remember(
            "products:list:owner:{$ownerId}",
            300,
            function () use ($owner) {
                return $owner->products()->orderBy('name')->get();
            }
        );

        $locations = Cache::remember(
            "locations:list:owner:{$ownerId}",
            300,
            function () use ($owner) {
                return $owner->locations()->orderBy('sort_order')->orderBy('name')->get();
            }
        );

        return Inertia::render('Stock/Form', [
            'mode'      => 'create',
            'stockItem' => null,
            'products'  => $products,
            'locations' => $locations,
        ]);
    }

    /**
     * Guardar nuevo registro.
     */
    public function store(StockItemRequest $request): RedirectResponse
    {
        $user    = $request->user();
        $ownerId = $user->kitchenOwnerId();

        $data = $request->validated();
        $data['user_id'] = $ownerId;

        StockItem::create($data);

        return redirect()
            ->route('stock.index')
            ->with('success', 'Stock guardado correctamente.');
    }

    /**
     * SHOW: evita 405 en GET/HEAD /stock/{id}
     */
    public function show(Request $request, StockItem $stockItem)
    {
        $ownerId = $request->user()->kitchenOwnerId();

        if ($stockItem->user_id !== $ownerId) {
            abort(403);
        }

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
        $ownerId = $request->user()->kitchenOwnerId();

        if ($stockItem->user_id !== $ownerId) {
            abort(403);
        }

        $owner = $request->user()->kitchenOwner();

        $products = Cache::remember(
            "products:list:owner:{$ownerId}",
            300,
            function () use ($owner) {
                return $owner->products()->orderBy('name')->get();
            }
        );

        $locations = Cache::remember(
            "locations:list:owner:{$ownerId}",
            300,
            function () use ($owner) {
                return $owner->locations()->orderBy('sort_order')->orderBy('name')->get();
            }
        );

        return Inertia::render('Stock/Form', [
            'mode'      => 'edit',
            'stockItem' => $stockItem->load(['product', 'location']),
            'products'  => $products,
            'locations' => $locations,
        ]);
    }

    /**
     * Actualizar registro.
     */
    public function update(StockItemRequest $request, StockItem $stockItem): RedirectResponse
    {
        $ownerId = $request->user()->kitchenOwnerId();

        if ($stockItem->user_id !== $ownerId) {
            abort(403);
        }

        $data = $request->validated();
        $stockItem->update($data);

        return redirect()
            ->route('stock.index')
            ->with('success', 'Stock actualizado correctamente.');
    }

    /**
     * Borrar registro.
     */
    public function destroy(Request $request, StockItem $stockItem): RedirectResponse
    {
        $ownerId = $request->user()->kitchenOwnerId();

        if ($stockItem->user_id !== $ownerId) {
            abort(403);
        }

        $stockItem->delete();

        return redirect()
            ->route('stock.index')
            ->with('success', 'Stock eliminado correctamente.');
    }

    /**
     * Exportar CSV de reposición (bajo mínimo).
     */
    public function exportMissingToCsv(Request $request): StreamedResponse
    {
        $ownerId = $request->user()->kitchenOwnerId();

        $productId  = $request->query('product_id');
        $locationId = $request->query('location_id');

        $query = StockItem::with(['product', 'location'])
            ->where('user_id', $ownerId)
            ->whereNotNull('min_quantity')
            ->whereColumn('quantity', '<', 'min_quantity');

        if (!empty($productId)) {
            $query->where('product_id', $productId);
        }

        if (!empty($locationId)) {
            $query->where('location_id', $locationId);
        }

        $items = $query->orderBy('id')->get();

        $filename = 'reposicion_bajo_minimo.csv';

        return response()->streamDownload(function () use ($items) {
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

            foreach ($items as $item) {
                $missing = max(0, (float)($item->min_quantity ?? 0) - (float)($item->quantity ?? 0));

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
        $ownerId = $request->user()->kitchenOwnerId();

        $productId  = $request->query('product_id');
        $locationId = $request->query('location_id');

        $query = StockItem::with(['product', 'location'])
            ->where('user_id', $ownerId)
            ->whereNotNull('min_quantity')
            ->whereColumn('quantity', '<', 'min_quantity');

        if (!empty($productId)) {
            $query->where('product_id', $productId);
        }

        if (!empty($locationId)) {
            $query->where('location_id', $locationId);
        }

        $items = $query->orderBy('id')->get();

        $pdf = Pdf::loadView('pdf.replenishment', [
            'items'       => $items,
            'user'        => $request->user(),
            'generatedAt' => now(),
        ])->setPaper('a4', 'portrait');

        return $pdf->download('reposicion_bajo_minimo.pdf');
    }
}
