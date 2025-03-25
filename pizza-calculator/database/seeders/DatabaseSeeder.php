<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ingredient;
use App\Models\Inventory;
use App\Models\Pizza;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create ingredients
        $ingredients = [
            [
                'name' => 'Pizza Dough',
                'unit' => 'grams',
                'description' => 'Fresh pizza dough base',
            ],
            [
                'name' => 'Tomato Sauce',
                'unit' => 'grams',
                'description' => 'Classic pizza sauce',
            ],
            [
                'name' => 'Mozzarella Cheese',
                'unit' => 'grams',
                'description' => 'Fresh mozzarella cheese',
            ],
            [
                'name' => 'Pepperoni',
                'unit' => 'grams',
                'description' => 'Sliced pepperoni',
            ],
            [
                'name' => 'Mushrooms',
                'unit' => 'grams',
                'description' => 'Fresh sliced mushrooms',
            ],
            [
                'name' => 'Bell Peppers',
                'unit' => 'grams',
                'description' => 'Fresh sliced bell peppers',
            ],
            [
                'name' => 'Onions',
                'unit' => 'grams',
                'description' => 'Fresh sliced onions',
            ],
            [
                'name' => 'Olives',
                'unit' => 'grams',
                'description' => 'Black olives',
            ],
        ];

        foreach ($ingredients as $ingredientData) {
            $ingredient = Ingredient::create($ingredientData);

            // Add inventory for each ingredient
            Inventory::create([
                'ingredient_id' => $ingredient->id,
                'quantity' => 5000,
                'remaining_quantity' => 5000,
                'purchase_price' => rand(1, 5),
                'purchased_at' => Carbon::now(),
                'notes' => 'Initial stock',
            ]);
        }

        // Create pizzas
        $pizzas = [
            [
                'name' => 'Margherita',
                'description' => 'Classic pizza with tomato sauce and mozzarella',
                'size' => 'medium',
                'base_price' => 10.00,
                'is_active' => true,
                'ingredients' => [
                    'Pizza Dough' => 250,
                    'Tomato Sauce' => 100,
                    'Mozzarella Cheese' => 200,
                ],
            ],
            [
                'name' => 'Pepperoni',
                'description' => 'Classic pepperoni pizza',
                'size' => 'medium',
                'base_price' => 12.00,
                'is_active' => true,
                'ingredients' => [
                    'Pizza Dough' => 250,
                    'Tomato Sauce' => 100,
                    'Mozzarella Cheese' => 200,
                    'Pepperoni' => 100,
                ],
            ],
            [
                'name' => 'Vegetarian',
                'description' => 'Loaded with fresh vegetables',
                'size' => 'medium',
                'base_price' => 11.00,
                'is_active' => true,
                'ingredients' => [
                    'Pizza Dough' => 250,
                    'Tomato Sauce' => 100,
                    'Mozzarella Cheese' => 200,
                    'Mushrooms' => 75,
                    'Bell Peppers' => 75,
                    'Onions' => 50,
                    'Olives' => 50,
                ],
            ],
            [
                'name' => 'Supreme',
                'description' => 'The ultimate pizza with all toppings',
                'size' => 'large',
                'base_price' => 15.00,
                'is_active' => true,
                'ingredients' => [
                    'Pizza Dough' => 300,
                    'Tomato Sauce' => 150,
                    'Mozzarella Cheese' => 250,
                    'Pepperoni' => 100,
                    'Mushrooms' => 75,
                    'Bell Peppers' => 75,
                    'Onions' => 50,
                    'Olives' => 50,
                ],
            ],
        ];

        foreach ($pizzas as $pizzaData) {
            $ingredients = $pizzaData['ingredients'];
            unset($pizzaData['ingredients']);
            
            $pizza = Pizza::create($pizzaData);

            foreach ($ingredients as $ingredientName => $amount) {
                $ingredient = Ingredient::where('name', $ingredientName)->first();
                $pizza->ingredients()->attach($ingredient->id, ['amount_required' => $amount]);
            }
        }
    }
}
