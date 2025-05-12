<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use Illuminate\Http\Request;

class IngredientController extends Controller
{
   public function index(Request $request)
{
    $query = Ingredient::orderBy('expires_at', 'asc');

    // Filtro: expirados, próximos a expirar (<7 días) o todos
    if ($request->filter === 'expired') {
        $query->where('expires_at', '<', now());
    } elseif ($request->filter === 'soon') {
        $query->whereBetween('expires_at', [now(), now()->addDays(7)]);
    }

    $ingredients = $query->paginate(10)->withQueryString();

    return view('ingredients.index', compact('ingredients'));
}


    public function create()
    {
        return view('ingredients.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'quantity'   => 'required|numeric',
            'unit'       => 'nullable|string|max:50',
            'expires_at' => 'nullable|date',
        ]);
        Ingredient::create($data);
        return redirect()->route('ingredients.index');
    }

    public function edit(Ingredient $ingredient)
    {
        return view('ingredients.edit', compact('ingredient'));
    }

    public function update(Request $request, Ingredient $ingredient)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'quantity'   => 'required|numeric',
            'unit'       => 'nullable|string|max:50',
            'expires_at' => 'nullable|date',
        ]);
        $ingredient->update($data);
        return redirect()->route('ingredients.index');
    }

    public function destroy(Ingredient $ingredient)
    {
        $ingredient->delete();
        return redirect()->route('ingredients.index');
    }
}
