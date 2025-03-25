<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ingredient_id')->constrained()->onDelete('cascade');
            $table->decimal('quantity', 10, 2); // Total quantity purchased
            $table->decimal('remaining_quantity', 10, 2); // Remaining quantity in stock
            $table->decimal('purchase_price', 10, 2); // Price per unit
            $table->timestamp('purchased_at');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Index for faster lookups
            $table->index(['ingredient_id', 'purchased_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
