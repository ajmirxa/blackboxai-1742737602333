<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\PizzaController;

Route::get('/', function () {
    return redirect()->route('calculator');
});

// Calculator routes
Route::get('/calculator', [PizzaController::class, 'calculator'])->name('calculator');
Route::get('/pizzas/{pizza}/cost', [PizzaController::class, 'calculateCost'])->name('pizzas.cost');

// Resource routes
Route::resource('ingredients', IngredientController::class);
Route::resource('inventory', InventoryController::class);
Route::resource('pizzas', PizzaController::class);

// Additional pizza routes
Route::post('/pizzas/{pizza}/toggle', [PizzaController::class, 'toggleStatus'])->name('pizzas.toggle');

// API routes for calculator
Route::get('/api/pizzas/{pizza}/cost', [PizzaController::class, 'calculateCost'])->name('api.pizzas.cost');