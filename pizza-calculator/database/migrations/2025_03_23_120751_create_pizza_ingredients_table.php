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
        Schema::create('pizza_ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pizza_id')->constrained()->onDelete('cascade');
            $table->foreignId('ingredient_id')->constrained()->onDelete('cascade');
            $table->decimal('amount_required', 10, 2); // Amount of ingredient needed for this pizza
            $table->timestamps();

            // Prevent duplicate ingredient entries for the same pizza
            $table->unique(['pizza_id', 'ingredient_id']);
            
            // Index for faster lookups
            $table->index(['pizza_id', 'ingredient_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pizza_ingredients');
    }
};
