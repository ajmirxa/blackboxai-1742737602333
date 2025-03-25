@extends('layouts.app')

@section('content')
<div class="bg-white rounded-lg shadow-lg p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">
            <i class="fas fa-plus text-indigo-600 mr-2"></i>Add New Stock
        </h1>
        <p class="text-gray-600 mt-1">Add new inventory for an ingredient.</p>
    </div>

    <form action="{{ route('inventory.store') }}" method="POST">
        @include('inventory.form')
    </form>

    <!-- Current Stock Levels -->
    <div class="mt-8 pt-6 border-t border-gray-200">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Current Stock Levels</h2>
        
        <div class="bg-gray-50 rounded-lg p-4">
            <div class="space-y-4">
                @forelse($ingredients as $ingredient)
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $ingredient->name }}</p>
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
                                    Last price: ${{ number_format($latestInventory->purchase_price, 2) }}
                                </p>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 italic">No ingredients found. Please add ingredients first.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection