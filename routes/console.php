<?php

use App\Models\StockItem;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('km:convert-stock-to-units', function () {
    $this->info('Convirtiendo stock a unidades...');

    $converted = 0;
    $skipped = [];

    StockItem::with('product')->chunk(200, function ($items) use (&$converted, &$skipped) {
        foreach ($items as $item) {
            $product = $item->product;

            if (!$product) {
                $skipped[] = "#{$item->id}: sin producto asociado";
                continue;
            }

            $defaultQuantity = (float) ($product->default_quantity ?? 0);

            if ($defaultQuantity <= 0) {
                $skipped[] = "#{$item->id}: default_quantity inválida";
                continue;
            }

            if ($item->unit !== $product->default_unit) {
                $skipped[] = "#{$item->id}: unidad distinta ({$item->unit} vs {$product->default_unit})";
                continue;
            }

            $quantity = (float) ($item->quantity ?? 0);
            $ratio = $quantity / $defaultQuantity;
            $roundedRatio = round($ratio);

            if (abs($ratio - $roundedRatio) > 1e-6) {
                $skipped[] = "#{$item->id}: cantidad {$quantity} no divisible por {$defaultQuantity}";
                continue;
            }

            $item->quantity = $roundedRatio;
            $item->unit = 'unidad';

            if ($item->min_quantity !== null) {
                $item->min_quantity = $item->min_quantity / $defaultQuantity;
            }

            $item->save();
            $converted++;
        }
    });

    $this->info("Convertidos: {$converted}");

    if (count($skipped)) {
        $this->warn('Registros no convertidos:');
        foreach ($skipped as $note) {
            $this->line(" - {$note}");
        }
    } else {
        $this->info('Todos los registros se convirtieron correctamente.');
    }
})->purpose('Convierte el stock a unidades usando la configuración del producto.');
