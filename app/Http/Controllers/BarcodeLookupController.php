<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BarcodeLookupController extends Controller
{
    public function lookup(Request $request)
    {
        $validated = $request->validate([
            'barcode' => ['required', 'string', 'min:6'],
        ]);

        $rawBarcode = $validated['barcode'] ?? '';

        // Normalizamos: solo dígitos (EAN-8, EAN-13, etc.)
        $barcode = preg_replace('/\D+/', '', $rawBarcode);

        if ($barcode === '') {
            return response()->json([
                'success' => false,
                'message' => 'El código de barras no es válido.',
            ], 422);
        }

        $user = $request->user();

        $ownerId = method_exists($user, 'kitchenOwnerId')
            ? $user->kitchenOwnerId()
            : $user->id;

        // 1) Si ya existe en tu base de datos, usamos ese producto
        $localProduct = Product::where('user_id', $ownerId)
            ->where('barcode', $barcode)
            ->first();

        if ($localProduct) {
            return response()->json([
                'success' => true,
                'source'  => 'local',
                'data'    => [
                    'barcode'           => $localProduct->barcode,
                    'name'              => $localProduct->name,
                    'default_quantity'  => $localProduct->default_quantity,
                    'default_unit'      => $localProduct->default_unit,
                    'default_pack_size' => $localProduct->default_pack_size,
                ],
            ]);
        }

        // 2) Intentamos buscarlo en Open Food Facts (ideal para productos envasados)
        try {
            $response = Http::timeout(7)
                ->acceptJson()
                ->get("https://world.openfoodfacts.org/api/v2/product/{$barcode}.json", [
                    'fields' => 'code,product_name,product_name_es,brands,quantity,serving_size',
                ]);

            if (! $response->ok()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se pudo consultar la base de datos externa.',
                ], 502);
            }

            $json = $response->json();

            if (($json['status'] ?? 0) !== 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron datos para este código.',
                ], 404);
            }

            $product = $json['product'] ?? [];

            $nameEs = $product['product_name_es'] ?? null;
            $name   = $nameEs ?: ($product['product_name'] ?? null);

            $brands = $product['brands'] ?? null;

            if ($name && $brands) {
                // Ejemplo: "Leche entera (Marca X)"
                $name = trim($name . ' (' . $brands . ')');
            }

            $quantityStr = $product['quantity'] ?? null;

            [$defaultQuantity, $defaultUnit, $defaultPackSize] = $this->parseQuantity($quantityStr);

            if (! $name && ! $defaultQuantity && ! $defaultUnit && ! $defaultPackSize) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay datos útiles para este código.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'source'  => 'openfoodfacts',
                'data'    => [
                    'barcode'           => $barcode,
                    'name'              => $name,
                    'default_quantity'  => $defaultQuantity,
                    'default_unit'      => $defaultUnit,
                    'default_pack_size' => $defaultPackSize,
                ],
            ]);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Error consultando la información del producto.',
            ], 500);
        }
    }

    /**
     * Intenta extraer cantidad, unidad y tamaño de pack a partir de un texto tipo:
     *  - "1 L"
     *  - "500 g"
     *  - "6 x 33 cl"
     *  - "4 x 125 g"
     */
    protected function parseQuantity(?string $quantityStr): array
    {
        $quantityStr = trim(mb_strtolower($quantityStr ?? ''));

        if ($quantityStr === '') {
            return [null, null, null];
        }

        // Multipack: "6 x 33 cl", "4x125 g", etc.
        if (preg_match('/(\d+)\s*[x×]\s*(\d+(?:[.,]\d+)?)\s*(ml|l|cl|g|kg)\b/i', $quantityStr, $m)) {
            $packCount = (int) $m[1];
            $perQty    = (float) str_replace(',', '.', $m[2]);
            $unitRaw   = strtolower($m[3]);

            [$perQty, $unit] = $this->normalizeUnit($perQty, $unitRaw);

            $perQtyStr = rtrim(rtrim(number_format($perQty, 2, '.', ''), '0'), '.');

            $packSize = sprintf('%d x %s %s', $packCount, $perQtyStr, $unit);

            // Devolvemos la cantidad por unidad y el tamaño de pack como texto
            return [$perQty, $unit, $packSize];
        }

        // Simple: "500 g", "1 l", "330ml"
        if (preg_match('/(\d+(?:[.,]\d+)?)\s*(ml|l|cl|g|kg)\b/i', $quantityStr, $m)) {
            $qty     = (float) str_replace(',', '.', $m[1]);
            $unitRaw = strtolower($m[2]);

            [$qty, $unit] = $this->normalizeUnit($qty, $unitRaw);

            return [$qty, $unit, null];
        }

        // No pudimos parsear nada "numérico": lo devolvemos como tamaño de pack
        return [null, null, $quantityStr];
    }

    /**
     * Normaliza unidades a las que usas en el front:
     *  - ml, L, g, kg...
     */
    protected function normalizeUnit(float $qty, string $unitRaw): array
    {
        $unitRaw = strtolower($unitRaw);

        switch ($unitRaw) {
            case 'ml':
                return [$qty, 'ml'];

            case 'cl':
                // 1 cl = 10 ml
                return [$qty * 10.0, 'ml'];

            case 'l':
                return [$qty, 'L'];

            case 'kg':
                return [$qty, 'kg'];

            case 'g':
                return [$qty, 'g'];

            default:
                return [$qty, $unitRaw];
        }
    }
}
