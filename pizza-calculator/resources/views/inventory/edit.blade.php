@extends('layouts.app')

@section('content')
<div class="bg-white rounded-lg shadow-lg p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">
            <i class="fas fa-edit text-indigo-600 mr-2"></i>Edit Stock Record
        </h1>
        <p class="text-gray-600 mt-1">
            Update inventory record for {{ $inventory->ingredient->name }}
        </p>
    </div>

    <form action="{{ route('inventory.update', $inventory) }}" method="POST">
        @method('PUT')
        @include('inventory.form')
    </form>

    <!-- Stock Usage Information -->
    <div class="mt-8 pt-6 border-t border-gray-200">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Stock Usage Information</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Current Stock Status -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-sm font-medium text-gray-700 mb-3">Current Status</h3>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Initial Quantity:</span>
                        <span class="text-sm font-medium">
                            {{ number_format($inventory->quantity, 2) }} {{ $inventory->ingredient->unit }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Used Quantity:</span>
                        <span class="text-sm font-medium">
                            {{ number_format($inventory->quantity - $inventory->remaining_quantity, 2) }} {{ $inventory->ingredient->unit }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Remaining Quantity:</span>
                        <span @class([
                            'text-sm font-medium',
                            'text-green-600' => $inventory->remaining_quantity > ($inventory->quantity * 0.5),
                            'text-yellow-600' => $inventory->remaining_quantity <= ($inventory->quantity * 0.5) && $inventory->remaining_quantity > 0,
                            'text-red-600' => $inventory->remaining_quantity == 0,
                        ])>
                            {{ number_format($inventory->remaining_quantity, 2) }} {{ $inventory->ingredient->unit }}
                            ({{ number_format(($inventory->remaining_quantity / $inventory->quantity) * 100, 1) }}%)
                        </span>
                    </div>
                </div>
            </div>

            <!-- Purchase Information -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-sm font-medium text-gray-700 mb-3">Purchase Information</h3>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Purchase Date:</span>
                        <span class="text-sm font-medium">
                            {{ $inventory->purchased_at->format('M d, Y H:i') }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Purchase Price:</span>
                        <span class="text-sm font-medium">
                            ${{ number_format($inventory->purchase_price, 2) }} per {{ $inventory->ingredient->unit }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Total Cost:</span>
                        <span class="text-sm font-medium">
                            ${{ number_format($inventory->quantity * $inventory->purchase_price, 2) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        @if($inventory->notes)
            <div class="mt-4 bg-yellow-50 rounded-lg p-4">
                <h3 class="text-sm font-medium text-yellow-800 mb-2">Notes</h3>
                <p class="text-sm text-yellow-700">{{ $inventory->notes }}</p>
            </div>
        @endif
    </div>

    <!-- Usage in Pizzas -->
    <div class="mt-8 pt-6 border-t border-gray-200">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Used in Pizzas</h2>
        
        @if($inventory->ingredient->pizzas->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($inventory->ingredient->pizzas as $pizza)
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-sm font-medium text-gray-900">{{ $pizza->name }}</h3>
                                <p class="text-xs text-gray-500">Size: {{ ucfirst($pizza->size) }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-indigo-600">
                                    {{ number_format($pizza->pivot->amount_required, 2) }}
                                </p>
                                <p class="text-xs text-gray-500">{{ $inventory->ingredient->unit }} per pizza</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 italic">This ingredient is not used in any pizzas.</p>
        @endif
    </div>
</div>
@endsection