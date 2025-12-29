<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Location;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function index(Request $request): Response
    {
        [$owner, $ownerId, $kitchenId] = $this->resolveKitchenContext($request);

        $search     = $request->input('search');
        $locationId = $request->input('location_id');
        $sort       = $request->input('sort', 'name');
        $direction  = $request->input('direction', 'asc');

        $query = Product::with('location')
            ->where('user_id', $ownerId)
            ->where('kitchen_id', $kitchenId);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('notes', 'like', '%' . $search . '%')
                    ->orWhere('barcode', 'like', '%' . $search . '%');
            });
        }

        if (!empty($locationId)) {
            $query->where('location_id', $locationId);
        }

        if (!in_array($sort, ['name', 'default_unit'], true)) {
            $sort = 'name';
        }

        if (!in_array($direction, ['asc', 'desc'], true)) {
            $direction = 'asc';
        }

        $products = $query
            ->orderBy($sort, $direction)
            ->orderBy('id')
            ->paginate(25)
            ->withQueryString();

        $locations = Cache::remember("locations.list.{$ownerId}.{$kitchenId}", 300, function () use ($owner, $kitchenId) {
            return $owner->locations()
                ->orderBy('sort_order')
                ->orderBy('name')
                ->where('kitchen_id', $kitchenId)
                ->get();
        });

        return Inertia::render('Products/Index', [
            'products'  => $products,
            'locations' => $locations,
            'filters'   => [
                'search'      => $search ?? '',
                'location_id' => $locationId ? (string) $locationId : '',
                'sort'        => $sort,
                'direction'   => $direction,
            ],
        ]);
    }

    public function create(Request $request): Response
    {
        [$owner, $ownerId, $kitchenId] = $this->resolveKitchenContext($request);

        $locations = Cache::remember("locations.list.{$ownerId}.{$kitchenId}", 300, function () use ($owner, $kitchenId) {
            return $owner->locations()
                ->orderBy('sort_order')
                ->orderBy('name')
                ->where('kitchen_id', $kitchenId)
                ->get();
        });

        return Inertia::render('Products/Form', [
            'mode'      => 'create',
            'product'   => null,
            'locations' => $locations,
        ]);
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        [$owner, $ownerId, $kitchenId] = $this->resolveKitchenContext($request);

        $data = $request->validated();
        $data['user_id'] = $ownerId;
        $data['kitchen_id'] = $kitchenId;

        if (!empty($data['location_id'])) {
            $location = Location::where('id', $data['location_id'])
                ->where('user_id', $ownerId)
                ->where('kitchen_id', $kitchenId)
                ->firstOrFail();

            $data['location_id'] = $location->id;
        }

        Product::create($data);

        $this->forgetDashboardCache($ownerId, $kitchenId);

        return redirect()
            ->route('products.index')
            ->with('success', 'Producto creado correctamente.');
    }

    public function edit(Request $request, Product $product): Response
    {
        $this->authorize('view', $product);

        [$owner, $ownerId, $kitchenId] = $this->resolveKitchenContext($request);

        $locations = Cache::remember("locations.list.{$ownerId}.{$kitchenId}", 300, function () use ($owner, $kitchenId) {
            return $owner->locations()
                ->orderBy('sort_order')
                ->orderBy('name')
                ->where('kitchen_id', $kitchenId)
                ->get();
        });

        return Inertia::render('Products/Form', [
            'mode'      => 'edit',
            'product'   => $product,
            'locations' => $locations,
        ]);
    }

    /**
     * ✅ NUEVO: evita 405 en GET/HEAD /products/{id}
     * - Si es petición Inertia => Inertia::location a edit
     * - Si es AJAX/JSON real => JSON
     * - Si es navegación normal => redirect a edit
     */
    public function show(Request $request, Product $product)
    {
        $this->authorize('view', $product);

        if ($request->header('X-Inertia')) {
            return Inertia::location(route('products.edit', $product->id));
        }

        if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
            return response()->json($product);
        }

        return redirect()->route('products.edit', $product->id);
    }

    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        [$owner, $ownerId, $kitchenId] = $this->resolveKitchenContext($request);

        $this->authorize('update', $product);

        $data = $request->validated();

        if (!empty($data['location_id'])) {
            $location = Location::where('id', $data['location_id'])
                ->where('user_id', $ownerId)
                ->where('kitchen_id', $kitchenId)
                ->firstOrFail();

            $data['location_id'] = $location->id;
        }

        $product->update($data + [
            'user_id'    => $ownerId,
            'kitchen_id' => $kitchenId,
        ]);

        $this->forgetDashboardCache($ownerId, $kitchenId);

        return redirect()
            ->route('products.index')
            ->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(Request $request, Product $product): RedirectResponse
    {
        [$owner, $ownerId, $kitchenId] = $this->resolveKitchenContext($request);

        $this->authorize('delete', $product);

        $product->delete();

        $this->forgetDashboardCache($ownerId, $kitchenId);

        return redirect()
            ->route('products.index')
            ->with('success', 'Producto eliminado correctamente.');
    }

    /**
     * Opciones paginadas para selects asíncronos.
     */
    public function options(Request $request)
    {
        [$owner, $ownerId, $kitchenId] = $this->resolveKitchenContext($request);

        $search = trim((string) $request->query('search', ''));

        $query = Product::where('user_id', $ownerId)
            ->where('kitchen_id', $kitchenId);

        if ($search !== '') {
            $query->where('name', 'like', '%' . $search . '%');
        }

        return $query
            ->orderBy('name')
            ->paginate(15, [
                'id',
                'name',
                'default_quantity',
                'default_unit',
            ]);
    }
}
