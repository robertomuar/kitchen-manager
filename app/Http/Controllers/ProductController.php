<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        $search     = $request->input('search');
        $locationId = $request->input('location_id');
        $sort       = $request->input('sort', 'name');
        $direction  = $request->input('direction', 'asc');

        $query = Product::with('location')
            ->where('user_id', $user->id);

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
            ->get();

        $locations = $user->locations()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

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
        $user = $request->user();

        $locations = $user->locations()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return Inertia::render('Products/Form', [
            'mode'      => 'create',
            'product'   => null,
            'locations' => $locations,
        ]);
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validated();
        $data['user_id'] = $user->id;

        Product::create($data);

        return redirect()
            ->route('products.index')
            ->with('success', 'Producto creado correctamente.');
    }

    public function edit(Request $request, Product $product): Response
    {
        $user = $request->user();

        if ($product->user_id !== $user->id) {
            abort(403);
        }

        $locations = $user->locations()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return Inertia::render('Products/Form', [
            'mode'      => 'edit',
            'product'   => $product,
            'locations' => $locations,
        ]);
    }

    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        $user = $request->user();

        if ($product->user_id !== $user->id) {
            abort(403);
        }

        $data = $request->validated();

        $product->update($data);

        return redirect()
            ->route('products.index')
            ->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(Request $request, Product $product): RedirectResponse
    {
        $user = $request->user();

        if ($product->user_id !== $user->id) {
            abort(403);
        }

        $product->delete();

        return redirect()
            ->route('products.index')
            ->with('success', 'Producto eliminado correctamente.');
    }
}
