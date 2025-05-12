<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\ShoppingListController;  // Importamos el controlador de lista de la compra
use App\Models\Ingredient;
use App\Models\Recipe;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Aquí defines las rutas de tu aplicación.
|
*/

// Página principal y búsqueda
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/search', [HomeController::class, 'search'])->name('search');

// Dashboard con conteos
Route::get('/dashboard', function () {
    $ingCount = Ingredient::count();
    $recCount = Recipe::count();
    return view('dashboard', compact('ingCount', 'recCount'));
})->middleware(['auth', 'verified'])->name('dashboard');

// Rutas protegidas
Route::middleware(['auth'])->group(function () {
    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // CRUD Ingredientes
    Route::resource('ingredients', IngredientController::class);

    // CRUD Recetas
    Route::resource('recipes', RecipeController::class);

    // Lista de la compra
    Route::get('/shopping-list', [ShoppingListController::class, 'index'])
         ->name('shopping-list');
});

require __DIR__.'/auth.php';
