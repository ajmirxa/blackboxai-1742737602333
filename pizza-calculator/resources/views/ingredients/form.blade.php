@csrf

<div class="space-y-6">
    <!-- Name -->
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
        <div class="mt-1">
            <input type="text" 
                   name="name" 
                   id="name" 
                   value="{{ old('name', $ingredient->name ?? '') }}"
                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                   required>
        </div>
        @error('name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Unit -->
    <div>
        <label for="unit" class="block text-sm font-medium text-gray-700">Unit of Measurement</label>
        <div class="mt-1">
            <select name="unit" 
                    id="unit" 
                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                    required>
                <option value="">Select a unit</option>
                <option value="grams" {{ old('unit', $ingredient->unit ?? '') == 'grams' ? 'selected' : '' }}>Grams</option>
                <option value="kilograms" {{ old('unit', $ingredient->unit ?? '') == 'kilograms' ? 'selected' : '' }}>Kilograms</option>
                <option value="pieces" {{ old('unit', $ingredient->unit ?? '') == 'pieces' ? 'selected' : '' }}>Pieces</option>
                <option value="milliliters" {{ old('unit', $ingredient->unit ?? '') == 'milliliters' ? 'selected' : '' }}>Milliliters</option>
                <option value="liters" {{ old('unit', $ingredient->unit ?? '') == 'liters' ? 'selected' : '' }}>Liters</option>
            </select>
        </div>
        @error('unit')
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
                      class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('description', $ingredient->description ?? '') }}</textarea>
        </div>
        @error('description')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Form Actions -->
    <div class="flex justify-end space-x-3">
        <a href="{{ route('ingredients.index') }}" 
           class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-lg transition-colors duration-200">
            Cancel
        </a>
        <button type="submit" 
                class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
            {{ isset($ingredient) ? 'Update Ingredient' : 'Create Ingredient' }}
        </button>
    </div>
</div>