<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventory extends Model
{
    protected $fillable = [
        'ingredient_id',
        'quantity',
        'remaining_quantity',
        'purchase_price',
        'purchased_at',
        'notes'
    ];

    protected $casts = [
        'purchased_at' => 'datetime',
        'quantity' => 'decimal:2',
        'remaining_quantity' => 'decimal:2',
        'purchase_price' => 'decimal:2'
    ];

    /**
     * Get the ingredient that this inventory belongs to.
     */
    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }

    /**
     * Reduce the remaining quantity of this inventory batch
     */
    public function reduceStock(float $amount): bool
    {
        if ($this->remaining_quantity >= $amount) {
            $this->remaining_quantity -= $amount;
            return $this->save();
        }
        return false;
    }

    /**
     * Calculate the total cost for a specific quantity from this batch
     */
    public function calculateCost(float $quantity): float
    {
        return $quantity * $this->purchase_price;
    }

    /**
     * Check if this inventory batch has enough remaining quantity
     */
    public function hasEnoughStock(float $required): bool
    {
        return $this->remaining_quantity >= $required;
    }

    /**
     * Get the maximum amount that can be used from this batch
     */
    public function getAvailableQuantity(): float
    {
        return $this->remaining_quantity;
    }

    /**
     * Scope a query to only include inventory with remaining stock
     */
    public function scopeHasStock($query)
    {
        return $query->where('remaining_quantity', '>', 0);
    }

    /**
     * Scope a query to order by FIFO (First In, First Out)
     */
    public function scopeFifo($query)
    {
        return $query->orderBy('purchased_at', 'asc');
    }
}
