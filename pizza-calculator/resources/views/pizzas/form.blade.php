@csrf

<div class="space-y-6">
    <!-- Name -->
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700">Pizza Name</label>
        <div class="mt-1">
            <input type="text" 
                   name="name" 
                   id="name" 
                   value="{{ old('name', $pizza->name ?? '') }}"
                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                   required>
        </div>
        @error('name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Description -->
    <div>
        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
        <div class="mt-1">
            <textarea name="description" 
                      id="description" 
                      rows="3"
                      class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('description', $pizza->description ?? '') }}</textarea>
        </div>
        @error('description')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Size -->
    <div>
        <label for="size" class="block text-sm font-medium text-gray-700">Size</label>
        <div class="mt-1">
            <select name="size" 
                    id="size" 
                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                    required>
                <option value="">Select a size</option>
                <option value="small" {{ old('size', $pizza->size ?? '') == 'small' ? 'selected' : '' }}>Small</option>
                <option value="medium" {{ old('size', $pizza->size ?? '') == 'medium' ? 'selected' : '' }}>Medium</option>
                <option value="large" {{ old('size', $pizza->size ?? '') == 'large' ? 'selected' : '' }}>Large</option>
            </select>
        </div>
        @error('size')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Base Price -->
    <div>
        <label for="base_price" class="block text-sm font-medium text-gray-700">Base Price</label>
        <div class="mt-1 relative rounded-md shadow-sm">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <span class="text-gray-500 sm:text-sm">$</span>
            </div>
            <input type="number" 
                   name="base_price" 
                   id="base_price" 
                   step="0.01"
                   min="0"
                   value="{{ old('base_price', $pizza->base_price ?? '') }}"
                   class="pl-7 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                   required>
        </div>
        @error('base_price')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Ingredients -->
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Ingredients</label>
        <div id="ingredients-container" class="space-y-4">
            @if(isset($pizza) && $pizza->ingredients->isNotEmpty())
                @foreach($pizza->ingredients as $index => $pizzaIngredient)
                    <div class="ingredient-row bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center space-x-4">
                            <div class="flex-grow">
                                <select name="ingredients[{{ $index }}][id]" 
                                        class="ingredient-select shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                        required>
                                    <option value="">Select an ingredient</option>
                                    @foreach($ingredients as $ingredient)
                                        <option value="{{ $ingredient->id }}" 
                                                data-unit="{{ $ingredient->unit }}"
                                                {{ $pizzaIngredient->id == $ingredient->id ? 'selected' : '' }}>
                                            {{ $ingredient->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="w-32">
                                <div class="flex items-center">
                                    <input type="number" 
                                           name="ingredients[{{ $index }}][amount]" 
                                           value="{{ $pizzaIngredient->pivot->amount_required }}"
                                           step="0.01"
                                           min="0.01"
                                           class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                           required>
                                    <span class="ml-2 text-sm text-gray-500 ingredient-unit">{{ $pizzaIngredient->unit }}</span>
                                </div>
                            </div>
                            <button type="button" 
                                    class="remove-ingredient text-red-600 hover:text-red-900">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="ingredient-row bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center space-x-4">
                        <div class="flex-grow">
                            <select name="ingredients[0][id]" 
                                    class="ingredient-select shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                    required>
                                <option value="">Select an ingredient</option>
                                @foreach($ingredients as $ingredient)
                                    <option value="{{ $ingredient->id }}" 
                                            data-unit="{{ $ingredient->unit }}">
                                        {{ $ingredient->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-32">
                            <div class="flex items-center">
                                <input type="number" 
                                       name="ingredients[0][amount]" 
                                       step="0.01"
                                       min="0.01"
                                       class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                       required>
                                <span class="ml-2 text-sm text-gray-500 ingredient-unit"></span>
                            </div>
                        </div>
                        <button type="button" 
                                class="remove-ingredient text-red-600 hover:text-red-900">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            @endif
        </div>

        <button type="button" 
                id="add-ingredient"
                class="mt-4 inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <i class="fas fa-plus mr-2"></i>Add Ingredient
        </button>

        @error('ingredients')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Form Actions -->
    <div class="flex justify-end space-x-3">
        <a href="{{ route('pizzas.index') }}" 
           class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-lg transition-colors duration-200">
            Cancel
        </a>
        <button type="submit" 
                class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
            {{ isset($pizza) ? 'Update Pizza' : 'Create Pizza' }}
        </button>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('ingredients-container');
    const addButton = document.getElementById('add-ingredient');
    let ingredientCount = container.querySelectorAll('.ingredient-row').length;

    // Update unit when ingredient is selected
    function updateUnit(select) {
        const unit = select.options[select.selectedIndex].dataset.unit;
        const row = select.closest('.ingredient-row');
        const unitSpan = row.querySelector('.ingredient-unit');
        unitSpan.textContent = unit;
    }

    // Add new ingredient row
    addButton.addEventListener('click', function() {
        const template = container.querySelector('.ingredient-row').cloneNode(true);
        const selects = template.querySelectorAll('select');
        const inputs = template.querySelectorAll('input');
        
        // Update names and clear values
        selects.forEach(select => {
            select.name = `ingredients[${ingredientCount}][id]`;
            select.value = '';
        });
        
        inputs.forEach(input => {
            input.name = `ingredients[${ingredientCount}][amount]`;
            input.value = '';
        });

        template.querySelector('.ingredient-unit').textContent = '';
        container.appendChild(template);
        ingredientCount++;
    });

    // Remove ingredient row
    container.addEventListener('click', function(e) {
        if (e.target.closest('.remove-ingredient')) {
            const rows = container.querySelectorAll('.ingredient-row');
            if (rows.length > 1) {
                e.target.closest('.ingredient-row').remove();
            }
        }
    });

    // Update unit when ingredient is selected
    container.addEventListener('change', function(e) {
        if (e.target.classList.contains('ingredient-select')) {
            updateUnit(e.target);
        }
    });

    // Initialize units for existing ingredients
    container.querySelectorAll('.ingredient-select').forEach(updateUnit);
});
</script>
@endpush