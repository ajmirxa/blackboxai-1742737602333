<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Ingredient extends Model
{
    protected $fillable = [
        'name',
        'unit',
        'description'
    ];

    /**
     * Get the inventory records for this ingredient.
     */
    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

    /**
     * Get the pizzas that use this ingredient.
     */
    public function pizzas(): BelongsToMany
    {
        return $this->belongsToMany(Pizza::class, 'pizza_ingredients')
                    ->withPivot('amount_required')
                    ->withTimestamps();
    }

    /**
     * Get the latest available inventory with remaining quantity.
     */
    public function getLatestInventoryAttribute()
    {
        return $this->inventories()
                    ->where('remaining_quantity', '>', 0)
                    ->orderBy('purchased_at', 'asc')
                    ->first();
    }

    /**
     * Calculate the current cost of the ingredient based on FIFO.
     */
    public function getCurrentCostAttribute()
    {
        $latestInventory = $this->latest_inventory;
        return $latestInventory ? $latestInventory->purchase_price : 0;
    }
}
