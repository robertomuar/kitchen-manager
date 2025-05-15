<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\ShoppingListController;
use App\Models\Ingredient;
use App\Http\Controllers\DashboardController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Aquí defines las rutas de tu aplicación.
|
*/

// 1) Home y búsqueda (públicas)
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/search', [HomeController::class, 'search'])->name('search');

// 2) Dashboard (protegido)
Route::get('/dashboard', function () {
    $ingCount = Ingredient::count();
    $recCount = \App\Models\Recipe::count();
    return view('dashboard', compact('ingCount', 'recCount'));
})->middleware(['auth', 'verified'])->name('dashboard');

// 3) Rutas públicas de Recetas
Route::get('recipes', [RecipeController::class, 'index'])
     ->name('recipes.index');
Route::get('recipes/{recipe}', [RecipeController::class, 'show'])
     ->name('recipes.show');

// 4) Rutas protegidas (requieren login)
Route::middleware('auth')->group(function () {
    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // CRUD Ingredientes (TODOS requieren login)
    Route::resource('ingredients', IngredientController::class);

    // Recetas (solo create, store, edit, update, destroy requieren login)
    Route::get('recipes/create', [RecipeController::class, 'create'])->name('recipes.create');
    Route::post('recipes', [RecipeController::class, 'store'])->name('recipes.store');
    Route::get('recipes/{recipe}/edit', [RecipeController::class, 'edit'])->name('recipes.edit');
    Route::put('recipes/{recipe}', [RecipeController::class, 'update'])->name('recipes.update');
    Route::delete('recipes/{recipe}', [RecipeController::class, 'destroy'])->name('recipes.destroy');

    // Lista de la compra
    Route::get('/shopping-list', [ShoppingListController::class, 'index'])
         ->name('shopping-list');
});
Route::get('/dashboard', [DashboardController::class, 'index'])
     ->middleware(['auth','verified'])
     ->name('dashboard');
// 5) Rutas de autenticación (login, register, password, etc.)
require __DIR__.'/auth.php';