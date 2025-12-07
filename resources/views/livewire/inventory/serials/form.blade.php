<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">
                {{ $isEditing ? __('Edit Serial Number') : __('Create Serial Number') }}
            </h1>
            <p class="text-sm text-slate-500">{{ __('Serial number information and warranty') }}</p>
        </div>
        <a href="{{ route('inventory.serials.index') }}" class="erp-btn erp-btn-secondary">
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
                    <select wire:model.live="product_id" id="product_id" class="erp-input @error('product_id') border-red-500 @enderror">
                        <option value="">{{ __('Select a product') }}</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->sku }})</option>
                        @endforeach
                    </select>
                    @error('product_id') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                    <p class="text-xs text-slate-500 mt-1">{{ __('Only serialized products are shown') }}</p>
                </div>

                <div>
                    <label for="serial_number" class="block text-sm font-medium text-slate-700 mb-2">
                        {{ __('Serial Number') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="text" wire:model="serial_number" id="serial_number" class="erp-input @error('serial_number') border-red-500 @enderror">
                    @error('serial_number') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="unit_cost" class="block text-sm font-medium text-slate-700 mb-2">
                        {{ __('Unit Cost') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="number" step="0.01" wire:model="unit_cost" id="unit_cost" class="erp-input @error('unit_cost') border-red-500 @enderror">
                    @error('unit_cost') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="warehouse_id" class="block text-sm font-medium text-slate-700 mb-2">
                        {{ __('Warehouse') }}
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
                    <label for="batch_id" class="block text-sm font-medium text-slate-700 mb-2">
                        {{ __('Associated Batch') }}
                    </label>
                    <select wire:model="batch_id" id="batch_id" class="erp-input @error('batch_id') border-red-500 @enderror" @if(!$product_id) disabled @endif>
                        <option value="">{{ __('No batch') }}</option>
                        @foreach($batches as $batch)
                            <option value="{{ $batch->id }}">{{ $batch->batch_number }}</option>
                        @endforeach
                    </select>
                    @error('batch_id') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                    @if(!$product_id)
                        <p class="text-xs text-slate-500 mt-1">{{ __('Select a product first') }}</p>
                    @endif
                </div>

                <div>
                    <label for="warranty_start" class="block text-sm font-medium text-slate-700 mb-2">
                        {{ __('Warranty Start') }}
                    </label>
                    <input type="date" wire:model="warranty_start" id="warranty_start" class="erp-input @error('warranty_start') border-red-500 @enderror">
                    @error('warranty_start') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="warranty_end" class="block text-sm font-medium text-slate-700 mb-2">
                        {{ __('Warranty End') }}
                    </label>
                    <input type="date" wire:model="warranty_end" id="warranty_end" class="erp-input @error('warranty_end') border-red-500 @enderror">
                    @error('warranty_end') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
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
                <a href="{{ route('inventory.serials.index') }}" class="erp-btn erp-btn-secondary">{{ __('Cancel') }}</a>
                <button type="submit" class="erp-btn erp-btn-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove>{{ $isEditing ? __('Update Serial') : __('Create Serial') }}</span>
                    <span wire:loading>{{ __('Saving...') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>
