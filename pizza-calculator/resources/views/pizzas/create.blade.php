@extends('layouts.app')

@section('content')
<div class="bg-white rounded-lg shadow-lg p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">
            <i class="fas fa-plus text-indigo-600 mr-2"></i>Add New Pizza
        </h1>
        <p class="text-gray-600 mt-1">Create a new pizza with ingredients and pricing.</p>
    </div>

    <form action="{{ route('pizzas.store') }}" method="POST">
        @include('pizzas.form')
    </form>

    <!-- Current Ingredients Stock -->
    <div class="mt-8 pt-6 border-t border-gray-200">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Current Ingredients Stock</h2>
        
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
                                @endphp
                                <p @class([
                                    'text-sm font-medium',
                                    'text-green-600' => $totalStock > 100,
                                    'text-yellow-600' => $totalStock <= 100 && $totalStock > 0,
                                    'text-red-600' => $totalStock == 0,
                                ])>
                                    {{ number_format($totalStock, 2) }} {{ $ingredient->unit }}
                                </p>
                                @if($latestInventory = $ingredient->latest_inventory)
                                    <p class="text-xs text-gray-500">
                                        ${{ number_format($latestInventory->purchase_price, 2) }} per {{ $ingredient->unit }}
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