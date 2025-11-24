<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BarcodeLookupController extends Controller
{
    public function lookup(Request $request)
    {
        $data = $request->validate([
            'barcode' => ['required', 'string', 'max:50'],
        ]);

        $barcode = $data['barcode'];

        try {
            // Ejemplo con OpenFoodFacts
            $response = Http::timeout(5)->get(
                'https://world.openfoodfacts.org/api/v2/product/' . $barcode
            );

            if (! $response->ok()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se pudo contactar con el servicio externo.',
                ], 502);
            }

            $payload = $response->json();

            if (($payload['status'] ?? 0) !== 1 || empty($payload['product'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se ha encontrado información para este código.',
                ], 404);
            }

            $product = $payload['product'];

            $name = $product['product_name_es']
                ?? $product['product_name']
                ?? null;

            $quantityStr = $product['quantity'] ?? null; // p.ej. "500 g"

            $defaultQuantity = null;
            $defaultUnit     = null;

            if ($quantityStr) {
                if (preg_match('/(\d+(?:[.,]\d+)?)\s*(\w+)/u', $quantityStr, $m)) {
                    $defaultQuantity = (float) str_replace(',', '.', $m[1]);
                    $defaultUnit     = strtolower($m[2]);
                }
            }

            return response()->json([
                'success' => true,
                'data'    => [
                    'name'               => $name,
                    'default_quantity'   => $defaultQuantity,
                    'default_unit'       => $defaultUnit,
                    'default_pack_size'  => null,
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error consultando el servicio externo.',
            ], 502);
        }
    }
}
