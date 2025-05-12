<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ingredient;
use App\Models\Recipe;

class HomeController extends Controller
{
    public function index()
    {
        return view('home');
    }

    /**
     * Captura el término de búsqueda y muestra resultados.
     */
    public function search(Request $request)
    {
        $q = $request->query('q', '');

        // Ejemplo básico: buscar en títulos de recetas e ingredientes
        $recipes = Recipe::where('title', 'like', "%{$q}%")->get();
        $ingredients = Ingredient::where('name', 'like', "%{$q}%")->get();

        return view('search.results', compact('q', 'recipes', 'ingredients'));
    }
}
