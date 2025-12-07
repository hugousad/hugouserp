<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">
                {{ $isEditing ? __('Edit Batch') : __('Create Batch') }}
            </h1>
            <p class="text-sm text-slate-500">{{ __('Batch information and tracking') }}</p>
        </div>
        <a href="{{ route('inventory.batches.index') }}" class="erp-btn erp-btn-secondary">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            {{ __('Back to List') }}
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200">
        <form wire:submit="save" class="p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label for="product_id" class="block text-sm font-medium text-slate-700 mb-2">
                        {{ __('Product') }} <span class="text-red-500">*</span>
                    </label>
                    <select wire:model="product_id" id="product_id" class="erp-input @error('product_id') border-red-500 @enderror">
                        <option value="">{{ __('Select a product') }}</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->sku }})</option>
                        @endforeach
                    </select>
                    @error('product_id') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                    <p class="text-xs text-slate-500 mt-1">{{ __('Only batch-tracked products are shown') }}</p>
                </div>

                <div>
                    <label for="warehouse_id" class="block text-sm font-medium text-slate-700 mb-2">
                        {{ __('Warehouse') }} <span class="text-red-500">*</span>
                    </label>
                    <select wire:model="warehouse_id" id="warehouse_id" class="erp-input @error('warehouse_id') border-red-500 @enderror">
                        <option value="">{{ __('Select a warehouse') }}</option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                    @error('warehouse_id') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="batch_number" class="block text-sm font-medium text-slate-700 mb-2">
                        {{ __('Batch Number') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="text" wire:model="batch_number" id="batch_number" class="erp-input @error('batch_number') border-red-500 @enderror">
                    @error('batch_number') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="quantity" class="block text-sm font-medium text-slate-700 mb-2">
                        {{ __('Quantity') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="number" step="0.01" wire:model="quantity" id="quantity" class="erp-input @error('quantity') border-red-500 @enderror">
                    @error('quantity') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="unit_cost" class="block text-sm font-medium text-slate-700 mb-2">
                        {{ __('Unit Cost') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="number" step="0.01" wire:model="unit_cost" id="unit_cost" class="erp-input @error('unit_cost') border-red-500 @enderror">
                    @error('unit_cost') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="manufacturing_date" class="block text-sm font-medium text-slate-700 mb-2">
                        {{ __('Manufacturing Date') }}
                    </label>
                    <input type="date" wire:model="manufacturing_date" id="manufacturing_date" class="erp-input @error('manufacturing_date') border-red-500 @enderror">
                    @error('manufacturing_date') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="expiry_date" class="block text-sm font-medium text-slate-700 mb-2">
                        {{ __('Expiry Date') }}
                    </label>
                    <input type="date" wire:model="expiry_date" id="expiry_date" class="erp-input @error('expiry_date') border-red-500 @enderror">
                    @error('expiry_date') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="supplier_batch_ref" class="block text-sm font-medium text-slate-700 mb-2">
                        {{ __('Supplier Batch Ref') }}
                    </label>
                    <input type="text" wire:model="supplier_batch_ref" id="supplier_batch_ref" class="erp-input @error('supplier_batch_ref') border-red-500 @enderror">
                    @error('supplier_batch_ref') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-slate-700 mb-2">
                        {{ __('Notes') }}
                    </label>
                    <textarea wire:model="notes" id="notes" rows="3" class="erp-input @error('notes') border-red-500 @enderror"></textarea>
                    @error('notes') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                <a href="{{ route('inventory.batches.index') }}" class="erp-btn erp-btn-secondary">{{ __('Cancel') }}</a>
                <button type="submit" class="erp-btn erp-btn-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove>{{ $isEditing ? __('Update Batch') : __('Create Batch') }}</span>
                    <span wire:loading>{{ __('Saving...') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>
