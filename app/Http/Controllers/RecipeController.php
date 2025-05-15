<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\Ingredient;
use Illuminate\Http\Request;

class RecipeController extends Controller
{
    public function __construct()
    {
        // Ahora index (y show si lo añades) quedan públicos; el resto necesita auth
        $this->middleware('auth')->except(['index'/*, 'show'*/]);
    }

    public function index()
    {
        $recipes = Recipe::withCount('ingredients')
                         ->orderBy('title')
                         ->paginate(8);

        return view('recipes.index', compact('recipes'));
    }

    public function create()
    {
        $ingredients = Ingredient::orderBy('name')->get();
        return view('recipes.create', compact('ingredients'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'ingredients'  => 'required|array|min:1',
            'ingredients.*.id'       => 'exists:ingredients,id',
            'ingredients.*.quantity' => 'required|numeric|min:0.01',
            'ingredients.*.unit'     => 'nullable|string|max:50',
        ]);

        $recipe = Recipe::create([
            'title'       => $data['title'],
            'description' => $data['description'],
        ]);

        foreach ($data['ingredients'] as $ing) {
            $recipe->ingredients()->attach(
                $ing['id'],
                ['quantity' => $ing['quantity'], 'unit' => $ing['unit']]
            );
        }

        return redirect()->route('recipes.index');
    }

    public function edit(Recipe $recipe)
    {
        $ingredients = Ingredient::orderBy('name')->get();
        $recipe->load('ingredients');
        return view('recipes.edit', compact('recipe', 'ingredients'));
    }

    public function update(Request $request, Recipe $recipe)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'ingredients'  => 'required|array|min:1',
            'ingredients.*.id'       => 'exists:ingredients,id',
            'ingredients.*.quantity' => 'required|numeric|min:0.01',
            'ingredients.*.unit'     => 'nullable|string|max:50',
        ]);

        $recipe->update([
            'title'       => $data['title'],
            'description' => $data['description'],
        ]);

        $syncData = [];
        foreach ($data['ingredients'] as $ing) {
            $syncData[$ing['id']] = [
                'quantity' => $ing['quantity'],
                'unit'     => $ing['unit'],
            ];
        }
        $recipe->ingredients()->sync($syncData);

        return redirect()->route('recipes.index');
    }

    public function destroy(Recipe $recipe)
    {
        $recipe->delete();
        return redirect()->route('recipes.index');
    }
}
