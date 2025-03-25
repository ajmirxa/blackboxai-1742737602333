@extends('layouts.app')

@section('content')
<div class="bg-white rounded-lg shadow-lg p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">
            <i class="fas fa-pizza-slice text-indigo-600 mr-2"></i>Pizza Management
        </h1>
        <a href="{{ route('pizzas.create') }}" 
           class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
            <i class="fas fa-plus mr-2"></i>Add Pizza
        </a>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-gray-50 rounded-lg p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-indigo-100 text-indigo-600">
                    <i class="fas fa-pizza-slice fa-2x"></i>
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-500">Total Pizzas</div>
                    <div class="text-2xl font-semibold text-gray-900">{{ $pizzas->count() }}</div>
                </div>
            </div>
        </div>

        <div class="bg-gray-50 rounded-lg p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-check-circle fa-2x"></i>
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-500">Active Pizzas</div>
                    <div class="text-2xl font-semibold text-gray-900">
                        {{ $pizzas->where('is_active', true)->count() }}
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
                    <div class="text-sm font-medium text-gray-500">Low Stock Warnings</div>
                    <div class="text-2xl font-semibold text-gray-900">
                        {{ $pizzas->filter(function($pizza) {
                            return !$pizza->hasRequiredIngredients();
                        })->count() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($pizzas->isEmpty())
        <div class="text-center py-8">
            <i class="fas fa-pizza-slice text-4xl text-gray-400 mb-3"></i>
            <p class="text-gray-500">No pizzas found. Start by adding some pizzas.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($pizzas as $pizza)
                <div class="bg-gray-50 rounded-lg shadow p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">{{ $pizza->name }}</h3>
                            <p class="text-sm text-gray-500">Size: {{ ucfirst($pizza->size) }}</p>
                        </div>
                        <div>
                            <button onclick="togglePizzaStatus({{ $pizza->id }})" 
                                    class="toggle-status"
                                    data-pizza-id="{{ $pizza->id }}"
                                    @class([
                                        'px-3 py-1 rounded-full text-sm font-medium',
                                        'bg-green-100 text-green-800' => $pizza->is_active,
                                        'bg-gray-100 text-gray-800' => !$pizza->is_active,
                                    ])>
                                {{ $pizza->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </div>
                    </div>

                    @if($pizza->description)
                        <p class="text-sm text-gray-600 mb-4">{{ $pizza->description }}</p>
                    @endif

                    <div class="mb-4">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Ingredients:</h4>
                        <ul class="space-y-1">
                            @foreach($pizza->ingredients as $ingredient)
                                <li class="text-sm text-gray-600">
                                    {{ $ingredient->name }} 
                                    ({{ number_format($ingredient->pivot->amount_required, 2) }} {{ $ingredient->unit }})
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="mb-4">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Base Price:</span>
                            <span class="font-medium">${{ number_format($pizza->base_price, 2) }}</span>
                        </div>
                        @php
                            $costBreakdown = $pizza->calculateIngredientsCost();
                        @endphp
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Ingredients Cost:</span>
                            <span class="font-medium">${{ number_format($costBreakdown['total_cost'], 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm font-medium text-indigo-600 mt-1 pt-1 border-t">
                            <span>Total Cost:</span>
                            <span>${{ number_format($costBreakdown['final_cost'], 2) }}</span>
                        </div>
                    </div>

                    @if(!empty($costBreakdown['insufficient_ingredients']))
                        <div class="mb-4 bg-yellow-50 border-l-4 border-yellow-400 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700">
                                        Insufficient stock for some ingredients
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="flex justify-end space-x-2">
                        <a href="{{ route('pizzas.show', $pizza) }}" 
                           class="text-blue-600 hover:text-blue-900">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('pizzas.edit', $pizza) }}" 
                           class="text-indigo-600 hover:text-indigo-900">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('pizzas.destroy', $pizza) }}" 
                              method="POST" 
                              class="inline"
                              onsubmit="return confirm('Are you sure you want to delete this pizza?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

@push('scripts')
<script>
function togglePizzaStatus(pizzaId) {
    fetch(`/pizzas/${pizzaId}/toggle`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            const button = document.querySelector(`.toggle-status[data-pizza-id="${pizzaId}"]`);
            if (data.is_active) {
                button.classList.remove('bg-gray-100', 'text-gray-800');
                button.classList.add('bg-green-100', 'text-green-800');
                button.textContent = 'Active';
            } else {
                button.classList.remove('bg-green-100', 'text-green-800');
                button.classList.add('bg-gray-100', 'text-gray-800');
                button.textContent = 'Inactive';
            }
        }
    })
    .catch(error => console.error('Error:', error));
}
</script>
@endpush