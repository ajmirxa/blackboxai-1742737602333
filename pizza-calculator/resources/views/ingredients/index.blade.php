@extends('layouts.app')

@section('content')
<div class="bg-white rounded-lg shadow-lg p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">
            <i class="fas fa-carrot text-indigo-600 mr-2"></i>Ingredients
        </h1>
        <a href="{{ route('ingredients.create') }}" 
           class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
            <i class="fas fa-plus mr-2"></i>Add Ingredient
        </a>
    </div>

    @if($ingredients->isEmpty())
        <div class="text-center py-8">
            <i class="fas fa-box-open text-4xl text-gray-400 mb-3"></i>
            <p class="text-gray-500">No ingredients found. Start by adding some ingredients.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Stock</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Latest Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($ingredients as $ingredient)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $ingredient->name }}</div>
                                @if($ingredient->description)
                                    <div class="text-sm text-gray-500">{{ $ingredient->description }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $ingredient->unit }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $totalStock = $ingredient->inventories->sum('remaining_quantity');
                                @endphp
                                <div class="text-sm text-gray-900">
                                    {{ number_format($totalStock, 2) }} {{ $ingredient->unit }}
                                </div>
                                @if($totalStock < 100) {{-- Example threshold --}}
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Low Stock
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($ingredient->latest_inventory)
                                    <div class="text-sm text-gray-900">
                                        ${{ number_format($ingredient->latest_inventory->purchase_price, 2) }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        per {{ $ingredient->unit }}
                                    </div>
                                @else
                                    <span class="text-sm text-gray-500">No price data</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-3">
                                    <a href="{{ route('ingredients.edit', $ingredient) }}" 
                                       class="text-indigo-600 hover:text-indigo-900">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('ingredients.show', $ingredient) }}" 
                                       class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <form action="{{ route('ingredients.destroy', $ingredient) }}" 
                                          method="POST" 
                                          class="inline"
                                          onsubmit="return confirm('Are you sure you want to delete this ingredient?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<!-- Quick Stats -->
<div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-indigo-100 text-indigo-600">
                <i class="fas fa-box fa-2x"></i>
            </div>
            <div class="ml-4">
                <div class="text-sm font-medium text-gray-500">Total Ingredients</div>
                <div class="text-2xl font-semibold text-gray-900">{{ $ingredients->count() }}</div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                <i class="fas fa-exclamation-triangle fa-2x"></i>
            </div>
            <div class="ml-4">
                <div class="text-sm font-medium text-gray-500">Low Stock Items</div>
                <div class="text-2xl font-semibold text-gray-900">
                    {{ $ingredients->filter(function($ingredient) {
                        return $ingredient->inventories->sum('remaining_quantity') < 100;
                    })->count() }}
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-100 text-green-600">
                <i class="fas fa-pizza-slice fa-2x"></i>
            </div>
            <div class="ml-4">
                <div class="text-sm font-medium text-gray-500">Used In Pizzas</div>
                <div class="text-2xl font-semibold text-gray-900">
                    {{ $ingredients->filter(function($ingredient) {
                        return $ingredient->pizzas->isNotEmpty();
                    })->count() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection