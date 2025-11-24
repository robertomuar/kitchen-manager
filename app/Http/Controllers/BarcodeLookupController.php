<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BarcodeLookupController extends Controller
{
    /**
     * Devuelve info básica del producto a partir del código de barras.
     * Usa la API pública de OpenFoodFacts.
     */
    public function lookup(Request $request, string $barcode)
    {
        // Limpiamos un poco el código (por si vienen espacios)
        $barcode = trim($barcode);

        if ($barcode === '') {
            return response()->json([
                'message' => 'Código vacío.',
            ], 422);
        }

        // Llamada a OpenFoodFacts
        $response = Http::acceptJson()
            ->get("https://world.openfoodfacts.org/api/v0/product/{$barcode}.json");

        if (! $response->successful()) {
            return response()->json([
                'message' => 'No se pudo consultar la API externa.',
            ], 502);
        }

        $json = $response->json();

        if (($json['status'] ?? 0) !== 1) {
            return response()->json([
                'message' => 'Producto no encontrado en la base de datos pública.',
            ], 404);
        }

        $product = $json['product'] ?? [];

        $name        = $product['product_name'] ?? null;
        $quantityRaw = $product['quantity'] ?? null;

        $defaultQuantity = null;
        $defaultUnit     = null;

        if ($quantityRaw) {
            // Ejemplos típicos: "500 g", "1 L", "33 cl"
            // Sacamos número y dejamos el resto como unidad
            if (preg_match('/([\d.,]+)/', $quantityRaw, $m)) {
                $number = str_replace(',', '.', $m[1]);
                $defaultQuantity = is_numeric($number) ? (float) $number : null;

                $unitPart = trim(str_replace($m[1], '', $quantityRaw));
                $defaultUnit = $unitPart !== '' ? $unitPart : null;
            }
        }

        return response()->json([
            'barcode'          => $barcode,
            'name'             => $name,
            'default_quantity' => $defaultQuantity,
            'default_unit'     => $defaultUnit,
        ]);
    }
}
