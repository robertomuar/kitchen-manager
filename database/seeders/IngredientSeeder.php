<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ingredient;

class IngredientSeeder extends Seeder
{
    public function run()
    {
        $ingredientes = [
            ['name'=>'Harina',       'quantity'=>1.5, 'unit'=>'kg',  'expires_at'=>now()->addMonths(6)],
            ['name'=>'Azúcar',       'quantity'=>0.8, 'unit'=>'kg',  'expires_at'=>now()->addMonths(12)],
            ['name'=>'Huevos',       'quantity'=>12,  'unit'=>'uds', 'expires_at'=>now()->addWeeks(2)],
            ['name'=>'Leche',        'quantity'=>2,   'unit'=>'L',   'expires_at'=>now()->addDays(7)],
            ['name'=>'Mantequilla',  'quantity'=>0.5, 'unit'=>'kg',  'expires_at'=>now()->addMonths(3)],
        ];

        foreach ($ingredientes as $data) {
            Ingredient::create($data);
        }
    }
}
