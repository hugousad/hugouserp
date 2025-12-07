<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">{{ $editMode ? __('Edit Customer') : __('Add Customer') }}</h1>
            <p class="text-sm text-slate-500">{{ __('Fill in the customer details below') }}</p>
        </div>
        <a href="{{ route('customers.index') }}" class="erp-btn erp-btn-secondary">{{ __('Back') }}</a>
    </div>

    <form wire:submit="save" class="erp-card p-6 space-y-6">
        {{-- Loading Indicator --}}
        <x-loading-indicator target="save" :fullscreen="true" :text="__('Saving customer...')" />
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="erp-label">{{ __('Customer Name') }} <span class="text-red-500">*</span></label>
                <input type="text" wire:model="name" class="erp-input @error('name') border-red-500 @enderror" required>
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="erp-label">{{ __('Customer Type') }}</label>
                <select wire:model="customer_type" class="erp-input">
                    <option value="individual">{{ __('Individual') }}</option>
                    <option value="company">{{ __('Company') }}</option>
                </select>
            </div>

            <div>
                <label class="erp-label">{{ __('Email') }}</label>
                <input type="email" wire:model="email" class="erp-input @error('email') border-red-500 @enderror">
                @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="erp-label">{{ __('Phone') }}</label>
                <input type="text" wire:model="phone" dir="ltr" class="erp-input">
            </div>

            <div>
                <label class="erp-label">{{ __('Phone 2') }}</label>
                <input type="text" wire:model="phone2" dir="ltr" class="erp-input">
            </div>

            <div>
                <label class="erp-label">{{ __('Company Name') }}</label>
                <input type="text" wire:model="company_name" class="erp-input">
            </div>

            <div>
                <label class="erp-label">{{ __('Tax Number') }}</label>
                <input type="text" wire:model="tax_number" class="erp-input">
            </div>

            <div>
                <label class="erp-label">{{ __('Credit Limit') }}</label>
                <input type="number" wire:model="credit_limit" step="0.01" class="erp-input">
            </div>

            <div>
                <label class="erp-label">{{ __('City') }}</label>
                <input type="text" wire:model="city" class="erp-input">
            </div>

            <div>
                <label class="erp-label">{{ __('Country') }}</label>
                <input type="text" wire:model="country" class="erp-input">
            </div>

            <div class="md:col-span-2">
                <label class="erp-label">{{ __('Address') }}</label>
                <textarea wire:model="address" rows="2" class="erp-input"></textarea>
            </div>

            <div class="md:col-span-2">
                <label class="erp-label">{{ __('Notes') }}</label>
                <textarea wire:model="notes" rows="3" class="erp-input"></textarea>
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" wire:model="is_active" id="is_active" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                <label for="is_active" class="text-sm text-slate-700">{{ __('Active') }}</label>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t">
            <a href="{{ route('customers.index') }}" class="erp-btn erp-btn-secondary">{{ __('Cancel') }}</a>
            <button type="submit" class="erp-btn erp-btn-primary" wire:loading.attr="disabled" wire:target="save">
                <span wire:loading.remove wire:target="save">{{ $editMode ? __('Update') : __('Save') }}</span>
                <span wire:loading wire:target="save" class="flex items-center gap-2">
                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                    </svg>
                    {{ __('Saving...') }}
                </span>
            </button>
        </div>
    </form>
</div>
