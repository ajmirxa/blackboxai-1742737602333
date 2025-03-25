@extends('layouts.app')

@section('content')
<div class="bg-white rounded-lg shadow-lg p-6">
    <!-- Header -->
    <div class="flex justify-between items-start mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-800">
                <i class="fas fa-info-circle text-indigo-600 mr-2"></i>{{ $ingredient->name }}
            </h1>
            <p class="text-gray-600 mt-1">{{ $ingredient->description }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('ingredients.edit', $ingredient) }}" 
               class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            <form action="{{ route('ingredients.destroy', $ingredient) }}" 
                  method="POST" 
                  class="inline"
                  onsubmit="return confirm('Are you sure you want to delete this ingredient?');">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                    <i class="fas fa-trash mr-2"></i>Delete
                </button>
            </form>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-gray-50 rounded-lg p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-box fa-2x"></i>
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-500">Total Stock</div>
                    <div class="text-2xl font-semibold text-gray-900">
                        {{ number_format($ingredient->inventories->sum('remaining_quantity'), 2) }} {{ $ingredient->unit }}
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-gray-50 rounded-lg p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-dollar-sign fa-2x"></i>
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-500">Latest Price</div>
                    <div class="text-2xl font-semibold text-gray-900">
                        ${{ number_format($ingredient->latest_inventory?->purchase_price ?? 0, 2) }}
                        <span class="text-sm text-gray-500">per {{ $ingredient->unit }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-gray-50 rounded-lg p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <i class="fas fa-pizza-slice fa-2x"></i>
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-500">Used In</div>
                    <div class="text-2xl font-semibold text-gray-900">
                        {{ $ingredient->pizzas->count() }} Pizzas
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory History -->
    <div class="mb-8">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Inventory History</h2>
        @if($ingredient->inventories->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Batch #</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Purchase Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Initial Quantity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Remaining</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($ingredient->inventories->sortByDesc('purchased_at') as $inventory)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    #{{ $inventory->id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $inventory->purchased_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ number_format($inventory->quantity, 2) }} {{ $ingredient->unit }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div @class([
                                            'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                            'bg-green-100 text-green-800' => $inventory->remaining_quantity > ($inventory->quantity * 0.5),
                                            'bg-yellow-100 text-yellow-800' => $inventory->remaining_quantity <= ($inventory->quantity * 0.5) && $inventory->remaining_quantity > 0,
                                            'bg-red-100 text-red-800' => $inventory->remaining_quantity == 0,
                                        ])>
                                            {{ number_format($inventory->remaining_quantity, 2) }} {{ $ingredient->unit }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    ${{ number_format($inventory->purchase_price, 2) }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $inventory->notes ?? '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-500 italic">No inventory records found.</p>
        @endif
    </div>

    <!-- Pizza Usage -->
    <div>
        <h2 class="text-lg font-medium text-gray-900 mb-4">Pizza Usage</h2>
        @if($ingredient->pizzas->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($ingredient->pizzas as $pizza)
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">{{ $pizza->name }}</h3>
                                <p class="text-sm text-gray-500">Size: {{ ucfirst($pizza->size) }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-semibold text-indigo-600">
                                    {{ number_format($pizza->pivot->amount_required, 2) }}
                                </p>
                                <p class="text-sm text-gray-500">{{ $ingredient->unit }} per pizza</p>
                            </div>
                        </div>
                        @if($pizza->description)
                            <p class="mt-2 text-sm text-gray-600">{{ $pizza->description }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 italic">This ingredient is not used in any pizzas.</p>
        @endif
    </div>
</div>
@endsection