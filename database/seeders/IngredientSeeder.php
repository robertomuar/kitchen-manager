<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ingredient;

class IngredientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $samples = [
            ['name' => 'Harina',   'quantity' => 1.5, 'unit' => 'kg', 'location' => 'Armario de dentro',   'expires_at' => now()->addMonths(6)],
            ['name' => 'Azúcar',   'quantity' => 2,   'unit' => 'kg', 'location' => 'Armario de la terraza','expires_at' => now()->addYear()],
            ['name' => 'Leche',    'quantity' => 1,   'unit' => 'l',  'location' => 'Frigorífico',         'expires_at' => now()->addDays(7)],
            ['name' => 'Mantequilla','quantity'=>0.5, 'unit' => 'kg', 'location' => 'Congelador',          'expires_at' => now()->addMonths(2)],
            ['name' => 'Sal',      'quantity' => 1,   'unit' => 'kg', 'location' => 'Armario fregadero',   'expires_at' => null],
            ['name' => 'Pasta',    'quantity' => 3,   'unit' => 'paquetes', 'location' => 'Armario bajo','expires_at' => now()->addMonths(12)],
        ];

        foreach ($samples as $data) {
            Ingredient::create($data);
        }
    }
}
