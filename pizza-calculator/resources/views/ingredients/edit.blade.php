d ..
@extends('layouts.app')

@section('content')
<div class="bg-white rounded-lg shadow-lg p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">
            <i class="fas fa-edit text-indigo-600 mr-2"></i>Edit Ingredient
        </h1>
        <p class="text-gray-600 mt-1">Update the details for {{ $ingredient->name }}.</p>
    </div>

    <form action="{{ route('ingredients.update', $ingredient) }}" method="POST">
        @method('PUT')
        @include('ingredients.form')
    </form>

    <!-- Current Stock Information -->
    <div class="mt-8 pt-6 border-t border-gray-200">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Current Stock Information</h2>
        
        @if($ingredient->inventories->isNotEmpty())
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="space-y-4">
                    @foreach($ingredient->inventories->where('remaining_quantity', '>', 0) as $inventory)
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm font-medium text-gray-900">
                                    Batch #{{ $inventory->id }}
                                </p>
                                <p class="text-sm text-gray-500">
                                    Purchased: {{ $inventory->purchased_at->format('M d, Y') }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900">
                                    {{ number_format($inventory->remaining_quantity, 2) }} {{ $ingredient->unit }}
                                </p>
                                <p class="text-sm text-gray-500">
                                    ${{ number_format($inventory->purchase_price, 2) }} per {{ $ingredient->unit }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <p class="text-gray-500 italic">No stock records found.</p>
        @endif
    </div>

    <!-- Usage in Pizzas -->
    <div class="mt-8 pt-6 border-t border-gray-200">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Used in Pizzas</h2>
        
        @if($ingredient->pizzas->isNotEmpty())
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="space-y-4">
                    @foreach($ingredient->pizzas as $pizza)
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $pizza->name }}
                                </p>
                                <p class="text-sm text-gray-500">
                                    Size: {{ ucfirst($pizza->size) }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900">
                                    {{ number_format($pizza->pivot->amount_required, 2) }} {{ $ingredient->unit }}
                                </p>
                                <p class="text-sm text-gray-500">
                                    per pizza
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <p class="text-gray-500 italic">This ingredient is not used in any pizzas.</p>
        @endif
    </div>
</div>
@endsection