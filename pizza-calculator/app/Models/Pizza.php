<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Pizza extends Model
{
    protected $fillable = [
        'name',
        'description',
        'size',
        'base_price',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'base_price' => 'decimal:2',
    ];

    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(Ingredient::class, 'pizza_ingredients')
            ->withPivot('amount_required');
    }

    public function calculateIngredientsCost()
    {
        $this->load('ingredients.inventories');
        
        $totalCost = 0;
        $ingredientCosts = [];
        $insufficientIngredients = [];

        foreach ($this->ingredients as $ingredient) {
            $amountRequired = $ingredient->pivot->amount_required;
            $totalStock = $ingredient->inventories->sum('remaining_quantity');
            
            // Check if we have enough stock
            if ($totalStock < $amountRequired) {
                $insufficientIngredients[] = [
                    'ingredient' => $ingredient->name,
                    'required' => $amountRequired,
                    'available' => $totalStock,
                    'missing' => $amountRequired - $totalStock,
                ];
            }

            // Calculate cost using latest purchase price
            $latestInventory = $ingredient->inventories()
                ->whereNotNull('purchase_price')
                ->orderByDesc('purchased_at')
                ->first();

            if ($latestInventory) {
                $cost = $amountRequired * $latestInventory->purchase_price;
                $totalCost += $cost;
                
                $ingredientCosts[] = [
                    'ingredient' => $ingredient->name,
                    'amount' => $amountRequired,
                    'unit' => $ingredient->unit,
                    'unit_price' => $latestInventory->purchase_price,
                    'cost' => $cost,
                ];
            }
        }

        // Add base price to total cost
        $finalCost = $totalCost + $this->base_price;

        return [
            'base_price' => $this->base_price,
            'total_cost' => $totalCost,
            'final_cost' => $finalCost,
            'ingredient_costs' => $ingredientCosts,
            'insufficient_ingredients' => $insufficientIngredients,
        ];
    }

    public function hasRequiredIngredients()
    {
        $this->load('ingredients.inventories');
        
        foreach ($this->ingredients as $ingredient) {
            $amountRequired = $ingredient->pivot->amount_required;
            $totalStock = $ingredient->inventories->sum('remaining_quantity');
            
            if ($totalStock < $amountRequired) {
                return false;
            }
        }
        
        return true;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
