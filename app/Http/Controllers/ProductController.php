<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    /**
     * Listado de productos + filtros.
     */
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

    /**
     * Formulario de creación.
     */
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

    /**
     * Guardar nuevo producto.
     */
    public function store(ProductRequest $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validated();
        $data['user_id'] = $user->id;

        // Evitar problemas si la columna default_quantity es NOT NULL
        if (!array_key_exists('default_quantity', $data) || $data['default_quantity'] === null || $data['default_quantity'] === '') {
            $data['default_quantity'] = 0;
        }

        Product::create($data);

        return redirect()
            ->route('products.index')
            ->with('success', 'Producto creado correctamente.');
    }

    /**
     * Formulario de edición.
     */
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

    /**
     * Actualizar producto.
     */
    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        $user = $request->user();

        if ($product->user_id !== $user->id) {
            abort(403);
        }

        $data = $request->validated();

        if (!array_key_exists('default_quantity', $data) || $data['default_quantity'] === null || $data['default_quantity'] === '') {
            $data['default_quantity'] = 0;
        }

        $product->update($data);

        return redirect()
            ->route('products.index')
            ->with('success', 'Producto actualizado correctamente.');
    }

    /**
     * Borrar producto.
     */
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

    /**
     * 👉 Buscar datos reales de un producto a partir del código de barras.
     * Usa la API pública de OpenFoodFacts.
     */
    public function lookupByBarcode(Request $request)
    {
        $request->validate([
            'barcode' => ['required', 'string', 'max:50'],
        ]);

        $barcode = trim($request->input('barcode'));

        try {
            $response = Http::timeout(8)->get(
                'https://world.openfoodfacts.org/api/v2/product/' . $barcode . '.json'
            );

            if (!$response->ok()) {
                return response()->json([
                    'found'   => false,
                    'message' => 'No se ha podido consultar la base de datos externa.',
                ], 422);
            }

            $body = $response->json();

            if (($body['status'] ?? 0) !== 1 || empty($body['product'])) {
                return response()->json([
                    'found'   => false,
                    'message' => 'Producto no encontrado para este código de barras.',
                ], 404);
            }

            $p = $body['product'];

            $name         = $p['product_name_es'] ?? $p['product_name'] ?? null;
            $quantityText = $p['quantity'] ?? null;   // Ej: "1 L", "500 g"
            $brands       = $p['brands'] ?? null;

            [$normalizedQuantity, $normalizedUnit] = $this->parseQuantityAndUnitFromText($quantityText);

            return response()->json([
                'found' => true,
                'raw'   => [
                    'name'     => $name,
                    'quantity' => $quantityText,
                    'brands'   => $brands,
                ],
                'suggested' => [
                    'name'             => $name,
                    'default_quantity' => $normalizedQuantity,
                    'default_unit'     => $normalizedUnit,
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'found'   => false,
                'message' => 'Error al consultar el producto: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * 👉 Intenta extraer cantidad + unidad de un texto tipo "1 L", "500 g"...
     */
    protected function parseQuantityAndUnitFromText(?string $text): array
    {
        if (!$text) {
            return [null, null];
        }

        $normalized = preg_replace('/\s+/', ' ', trim($text));

        if (!preg_match('/([\d\.,]+)\s*(ml|l|cl|g|kg)/i', $normalized, $m)) {
            return [null, null];
        }

        $value = (float) str_replace(',', '.', $m[1]);
        $unit  = strtolower($m[2]);

        // Normalizamos a las unidades que manejas en el formulario
        if ($unit === 'ml') {
            return [$value, 'ml'];
        }

        if ($unit === 'cl') {
            // 1 cl = 10 ml
            return [$value * 10, 'ml'];
        }

        if ($unit === 'l') {
            return [$value, 'L'];
        }

        if ($unit === 'kg') {
            return [$value, 'kg'];
        }

        if ($unit === 'g') {
            return [$value, 'g'];
        }

        return [null, null];
    }
}
