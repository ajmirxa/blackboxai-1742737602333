@extends('layouts.app')

@section('content')
<div class="bg-white rounded-lg shadow-lg p-6">
    <h1 class="text-2xl font-semibold text-gray-800 mb-6">
        <i class="fas fa-calculator text-indigo-600 mr-2"></i>Pizza Cost Calculator
    </h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Pizza Selection -->
        <div>
            <h2 class="text-lg font-medium text-gray-900 mb-4">Select Pizza</h2>
            <div class="space-y-4">
                @foreach($pizzas as $pizza)
                    <div class="pizza-card bg-gray-50 rounded-lg p-4 cursor-pointer hover:bg-gray-100 transition-colors duration-200"
                         data-pizza-id="{{ $pizza->id }}"
                         onclick="selectPizza({{ $pizza->id }})">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">{{ $pizza->name }}</h3>
                                <p class="text-sm text-gray-600">{{ $pizza->description }}</p>
                                <p class="text-sm text-gray-500 mt-1">Size: {{ ucfirst($pizza->size) }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-medium text-indigo-600">
                                    Base: ${{ number_format($pizza->base_price, 2) }}
                                </p>
                            </div>
                        </div>

                        <div class="mt-3">
                            <h4 class="text-sm font-medium text-gray-700">Ingredients:</h4>
                            <ul class="mt-1 space-y-1">
                                @foreach($pizza->ingredients as $ingredient)
                                    <li class="text-sm text-gray-600">
                                        {{ $ingredient->name }} 
                                        ({{ number_format($ingredient->pivot->amount_required, 2) }} {{ $ingredient->unit }})
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Cost Breakdown -->
        <div>
            <h2 class="text-lg font-medium text-gray-900 mb-4">Cost Breakdown</h2>
            <div id="cost-breakdown" class="bg-gray-50 rounded-lg p-6">
                <div id="initial-message" class="flex items-center justify-center h-48 text-gray-500">
                    <div class="text-center">
                        <i class="fas fa-hand-point-left text-4xl mb-2"></i>
                        <p>Select a pizza to see cost breakdown</p>
                    </div>
                </div>
                <div id="loading-message" class="hidden flex items-center justify-center h-48">
                    <div class="text-center">
                        <i class="fas fa-spinner fa-spin text-4xl text-indigo-600 mb-2"></i>
                        <p class="text-gray-600">Calculating costs...</p>
                    </div>
                </div>
                <div id="error-message" class="hidden flex items-center justify-center h-48">
                    <div class="text-center text-red-500">
                        <i class="fas fa-exclamation-circle text-4xl mb-2"></i>
                        <p>Error calculating costs. Please try again.</p>
                    </div>
                </div>
                <div id="cost-details" class="hidden"></div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function showLoading() {
    document.getElementById('initial-message').classList.add('hidden');
    document.getElementById('error-message').classList.add('hidden');
    document.getElementById('cost-details').classList.add('hidden');
    document.getElementById('loading-message').classList.remove('hidden');
}

function showError() {
    document.getElementById('initial-message').classList.add('hidden');
    document.getElementById('loading-message').classList.add('hidden');
    document.getElementById('cost-details').classList.add('hidden');
    document.getElementById('error-message').classList.remove('hidden');
}

function showCostDetails(html) {
    document.getElementById('initial-message').classList.add('hidden');
    document.getElementById('loading-message').classList.add('hidden');
    document.getElementById('error-message').classList.add('hidden');
    const costDetails = document.getElementById('cost-details');
    costDetails.innerHTML = html;
    costDetails.classList.remove('hidden');
}

function selectPizza(pizzaId) {
    // Update selected state
    document.querySelectorAll('.pizza-card').forEach(card => {
        card.classList.remove('ring-2', 'ring-indigo-500');
    });
    document.querySelector(`.pizza-card[data-pizza-id="${pizzaId}"]`)
        .classList.add('ring-2', 'ring-indigo-500');

    // Show loading state
    showLoading();

    // Fetch cost breakdown
    fetch(`/pizzas/${pizzaId}/cost`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            let html = `
                <div class="space-y-6">
                    <!-- Base Cost -->
                    <div>
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Base Price</h3>
                        <p class="text-2xl font-semibold text-gray-900">
                            $${parseFloat(data.base_price).toFixed(2)}
                        </p>
                    </div>

                    <!-- Ingredients Cost -->
                    <div>
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Ingredients Cost</h3>
                        <div class="space-y-2">
            `;

            data.ingredient_costs.forEach(cost => {
                html += `
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">
                            ${cost.ingredient} (${cost.amount} ${cost.unit})
                        </span>
                        <span class="font-medium">$${parseFloat(cost.cost).toFixed(2)}</span>
                    </div>
                `;
            });

            html += `
                        </div>
                        <div class="mt-2 pt-2 border-t border-gray-200">
                            <div class="flex justify-between text-sm font-medium">
                                <span class="text-gray-900">Total Ingredients Cost</span>
                                <span class="text-indigo-600">$${parseFloat(data.total_cost).toFixed(2)}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Final Cost -->
                    <div class="pt-4 border-t-2 border-gray-200">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-medium text-gray-900">Final Cost</span>
                            <span class="text-2xl font-bold text-indigo-600">
                                $${parseFloat(data.final_cost).toFixed(2)}
                            </span>
                        </div>
                    </div>
                `;

            if (data.insufficient_ingredients && data.insufficient_ingredients.length > 0) {
                html += `
                    <!-- Stock Warnings -->
                    <div class="mt-4 bg-yellow-50 border-l-4 border-yellow-400 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800">Stock Warning</h3>
                                <div class="mt-2 text-sm text-yellow-700">
                                    <ul class="list-disc list-inside">
                `;

                data.insufficient_ingredients.forEach(warning => {
                    html += `
                        <li>
                            ${warning.ingredient}: Need ${warning.required}, 
                            Missing ${warning.missing}
                        </li>
                    `;
                });

                html += `
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }

            html += `</div>`;
            showCostDetails(html);
        })
        .catch(error => {
            console.error('Error fetching cost data:', error);
            showError();
        });
}
</script>
@endpush
@endsection