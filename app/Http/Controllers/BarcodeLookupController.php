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

        $rawBarcode = trim($validated['barcode'] ?? '');

        // Normalizamos: solo dígitos (EAN-8, EAN-13, etc.)
        $numericBarcode = preg_replace('/\D+/', '', $rawBarcode);

        if ($rawBarcode === '' && $numericBarcode === '') {
            return response()->json([
                'success' => false,
                'message' => 'El código de barras no es válido.',
            ], 422);
        }

        // Lista de posibles barcodes a buscar en BD
        $candidateBarcodes = array_values(array_unique(array_filter([
            $rawBarcode,
            $numericBarcode,
        ])));

        $user = $request->user();
        $ownerId = method_exists($user, 'kitchenOwnerId')
            ? $user->kitchenOwnerId()
            : $user->id;

        // 1) Búsqueda LOCAL prioritaria: primero en productos del owner
        $localProduct = Product::query()
            ->where('user_id', $ownerId)
            ->where(function ($q) use ($candidateBarcodes) {
                foreach ($candidateBarcodes as $code) {
                    $q->orWhere('barcode', $code);
                }
            })
            ->first();

        // 1b) Si no hay nada para ese owner, buscamos en cualquier producto con ese barcode
        if (! $localProduct) {
            $localProduct = Product::query()
                ->where(function ($q) use ($candidateBarcodes) {
                    foreach ($candidateBarcodes as $code) {
                        $q->orWhere('barcode', $code);
                    }
                })
                ->first();
        }

        if ($localProduct) {
            return response()->json([
                'success' => true,
                'source'  => 'local',
                'data'    => [
                    'barcode'           => (string) $localProduct->barcode,
                    'name'              => $localProduct->name,
                    'default_quantity'  => $localProduct->default_quantity,
                    'default_unit'      => $localProduct->default_unit,
                    'default_pack_size' => $localProduct->default_pack_size,
                ],
            ]);
        }

        // Si llegamos aquí, en tu BD no hay nada con ese código.
        // 2) Intentamos en fuentes externas:
        //    - Open Food Facts  (alimentos)
        //    - Open Beauty Facts (cosmética / higiene)
        $external = $this->lookupExternalProduct($numericBarcode !== '' ? $numericBarcode : $rawBarcode);

        if (! $external) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontraron datos para este código. Rellena el producto a mano: quedará guardado para futuros escaneos.',
            ], 404);
        }

        [$name, $quantityStr, $brands] = $external;

        if ($name && $brands) {
            $name = trim($name . ' (' . $brands . ')');
        }

        [$defaultQuantity, $defaultUnit, $defaultPackSize] = $this->parseQuantity($quantityStr);

        if (! $name && ! $defaultQuantity && ! $defaultUnit && ! $defaultPackSize) {
            return response()->json([
                'success' => false,
                'message' => 'No hay datos útiles para este código. Rellena el producto a mano y quedará guardado.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'source'  => 'external',
            'data'    => [
                'barcode'           => $numericBarcode !== '' ? $numericBarcode : $rawBarcode,
                'name'              => $name,
                'default_quantity'  => $defaultQuantity,
                'default_unit'      => $defaultUnit,
                'default_pack_size' => $defaultPackSize,
            ],
        ]);
    }

    /**
     * Busca el producto en varias bases externas:
     *  - Open Food Facts  (alimentos)
     *  - Open Beauty Facts (cosmética / higiene)
     *
     * Devuelve [name, quantityStr, brands] o null.
     */
    protected function lookupExternalProduct(string $barcode): ?array
    {
        $barcode = trim($barcode);

        if ($barcode === '') {
            return null;
        }

        $projects = [
            [
                'label' => 'food',
                'base'  => 'https://world.openfoodfacts.org',
            ],
            [
                'label' => 'beauty',
                'base'  => 'https://world.openbeautyfacts.org',
            ],
        ];

        foreach ($projects as $project) {
            try {
                $response = Http::timeout(7)
                    ->acceptJson()
                    ->get($project['base'] . "/api/v2/product/{$barcode}.json", [
                        'fields' => 'code,product_name,product_name_es,brands,quantity',
                    ]);

                if (! $response->ok()) {
                    continue;
                }

                $json = $response->json();

                if (($json['status'] ?? 0) !== 1) {
                    continue;
                }

                $product = $json['product'] ?? [];

                $nameEs = $product['product_name_es'] ?? null;
                $name   = $nameEs ?: ($product['product_name'] ?? null);

                $quantityStr = $product['quantity'] ?? null;
                $brands      = $product['brands'] ?? null;

                if (! $name && ! $quantityStr && ! $brands) {
                    continue;
                }

                return [$name, $quantityStr, $brands];
            } catch (\Throwable $e) {
                report($e);
                continue;
            }
        }

        return null;
    }

    /**
     * Intenta extraer cantidad, unidad y tamaño de pack a partir de un texto tipo:
     *  - "1 L"
     *  - "500 g"
     *  - "6 x 33 cl"
     *  - "4 x 125 g"
     *  - "2 x 250 ml"
     */
    protected function parseQuantity(?string $quantityStr): array
    {
        $quantityStr = trim(mb_strtolower($quantityStr ?? ''));

        if ($quantityStr === '') {
            return [null, null, null];
        }

        // Multipack: "6 x 33 cl", "4x125 g", "2 x 250 ml", etc.
        if (preg_match('/(\d+)\s*[x×]\s*(\d+(?:[.,]\d+)?)\s*(ml|l|cl|g|kg)\b/i', $quantityStr, $m)) {
            $packCount = (int) $m[1];
            $perQty    = (float) str_replace(',', '.', $m[2]);
            $unitRaw   = strtolower($m[3]);

            [$perQty, $unit] = $this->normalizeUnit($perQty, $unitRaw);

            $perQtyStr = rtrim(rtrim(number_format($perQty, 2, '.', ''), '0'), '.');

            $packSize = sprintf('%d x %s %s', $packCount, $perQtyStr, $unit);

            return [$perQty, $unit, $packSize];
        }

        // Simple: "500 g", "1 l", "330ml", "200 ml"
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
