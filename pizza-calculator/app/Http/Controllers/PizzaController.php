<?php

namespace App\Http\Controllers;

use App\Models\Pizza;
use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PizzaController extends Controller
{
    public function index()
    {
        $pizzas = Pizza::with('ingredients')->get();
        return view('pizzas.index', compact('pizzas'));
    }

    public function create()
    {
        $ingredients = Ingredient::all();
        return view('pizzas.create', compact('ingredients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'size' => 'required|string|in:small,medium,large',
            'base_price' => 'required|numeric|min:0',
            'ingredients' => 'required|array',
            'ingredients.*.id' => 'required|exists:ingredients,id',
            'ingredients.*.amount' => 'required|numeric|min:0.01',
        ]);

        $pizza = Pizza::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'size' => $validated['size'],
            'base_price' => $validated['base_price'],
            'is_active' => true,
        ]);

        foreach ($validated['ingredients'] as $ingredient) {
            $pizza->ingredients()->attach($ingredient['id'], [
                'amount_required' => $ingredient['amount'],
            ]);
        }

        return redirect()->route('pizzas.index')
            ->with('success', 'Pizza created successfully.');
    }

    public function show(Pizza $pizza)
    {
        $pizza->load('ingredients.inventories');
        return view('pizzas.show', compact('pizza'));
    }

    public function edit(Pizza $pizza)
    {
        $ingredients = Ingredient::all();
        return view('pizzas.edit', compact('pizza', 'ingredients'));
    }

    public function update(Request $request, Pizza $pizza)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'size' => 'required|string|in:small,medium,large',
            'base_price' => 'required|numeric|min:0',
            'ingredients' => 'required|array',
            'ingredients.*.id' => 'required|exists:ingredients,id',
            'ingredients.*.amount' => 'required|numeric|min:0.01',
        ]);

        $pizza->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'size' => $validated['size'],
            'base_price' => $validated['base_price'],
        ]);

        $pizza->ingredients()->detach();
        foreach ($validated['ingredients'] as $ingredient) {
            $pizza->ingredients()->attach($ingredient['id'], [
                'amount_required' => $ingredient['amount'],
            ]);
        }

        return redirect()->route('pizzas.index')
            ->with('success', 'Pizza updated successfully.');
    }

    public function destroy(Pizza $pizza)
    {
        $pizza->ingredients()->detach();
        $pizza->delete();

        return redirect()->route('pizzas.index')
            ->with('success', 'Pizza deleted successfully.');
    }

    public function calculator()
    {
        $pizzas = Pizza::with('ingredients')->where('is_active', true)->get();
        return view('pizzas.calculator', compact('pizzas'));
    }

    public function calculateCost(Pizza $pizza)
    {
        $pizza->load('ingredients.inventories');
        
        $ingredientCosts = [];
        $totalCost = 0;
        
        foreach ($pizza->ingredients as $ingredient) {
            $latestInventory = $ingredient->inventories()
                ->whereNotNull('purchase_price')
                ->orderByDesc('purchased_at')
                ->first();

            if ($latestInventory) {
                $cost = $ingredient->pivot->amount_required * $latestInventory->purchase_price;
                $totalCost += $cost;
                
                $ingredientCosts[] = [
                    'ingredient' => $ingredient->name,
                    'amount' => $ingredient->pivot->amount_required,
                    'unit' => $ingredient->unit,
                    'cost' => $cost
                ];
            }
        }

        $finalCost = $totalCost + $pizza->base_price;

        return response()->json([
            'base_price' => $pizza->base_price,
            'total_cost' => $totalCost,
            'final_cost' => $finalCost,
            'ingredient_costs' => $ingredientCosts
        ]);
    }

    public function toggleStatus(Pizza $pizza)
    {
        $pizza->update(['is_active' => !$pizza->is_active]);
        
        return response()->json([
            'status' => 'success',
            'is_active' => $pizza->is_active,
        ]);
    }
}
