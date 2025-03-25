@extends('layouts.app')

@section('content')
<div class="bg-white rounded-lg shadow-lg p-6">
    <!-- Header -->
    <div class="flex justify-between items-start mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-800">
                <i class="fas fa-box text-indigo-600 mr-2"></i>Inventory Record Details
            </h1>
            <p class="text-gray-600 mt-1">
                Batch #{{ $inventory->id }} for {{ $inventory->ingredient->name }}
            </p>
        </div>
        <div class="flex space-x-3">
            @if($inventory->remaining_quantity == $inventory->quantity)
                <a href="{{ route('inventory.edit', $inventory) }}" 
                   class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
                <form action="{{ route('inventory.destroy', $inventory) }}" 
                      method="POST" 
                      class="inline"
                      onsubmit="return confirm('Are you sure you want to delete this inventory record?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                        <i class="fas fa-trash mr-2"></i>Delete
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Main Information -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Stock Information -->
        <div class="bg-gray-50 rounded-lg p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Stock Information</h2>
            
            <div class="space-y-4">
                <div>
                    <label class="text-sm font-medium text-gray-700">Ingredient</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $inventory->ingredient->name }}</p>
                    <p class="text-xs text-gray-500">Unit: {{ $inventory->ingredient->unit }}</p>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-700">Initial Quantity</label>
                    <p class="mt-1 text-sm text-gray-900">
                        {{ number_format($inventory->quantity, 2) }} {{ $inventory->ingredient->unit }}
                    </p>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-700">Remaining Quantity</label>
                    <div class="mt-1">
                        <div @class([
                            'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium',
                            'bg-green-100 text-green-800' => $inventory->remaining_quantity > ($inventory->quantity * 0.5),
                            'bg-yellow-100 text-yellow-800' => $inventory->remaining_quantity <= ($inventory->quantity * 0.5) && $inventory->remaining_quantity > 0,
                            'bg-red-100 text-red-800' => $inventory->remaining_quantity == 0,
                        ])>
                            {{ number_format($inventory->remaining_quantity, 2) }} {{ $inventory->ingredient->unit }}
                            ({{ number_format(($inventory->remaining_quantity / $inventory->quantity) * 100, 1) }}%)
                        </div>
                    </div>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-700">Used Quantity</label>
                    <p class="mt-1 text-sm text-gray-900">
                        {{ number_format($inventory->quantity - $inventory->remaining_quantity, 2) }} {{ $inventory->ingredient->unit }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Purchase Information -->
        <div class="bg-gray-50 rounded-lg p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Purchase Information</h2>
            
            <div class="space-y-4">
                <div>
                    <label class="text-sm font-medium text-gray-700">Purchase Date</label>
                    <p class="mt-1 text-sm text-gray-900">
                        {{ $inventory->purchased_at->format('M d, Y H:i') }}
                    </p>
                    <p class="text-xs text-gray-500">
                        {{ $inventory->purchased_at->diffForHumans() }}
                    </p>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-700">Purchase Price</label>
                    <p class="mt-1 text-sm text-gray-900">
                        ${{ number_format($inventory->purchase_price, 2) }} per {{ $inventory->ingredient->unit }}
                    </p>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-700">Total Cost</label>
                    <p class="mt-1 text-sm text-gray-900">
                        ${{ number_format($inventory->quantity * $inventory->purchase_price, 2) }}
                    </p>
                </div>

                @if($inventory->notes)
                    <div class="mt-4 bg-yellow-50 rounded-lg p-4">
                        <label class="text-sm font-medium text-yellow-800">Notes</label>
                        <p class="mt-1 text-sm text-yellow-700">{{ $inventory->notes }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Usage in Pizzas -->
    <div class="mt-8">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Ingredient Usage in Pizzas</h2>
        
        @if($inventory->ingredient->pizzas->isNotEmpty())
            <div class="bg-gray-50 rounded-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($inventory->ingredient->pizzas as $pizza)
                        <div class="bg-white rounded-lg p-4 shadow">
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
                            @if($pizza->description)
                                <p class="mt-2 text-sm text-gray-600">{{ $pizza->description }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <p class="text-gray-500 italic">This ingredient is not used in any pizzas.</p>
        @endif
    </div>

    <!-- Back Button -->
    <div class="mt-6 flex justify-end">
        <a href="{{ route('inventory.index') }}" 
           class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-lg transition-colors duration-200">
            <i class="fas fa-arrow-left mr-2"></i>Back to Inventory
        </a>
    </div>
</div>
@endsection