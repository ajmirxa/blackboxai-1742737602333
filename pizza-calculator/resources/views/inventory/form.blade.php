@csrf

<div class="space-y-6">
    <!-- Ingredient Selection -->
    <div>
        <label for="ingredient_id" class="block text-sm font-medium text-gray-700">Ingredient</label>
        <div class="mt-1">
            <select name="ingredient_id" 
                    id="ingredient_id" 
                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                    {{ isset($inventory) ? 'disabled' : 'required' }}>
                <option value="">Select an ingredient</option>
                @foreach($ingredients as $ingredient)
                    <option value="{{ $ingredient->id }}" 
                            {{ old('ingredient_id', $inventory->ingredient_id ?? '') == $ingredient->id ? 'selected' : '' }}>
                        {{ $ingredient->name }} ({{ $ingredient->unit }})
                    </option>
                @endforeach
            </select>
        </div>
        @error('ingredient_id')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Quantity -->
    <div>
        <label for="quantity" class="block text-sm font-medium text-gray-700">Quantity</label>
        <div class="mt-1">
            <input type="number" 
                   name="quantity" 
                   id="quantity" 
                   step="0.01"
                   min="{{ isset($inventory) ? $inventory->quantity - $inventory->remaining_quantity : '0.01' }}"
                   value="{{ old('quantity', $inventory->quantity ?? '') }}"
                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                   required>
        </div>
        @if(isset($inventory))
            <p class="mt-1 text-sm text-gray-500">
                Minimum quantity: {{ number_format($inventory->quantity - $inventory->remaining_quantity, 2) }}
                ({{ number_format($inventory->quantity - $inventory->remaining_quantity, 2) }} units already used)
            </p>
        @endif
        @error('quantity')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Purchase Price -->
    <div>
        <label for="purchase_price" class="block text-sm font-medium text-gray-700">Purchase Price (per unit)</label>
        <div class="mt-1 relative rounded-md shadow-sm">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <span class="text-gray-500 sm:text-sm">$</span>
            </div>
            <input type="number" 
                   name="purchase_price" 
                   id="purchase_price" 
                   step="0.01"
                   min="0.01"
                   value="{{ old('purchase_price', $inventory->purchase_price ?? '') }}"
                   class="pl-7 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                   required>
        </div>
        @error('purchase_price')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Purchase Date -->
    <div>
        <label for="purchased_at" class="block text-sm font-medium text-gray-700">Purchase Date</label>
        <div class="mt-1">
            <input type="datetime-local" 
                   name="purchased_at" 
                   id="purchased_at" 
                   value="{{ old('purchased_at', isset($inventory) ? $inventory->purchased_at->format('Y-m-d\TH:i') : '') }}"
                   max="{{ now()->format('Y-m-d\TH:i') }}"
                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                   required>
        </div>
        @error('purchased_at')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Notes -->
    <div>
        <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
        <div class="mt-1">
            <textarea name="notes" 
                      id="notes" 
                      rows="3"
                      class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('notes', $inventory->notes ?? '') }}</textarea>
        </div>
        @error('notes')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Form Actions -->
    <div class="flex justify-end space-x-3">
        <a href="{{ route('inventory.index') }}" 
           class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-lg transition-colors duration-200">
            Cancel
        </a>
        <button type="submit" 
                class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
            {{ isset($inventory) ? 'Update Stock' : 'Add Stock' }}
        </button>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set default purchase date to now if creating new inventory
    if (!document.querySelector('#purchased_at').value) {
        document.querySelector('#purchased_at').value = new Date().toISOString().slice(0, 16);
    }
});
</script>
@endpush