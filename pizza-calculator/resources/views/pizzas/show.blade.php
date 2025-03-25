@extends('layouts.app')

@section('content')
<div class="bg-white rounded-lg shadow-lg p-6">
    <!-- Header -->
    <div class="flex justify-between items-start mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-800">
                <i class="fas fa-pizza-slice text-indigo-600 mr-2"></i>{{ $pizza->name }}
            </h1>
            <p class="text-gray-600 mt-1">{{ $pizza->description }}</p>
            <div class="mt-2">
                <span @class([
                    'px-2 py-1 text-sm font-medium rounded-full',
                    'bg-green-100 text-green-800' => $pizza->is_active,
                    'bg-gray-100 text-gray-800' => !$pizza->is_active,
                ])>
                    {{ $pizza->is_active ? 'Active' : 'Inactive' }}
                </span>
                <span class="ml-2 text-sm text-gray-500">Size: {{ ucfirst($pizza->size) }}</span>
            </div>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('pizzas.edit', $pizza) }}" 
               class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            <form action="{{ route('pizzas.destroy', $pizza) }}" 
                  method="POST" 
                  class="inline"
                  onsubmit="return confirm('Are you sure you want to delete this pizza?');">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                    <i class="fas fa-trash mr-2"></i>Delete
                </button>
            </form>
        </div>
    </div>

    <!-- Cost Information -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <!-- Basic Cost Info -->
        <div class="bg-gray-50 rounded-lg p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Cost Information</h2>
            
            @php
                $costBreakdown = $pizza->calculateIngredientsCost();
            @endphp

            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Base Price:</span>
                    <span class="text-lg font-medium">${{ number_format($pizza->base_price, 2) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Ingredients Cost:</span>
                    <span class="text-lg font-medium">${{ number_format($costBreakdown['total_cost'], 2) }}</span>
                </div>
                <div class="flex justify-between items-center pt-4 border-t">
                    <span class="text-gray-900 font-medium">Final Cost:</span>
                    <span class="text-xl font-semibold text-indigo-600">
                        ${{ number_format($costBreakdown['final_cost'], 2) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Stock Status -->
        <div class="bg-gray-50 rounded-lg p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Stock Status</h2>
            
            @if(!empty($costBreakdown['insufficient_ingredients']))
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">Stock Warning</h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <ul class="list-disc list-inside">
                                    @foreach($costBreakdown['insufficient_ingredients'] as $warning)
                                        <li>
                                            {{ $warning['ingredient'] }}: Need {{ number_format($warning['required'], 2) }}, 
                                            Missing {{ number_format($warning['missing'], 2) }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-green-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700">
                                All ingredients are available in sufficient quantity.
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Ingredients -->
    <div class="mb-8">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Ingredients</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($pizza->ingredients as $ingredient)
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-sm font-medium text-gray-900">{{ $ingredient->name }}</h3>
                            <p class="text-xs text-gray-500">Required: {{ number_format($ingredient->pivot->amount_required, 2) }} {{ $ingredient->unit }}</p>
                        </div>
                        <div class="text-right">
                            @php
                                $totalStock = $ingredient->inventories->sum('remaining_quantity');
                                $cost = collect($costBreakdown['ingredient_costs'])
                                    ->firstWhere('ingredient', $ingredient->name)['cost'] ?? 0;
                            @endphp
                            <p @class([
                                'text-sm font-medium',
                                'text-green-600' => $totalStock >= $ingredient->pivot->amount_required,
                                'text-yellow-600' => $totalStock < $ingredient->pivot->amount_required && $totalStock > 0,
                                'text-red-600' => $totalStock == 0,
                            ])>
                                Stock: {{ number_format($totalStock, 2) }} {{ $ingredient->unit }}
                            </p>
                            <p class="text-xs text-gray-500">
                                Cost: ${{ number_format($cost, 2) }}
                            </p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Inventory Details -->
    <div>
        <h2 class="text-lg font-medium text-gray-900 mb-4">Inventory Details</h2>
        
        <div class="bg-gray-50 rounded-lg p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ingredient</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Available Batches</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Latest Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($pizza->ingredients as $ingredient)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $ingredient->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $ingredient->unit }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $ingredient->inventories->where('remaining_quantity', '>', 0)->count() }} batches
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        Total: {{ number_format($ingredient->inventories->sum('remaining_quantity'), 2) }} {{ $ingredient->unit }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($latestInventory = $ingredient->latest_inventory)
                                        <div class="text-sm text-gray-900">
                                            ${{ number_format($latestInventory->purchase_price, 2) }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            per {{ $ingredient->unit }}
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-500">No price data</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $required = $ingredient->pivot->amount_required;
                                        $available = $ingredient->inventories->sum('remaining_quantity');
                                        $status = $available >= $required ? 'In Stock' : 'Low Stock';
                                    @endphp
                                    <span @class([
                                        'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                        'bg-green-100 text-green-800' => $status === 'In Stock',
                                        'bg-red-100 text-red-800' => $status === 'Low Stock',
                                    ])>
                                        {{ $status }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection