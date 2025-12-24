<div class="max-w-2xl mx-auto p-6">
    <div class="mb-6">
        <a href="{{ route('app.inventory.products.store-mappings', ['product' => $productId]) }}" class="inline-flex items-center text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            {{ __('Back to Store Mappings') }}
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h1 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                {{ $mappingId ? __('Edit Store Mapping') : __('Add Store Mapping') }}
            </h1>
            @if($product)
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    {{ __('Product') }}: {{ $product->name }}
                </p>
            @endif
        </div>

        <form wire:submit="save" class="p-6 space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Store') }} *</label>
                <select wire:model="store_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-emerald-500" {{ $mappingId ? 'disabled' : '' }}>
                    <option value="">{{ __('Select Store') }}</option>
                    @foreach($stores as $store)
                        <option value="{{ $store['id'] }}">{{ $store['name'] }} ({{ ucfirst($store['type']) }})</option>
                    @endforeach
                </select>
                @error('store_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('External Product ID') }} *</label>
                <input type="text" wire:model="external_id" placeholder="e.g. 1234567890" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-emerald-500 font-mono">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('The product ID in the external store') }}</p>
                @error('external_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('External SKU') }}</label>
                <input type="text" wire:model="external_sku" placeholder="{{ __('Optional') }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-emerald-500">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('The SKU in the external store (optional)') }}</p>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('app.inventory.products.store-mappings', ['product' => $productId]) }}" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                    {{ __('Cancel') }}
                </a>
                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition">
                    {{ $mappingId ? __('Update') : __('Create') }}
                </button>
            </div>
        </form>
    </div>
</div>
