@extends('layouts.app')

@section('content')
<div class="bg-white rounded-lg shadow-lg p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">
            <i class="fas fa-edit text-indigo-600 mr-2"></i>Edit Pizza
        </h1>
        <p class="text-gray-600 mt-1">Update details for {{ $pizza->name }}</p>
    </div>

    <form action="{{ route('pizzas.update', $pizza) }}" method="POST">
        @method('PUT')
        @include('pizzas.form')
    </form>

    <!-- Current Cost Information -->
    <div class="mt-8 pt-6 border-t border-gray-200">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Current Cost Information</h2>
        
        @php
            $costBreakdown = $pizza->calculateIngredientsCost();
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Cost Breakdown -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-sm font-medium text-gray-700 mb-3">Cost Breakdown</h3>
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Base Price:</span>
                        <span class="font-medium">${{ number_format($pizza->base_price, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Ingredients Cost:</span>
                        <span class="font-medium">${{ number_format($costBreakdown['total_cost'], 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm font-medium text-indigo-600 pt-2 border-t">
                        <span>Final Cost:</span>
                        <span>${{ number_format($costBreakdown['final_cost'], 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Ingredient Costs -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-sm font-medium text-gray-700 mb-3">Ingredient Costs</h3>
                <div class="space-y-2">
                    @foreach($costBreakdown['ingredient_costs'] as $cost)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">{{ $cost['ingredient'] }}:</span>
                            <span class="font-medium">${{ number_format($cost['cost'], 2) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Stock Warnings -->
        @if(!empty($costBreakdown['insufficient_ingredients']))
            <div class="mt-4 bg-yellow-50 border-l-4 border-yellow-400 p-4">
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
        @endif
    </div>

    <!-- Current Stock Levels -->
    <div class="mt-8 pt-6 border-t border-gray-200">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Current Stock Levels</h2>
        
        <div class="bg-gray-50 rounded-lg p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($ingredients as $ingredient)
                    <div class="bg-white rounded-lg p-4 shadow">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-sm font-medium text-gray-900">{{ $ingredient->name }}</h3>
                                <p class="text-xs text-gray-500">Unit: {{ $ingredient->unit }}</p>
                            </div>
                            <div class="text-right">
                                @php
                                    $totalStock = $ingredient->inventories->sum('remaining_quantity');
                                    $requiredAmount = $pizza->ingredients->where('id', $ingredient->id)->first()?->pivot->amount_required ?? 0;
                                @endphp
                                <p @class([
                                    'text-sm font-medium',
                                    'text-green-600' => $totalStock >= $requiredAmount,
                                    'text-yellow-600' => $totalStock < $requiredAmount && $totalStock > 0,
                                    'text-red-600' => $totalStock == 0,
                                ])>
                                    {{ number_format($totalStock, 2) }} {{ $ingredient->unit }}
                                </p>
                                @if($requiredAmount > 0)
                                    <p class="text-xs text-gray-500">
                                        Needed: {{ number_format($requiredAmount, 2) }} {{ $ingredient->unit }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection