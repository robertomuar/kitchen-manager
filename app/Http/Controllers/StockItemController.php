<?php

namespace App\Http\Controllers;

use App\Http\Requests\StockItemRequest;
use App\Models\StockItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

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

        // Filtrar por estado (bajo mínimo / normal)
        if ($status === 'low') {
            $query->whereNotNull('min_quantity')
                ->whereColumn('quantity', '<', 'min_quantity');
        } elseif ($status === 'normal') {
            $query->where(function ($q) {
                $q->whereNull('min_quantity')
                    ->orWhereColumn('quantity', '>=', 'min_quantity');
            });
        }

        // Campos válidos para ordenar
        if (!in_array($sort, ['expires_at', 'quantity'], true)) {
            $sort = 'expires_at';
        }

        if (!in_array($direction, ['asc', 'desc'], true)) {
            $direction = 'asc';
        }

        $stockItems = $query
            ->orderBy($sort, $direction)
            ->orderBy('id')
            ->get();

        $owner = $user->kitchenOwner();

        $products = $owner->products()
            ->orderBy('name')
            ->get();

        $locations = $owner->locations()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

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
     * Formulario de creación de registro de stock.
     */
    public function create(Request $request): Response
    {
        $user  = $request->user();
        $owner = $user->kitchenOwner();

        $products = $owner->products()
            ->orderBy('name')
            ->get();

        $locations = $owner->locations()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return Inertia::render('Stock/Form', [
            'mode'      => 'create',
            'stockItem' => null,
            'products'  => $products,
            'locations' => $locations,
        ]);
    }

    /**
     * Guardar nuevo registro de stock.
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
     * Formulario de edición de registro de stock.
     */
    public function edit(Request $request, StockItem $stockItem): Response
    {
        $ownerId = $request->user()->kitchenOwnerId();

        if ($stockItem->user_id !== $ownerId) {
            abort(403);
        }

        $owner = $request->user()->kitchenOwner();

        $products = $owner->products()
            ->orderBy('name')
            ->get();

        $locations = $owner->locations()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return Inertia::render('Stock/Form', [
            'mode'      => 'edit',
            'stockItem' => $stockItem->load(['product', 'location']),
            'products'  => $products,
            'locations' => $locations,
        ]);
    }

    /**
     * Actualizar registro de stock.
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
     * Borrar registro de stock.
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
     * Exportar la lista de reposición (bajo mínimo) a CSV.
     */
    public function exportReplenishment(Request $request)
    {
        $user    = $request->user();
        $ownerId = $user->kitchenOwnerId();

        $query = StockItem::with(['product', 'location'])
            ->where('user_id', $ownerId)
            ->whereNotNull('min_quantity')
            ->whereColumn('quantity', '<', 'min_quantity');

        // Respetar filtros (producto / ubicación)
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->integer('product_id'));
        }

        if ($request->filled('location_id')) {
            $query->where('location_id', $request->integer('location_id'));
        }

        $items = $query
            ->orderBy('product_id')
            ->get();

        $fileName = 'lista_reposicion_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        $callback = static function () use ($items) {
            $handle = fopen('php://output', 'w');

            // BOM para Excel
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Cabeceras
            fputcsv($handle, [
                'Producto',
                'Ubicación',
                'Cantidad actual',
                'Unidad',
                'Mínimo',
                'Falta aprox.',
                'Caducidad',
            ], ';');

            foreach ($items as $item) {
                $quantity = $item->quantity ?? 0;
                $min      = $item->min_quantity ?? 0;
                $missing  = max(0, $min - $quantity);

                fputcsv($handle, [
                    optional($item->product)->name,
                    optional($item->location)->name,
                    $quantity,
                    $item->unit,
                    $min,
                    number_format($missing, 2, ',', ''),
                    optional($item->expires_at)?->format('Y-m-d'),
                ], ';');
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
