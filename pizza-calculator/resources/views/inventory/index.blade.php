@extends('layouts.app')

@section('content')
<div class="bg-white rounded-lg shadow-lg p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">
            <i class="fas fa-warehouse text-indigo-600 mr-2"></i>Inventory Management
        </h1>
        <a href="{{ route('inventory.create') }}" 
           class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
            <i class="fas fa-plus mr-2"></i>Add Stock
        </a>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-gray-50 rounded-lg p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-boxes fa-2x"></i>
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-500">Total Batches</div>
                    <div class="text-2xl font-semibold text-gray-900">
                        {{ $inventories->total() }}
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-gray-50 rounded-lg p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fas fa-exclamation-triangle fa-2x"></i>
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-500">Low Stock Items</div>
                    <div class="text-2xl font-semibold text-gray-900">
                        {{ $inventories->filter(function($inventory) {
                            return $inventory->remaining_quantity <= ($inventory->quantity * 0.2);
                        })->count() }}
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-gray-50 rounded-lg p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-check-circle fa-2x"></i>
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-500">Active Batches</div>
                    <div class="text-2xl font-semibold text-gray-900">
                        {{ $inventories->filter(function($inventory) {
                            return $inventory->remaining_quantity > 0;
                        })->count() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory Table -->
    @if($inventories->isEmpty())
        <div class="text-center py-8">
            <i class="fas fa-box-open text-4xl text-gray-400 mb-3"></i>
            <p class="text-gray-500">No inventory records found. Start by adding some stock.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ingredient</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Purchase Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Initial Quantity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remaining</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($inventories as $inventory)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $inventory->ingredient->name }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    Unit: {{ $inventory->ingredient->unit }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $inventory->purchased_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ number_format($inventory->quantity, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div @class([
                                        'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                        'bg-green-100 text-green-800' => $inventory->remaining_quantity > ($inventory->quantity * 0.5),
                                        'bg-yellow-100 text-yellow-800' => $inventory->remaining_quantity <= ($inventory->quantity * 0.5) && $inventory->remaining_quantity > 0,
                                        'bg-red-100 text-red-800' => $inventory->remaining_quantity == 0,
                                    ])>
                                        {{ number_format($inventory->remaining_quantity, 2) }}
                                        ({{ number_format(($inventory->remaining_quantity / $inventory->quantity) * 100, 1) }}%)
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    ${{ number_format($inventory->purchase_price, 2) }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    per {{ $inventory->ingredient->unit }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-3">
                                    <a href="{{ route('inventory.show', $inventory) }}" 
                                       class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($inventory->remaining_quantity == $inventory->quantity)
                                        <a href="{{ route('inventory.edit', $inventory) }}" 
                                           class="text-indigo-600 hover:text-indigo-900">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('inventory.destroy', $inventory) }}" 
                                              method="POST" 
                                              class="inline"
                                              onsubmit="return confirm('Are you sure you want to delete this inventory record?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $inventories->links() }}
        </div>
    @endif
</div>
@endsection