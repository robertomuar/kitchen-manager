<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use Illuminate\Http\Request;

class ShoppingListController extends Controller
{
    /**
     * Muestra la lista de la compra: ingredientes con cantidad < mínimo.
     */
    public function index()
    {
        $items = Ingredient::whereColumn('quantity', '<', 'min_quantity')
                           ->orderBy('name')
                           ->get();

        return view('ingredients.shopping-list', compact('items'));
    }
}
