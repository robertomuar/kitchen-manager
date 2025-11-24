<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BarcodeLookupController extends Controller
{
    /**
     * Endpoint para consultar un código de barras en OpenFoodFacts
     * y devolver un JSON simplificado para el frontend.
     */
    public function __invoke(Request $request, string $barcode)
    {
        $barcode = trim($barcode);

        if ($barcode === '' || strlen($barcode) < 6) {
            return response()->json([
                'found'   => false,
                'message' => 'invalid_barcode',
            ], 422);
        }

        try {
            $response = Http::timeout(8)
                ->acceptJson()
                ->get("https://world.openfoodfacts.org/api/v0/product/{$barcode}.json");
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'found'   => false,
                'message' => 'request_error',
            ], 500);
        }

        if (! $response->ok()) {
            return response()->json([
                'found'   => false,
                'message' => 'http_' . $response->status(),
            ], 502);
        }

        $json = $response->json();

        if (($json['status'] ?? 0) !== 1 || empty($json['product'])) {
            return response()->json([
                'found'   => false,
                'message' => 'not_found',
            ]);
        }

        $p = $json['product'];

        // Intentamos separar valor y unidad a partir de "1 L", "500 g", etc.
        $parsedQuantity = $this->parseQuantity($p['quantity'] ?? null);

        return response()->json([
            'found'          => true,
            'name'           => $p['product_name']        ?? null,
            'brands'         => $p['brands']              ?? null,
            'generic_name'   => $p['generic_name']        ?? null,
            'quantity'       => $p['quantity']            ?? null,
            'packaging'      => $p['packaging']           ?? null,
            'quantity_value' => $parsedQuantity['value']  ?? null,
            'quantity_unit'  => $parsedQuantity['unit']   ?? null,
        ]);
    }

    /**
     * Intenta extraer número y unidad de un texto tipo "1 L", "500 g", etc.
     */
    protected function parseQuantity(?string $raw): array
    {
        if (! $raw) {
            return ['value' => null, 'unit' => null];
        }

        if (preg_match('/([\d.,]+)\s*([a-zA-Z]+)/', $raw, $m)) {
            $value = (float) str_replace(',', '.', $m[1]);
            $unit  = strtolower($m[2]);

            return ['value' => $value, 'unit' => $unit];
        }

        return ['value' => null, 'unit' => null];
    }
}
