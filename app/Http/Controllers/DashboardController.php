<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Ingredient;
use App\Models\Recipe;

class DashboardController extends Controller
{
    public function __construct()
    {
        // Todas las acciones requieren auth
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();

        // Estadísticas
        $ingCount = Ingredient::count();
        $recCount = Recipe::count();

        return view('dashboard', compact('user','ingCount','recCount'));
    }
}
