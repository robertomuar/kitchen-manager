<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class IngredientController extends Controller
{
    /**
     * Muestra el listado de ingredientes con filtrado y búsqueda.
     */
    public function index(Request $request)
{
    $filter = $request->input('filter');
    $search = $request->input('search');

    $query = Ingredient::query();

    // Filtrar expirados
    if ($filter === 'expired') {
        $query->whereNotNull('expires_at')
              ->where('expires_at', '<', now());
    }

    // Filtrar por expirar en los próximos 7 días
    if ($filter === 'soon') {
        $query->whereNotNull('expires_at')
              ->whereBetween('expires_at', [now(), now()->addDays(7)]);
    }

    // Filtrar ingredientes a comprar
    if ($filter === 'buy') {
        $query->whereColumn('quantity', '<', 'min_quantity');
    }

    // Búsqueda por nombre
    if ($search) {
        $query->where('name', 'like', '%' . $search . '%');
    }

    $ingredients = $query
        ->orderBy('name')
        ->paginate(10)
        ->withQueryString();

    return view('ingredients.index', compact('ingredients'));
}


    /**
     * Muestra el formulario de creación.
     */
    public function create()
    {
        return view('ingredients.create');
    }

    /**
     * Almacena un nuevo ingrediente.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'quantity'     => 'required|numeric|min:0',
            'min_quantity' => 'required|numeric|min:0',
            'unit'         => ['required', Rule::in(array_keys(Ingredient::units()))],
            'location'     => ['required', Rule::in(Ingredient::locations())],
            'expires_at'   => 'nullable|date',
        ]);

        Ingredient::create($data);

        return redirect()
            ->route('ingredients.index')
            ->with('success', 'Ingrediente creado correctamente.');
    }

    /**
     * Muestra el formulario de edición.
     */
    public function edit(Ingredient $ingredient)
    {
        return view('ingredients.edit', compact('ingredient'));
    }

    /**
     * Actualiza un ingrediente existente.
     */
    public function update(Request $request, Ingredient $ingredient)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'quantity'     => 'required|numeric|min:0',
            'min_quantity' => 'required|numeric|min:0',
            'unit'         => ['required', Rule::in(array_keys(Ingredient::units()))],
            'location'     => ['required', Rule::in(Ingredient::locations())],
            'expires_at'   => 'nullable|date',
        ]);

        $ingredient->update($data);

        return redirect()
            ->route('ingredients.index')
            ->with('success', 'Ingrediente actualizado correctamente.');
    }

    /**
     * Elimina un ingrediente.
     */
    public function destroy(Ingredient $ingredient)
    {
        $ingredient->delete();

        return back()->with('success', 'Ingrediente eliminado correctamente.');
    }
}
