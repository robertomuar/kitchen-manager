<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Recipe;

class RecipeSeeder extends Seeder
{
    public function run()
    {
        $r = Recipe::create([
            'title'       => 'Bizcocho Básico',
            'description' => 'Un bizcocho esponjoso con harina, azúcar, huevos y mantequilla.'
        ]);

        // Vinculamos ingredientes 1=Harina, 2=Azúcar, 3=Huevos, 5=Mantequilla
        $r->ingredients()->attach([
            1 => ['quantity'=>250, 'unit'=>'g'],
            2 => ['quantity'=>150, 'unit'=>'g'],
            3 => ['quantity'=>3,   'unit'=>'uds'],
            5 => ['quantity'=>100, 'unit'=>'g'],
        ]);
    }
}
