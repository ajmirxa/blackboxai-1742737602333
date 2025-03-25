<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class IngredientController extends Controller
{
    /**
     * Display a listing of the ingredients.
     */
    public function index()
    {
        $ingredients = Ingredient::with(['inventories' => function($query) {
            $query->hasStock()->fifo();
        }])->get();

        return view('ingredients.index', compact('ingredients'));
    }

    /**
     * Show the form for creating a new ingredient.
     */
    public function create()
    {
        return view('ingredients.create');
    }

    /**
     * Store a newly created ingredient in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:ingredients,name|max:255',
            'unit' => 'required|string|max:50',
            'description' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();
            
            $ingredient = Ingredient::create($validated);
            
            DB::commit();

            return redirect()
                ->route('ingredients.index')
                ->with('success', 'Ingredient created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Error creating ingredient. ' . $e->getMessage());
        }
    }

    /**
     * Display the specified ingredient.
     */
    public function show(Ingredient $ingredient)
    {
        $ingredient->load(['inventories' => function($query) {
            $query->hasStock()->fifo();
        }]);

        return view('ingredients.show', compact('ingredient'));
    }

    /**
     * Show the form for editing the specified ingredient.
     */
    public function edit(Ingredient $ingredient)
    {
        return view('ingredients.edit', compact('ingredient'));
    }

    /**
     * Update the specified ingredient in storage.
     */
    public function update(Request $request, Ingredient $ingredient)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('ingredients')->ignore($ingredient->id),
            ],
            'unit' => 'required|string|max:50',
            'description' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();
            
            $ingredient->update($validated);
            
            DB::commit();

            return redirect()
                ->route('ingredients.index')
                ->with('success', 'Ingredient updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Error updating ingredient. ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified ingredient from storage.
     */
    public function destroy(Ingredient $ingredient)
    {
        try {
            DB::beginTransaction();
            
            // Check if ingredient is being used in any pizzas
            if ($ingredient->pizzas()->exists()) {
                throw new \Exception('Cannot delete ingredient that is used in pizzas.');
            }
            
            $ingredient->delete();
            
            DB::commit();

            return redirect()
                ->route('ingredients.index')
                ->with('success', 'Ingredient deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error deleting ingredient. ' . $e->getMessage());
        }
    }

    /**
     * Get ingredient details with current stock levels and prices
     */
    public function getDetails(Ingredient $ingredient)
    {
        $ingredient->load(['inventories' => function($query) {
            $query->hasStock()->fifo();
        }]);

        return response()->json([
            'ingredient' => $ingredient,
            'current_cost' => $ingredient->current_cost,
            'latest_inventory' => $ingredient->latest_inventory
        ]);
    }
}
