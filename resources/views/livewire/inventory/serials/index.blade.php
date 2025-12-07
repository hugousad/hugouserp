<div class="space-y-6">
    <div wire:loading.delay class="fixed inset-0 bg-slate-900/20 z-50 flex items-center justify-center">
        <div class="bg-white rounded-xl shadow-xl p-6 flex items-center gap-3">
            <svg class="animate-spin h-6 w-6 text-emerald-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-slate-700 font-medium">{{ __('Loading...') }}</span>
        </div>
    </div>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">{{ __('Serial Tracking') }}</h1>
            <p class="text-sm text-slate-500">{{ __('Manage serial numbers and warranty tracking') }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('inventory.serials.create') }}" class="erp-btn erp-btn-primary">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                {{ __('New Serial') }}
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-4 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm">{{ __('Total Serials') }}</p>
                    <p class="text-2xl font-bold">{{ number_format($statistics['total_serials']) }}</p>
                </div>
                <div class="bg-white/20 rounded-lg p-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/></svg>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl p-4 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-emerald-100 text-sm">{{ __('In Stock') }}</p>
                    <p class="text-2xl font-bold">{{ number_format($statistics['in_stock']) }}</p>
                </div>
                <div class="bg-white/20 rounded-lg p-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl p-4 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-amber-100 text-sm">{{ __('Sold') }}</p>
                    <p class="text-2xl font-bold">{{ number_format($statistics['sold']) }}</p>
                </div>
                <div class="bg-white/20 rounded-lg p-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-4 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm">{{ __('Under Warranty') }}</p>
                    <p class="text-2xl font-bold">{{ number_format($statistics['under_warranty']) }}</p>
                </div>
                <div class="bg-white/20 rounded-lg p-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
            </div>
        </div>
    </div>

    <div class="erp-card p-4">
        <div class="flex flex-col lg:flex-row gap-4 mb-6">
            <div class="flex-1 relative">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="{{ __('Search by serial number or product...') }}" class="erp-input pr-10">
            </div>
            <div class="relative">
                <select wire:model.live="status" class="erp-input w-full lg:w-40">
                    <option value="">{{ __('All Status') }}</option>
                    <option value="in_stock">{{ __('In Stock') }}</option>
                    <option value="sold">{{ __('Sold') }}</option>
                    <option value="returned">{{ __('Returned') }}</option>
                    <option value="defective">{{ __('Defective') }}</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="erp-table">
                <thead>
                    <tr>
                        <th wire:click="sortBy('serial_number')" class="cursor-pointer hover:bg-slate-100">{{ __('Serial Number') }}</th>
                        <th>{{ __('Product') }}</th>
                        <th>{{ __('Warehouse') }}</th>
                        <th>{{ __('Customer') }}</th>
                        <th>{{ __('Warranty End') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th class="text-center">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($serials as $serial)
                        <tr wire:key="serial-{{ $serial->id }}">
                            <td class="font-mono text-sm font-medium">{{ $serial->serial_number }}</td>
                            <td>{{ $serial->product->name ?? '-' }}</td>
                            <td>{{ $serial->warehouse->name ?? '-' }}</td>
                            <td>{{ $serial->customer->name ?? '-' }}</td>
                            <td>
                                @if($serial->warranty_end)
                                    <span class="@if($serial->isWarrantyActive()) text-emerald-600 @else text-slate-500 @endif">
                                        {{ $serial->warranty_end->format('Y-m-d') }}
                                    </span>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($serial->status === 'in_stock')
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-emerald-100 text-emerald-700">{{ __('In Stock') }}</span>
                                @elseif($serial->status === 'sold')
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-700">{{ __('Sold') }}</span>
                                @elseif($serial->status === 'returned')
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-amber-100 text-amber-700">{{ __('Returned') }}</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-700">{{ __('Defective') }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('inventory.serials.edit', $serial) }}" class="text-blue-600 hover:text-blue-800">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-12">
                                <div class="text-slate-400">
                                    <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/></svg>
                                    <p class="text-lg font-medium">{{ __('No serial numbers found') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($serials->hasPages())
            <div class="mt-4">{{ $serials->links() }}</div>
        @endif
    </div>
</div>
