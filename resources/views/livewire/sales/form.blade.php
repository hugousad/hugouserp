<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
            {{ $editMode ? __('Edit Sale') : __('New Sale') }}
        </h1>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <p class="text-gray-600 dark:text-gray-400">
            {{ __('Sale form will be implemented here. For now, use the existing POS interface.') }}
            <!-- TODO: Implement full sales form with line items, customer selection, payment processing -->
        </p>
        
        <div class="mt-6">
            <a href="{{ route('app.sales.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                {{ __('Back to Sales') }}
            </a>
            <a href="{{ route('pos.terminal') }}" class="ml-2 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                {{ __('Go to POS') }}
            </a>
        </div>
    </div>
</div>
