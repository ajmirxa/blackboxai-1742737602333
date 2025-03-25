<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    /**
     * Display a listing of the inventory.
     */
    public function index()
    {
        $inventories = Inventory::with('ingredient')
            ->orderBy('purchased_at', 'desc')
            ->paginate(15);

        return view('inventory.index', compact('inventories'));
    }

    /**
     * Show the form for creating a new inventory record.
     */
    public function create()
    {
        $ingredients = Ingredient::orderBy('name')->get();
        return view('inventory.create', compact('ingredients'));
    }

    /**
     * Store a newly created inventory record in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ingredient_id' => 'required|exists:ingredients,id',
            'quantity' => 'required|numeric|min:0.01',
            'purchase_price' => 'required|numeric|min:0.01',
            'purchased_at' => 'required|date|before_or_equal:now',
            'notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();
            
            // Set remaining quantity equal to initial quantity
            $validated['remaining_quantity'] = $validated['quantity'];
            
            $inventory = Inventory::create($validated);
            
            DB::commit();

            return redirect()
                ->route('inventory.index')
                ->with('success', 'Inventory record created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Error creating inventory record. ' . $e->getMessage());
        }
    }

    /**
     * Display the specified inventory record.
     */
    public function show(Inventory $inventory)
    {
        $inventory->load('ingredient');
        return view('inventory.show', compact('inventory'));
    }

    /**
     * Show the form for editing the specified inventory record.
     */
    public function edit(Inventory $inventory)
    {
        $ingredients = Ingredient::orderBy('name')->get();
        return view('inventory.edit', compact('inventory', 'ingredients'));
    }

    /**
     * Update the specified inventory record in storage.
     */
    public function update(Request $request, Inventory $inventory)
    {
        $validated = $request->validate([
            'ingredient_id' => 'required|exists:ingredients,id',
            'quantity' => 'required|numeric|min:' . $inventory->quantity - $inventory->remaining_quantity,
            'purchase_price' => 'required|numeric|min:0.01',
            'purchased_at' => 'required|date|before_or_equal:now',
            'notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();
            
            // Update remaining quantity based on the difference in total quantity
            $quantityDifference = $validated['quantity'] - $inventory->quantity;
            $validated['remaining_quantity'] = $inventory->remaining_quantity + $quantityDifference;
            
            if ($validated['remaining_quantity'] < 0) {
                throw new \Exception('Cannot reduce quantity below used amount.');
            }
            
            $inventory->update($validated);
            
            DB::commit();

            return redirect()
                ->route('inventory.index')
                ->with('success', 'Inventory record updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Error updating inventory record. ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified inventory record from storage.
     */
    public function destroy(Inventory $inventory)
    {
        try {
            DB::beginTransaction();
            
            // Only allow deletion if no stock has been used
            if ($inventory->quantity != $inventory->remaining_quantity) {
                throw new \Exception('Cannot delete inventory record that has been used.');
            }
            
            $inventory->delete();
            
            DB::commit();

            return redirect()
                ->route('inventory.index')
                ->with('success', 'Inventory record deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error deleting inventory record. ' . $e->getMessage());
        }
    }

    /**
     * Get current stock levels for an ingredient
     */
    public function getStockLevels(Ingredient $ingredient)
    {
        $stockLevels = $ingredient->inventories()
            ->hasStock()
            ->fifo()
            ->get()
            ->map(function ($inventory) {
                return [
                    'batch_id' => $inventory->id,
                    'remaining_quantity' => $inventory->remaining_quantity,
                    'purchase_price' => $inventory->purchase_price,
                    'purchased_at' => $inventory->purchased_at->format('Y-m-d H:i:s')
                ];
            });

        return response()->json([
            'ingredient' => $ingredient->name,
            'stock_levels' => $stockLevels
        ]);
    }
}
