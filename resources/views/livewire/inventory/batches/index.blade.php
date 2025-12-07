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
            <h1 class="text-2xl font-bold text-slate-800">{{ __('Batch Tracking') }}</h1>
            <p class="text-sm text-slate-500">{{ __('Manage inventory batches and expiry dates') }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('inventory.batches.create') }}" class="erp-btn erp-btn-primary">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                {{ __('New Batch') }}
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-4 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm">{{ __('Total Batches') }}</p>
                    <p class="text-2xl font-bold">{{ number_format($statistics['total_batches']) }}</p>
                </div>
                <div class="bg-white/20 rounded-lg p-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl p-4 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-emerald-100 text-sm">{{ __('Active Batches') }}</p>
                    <p class="text-2xl font-bold">{{ number_format($statistics['active_batches']) }}</p>
                </div>
                <div class="bg-white/20 rounded-lg p-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl p-4 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-amber-100 text-sm">{{ __('Expiring Soon') }}</p>
                    <p class="text-2xl font-bold">{{ number_format($statistics['expired_batches']) }}</p>
                </div>
                <div class="bg-white/20 rounded-lg p-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-4 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm">{{ __('Total Quantity') }}</p>
                    <p class="text-2xl font-bold">{{ number_format($statistics['total_quantity'], 2) }}</p>
                </div>
                <div class="bg-white/20 rounded-lg p-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
                </div>
            </div>
        </div>
    </div>

    <div class="erp-card p-4">
        <div class="flex flex-col lg:flex-row gap-4 mb-6">
            <div class="flex-1 relative">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="{{ __('Search by batch number or product...') }}" class="erp-input pr-10">
            </div>
            <div class="relative">
                <select wire:model.live="status" class="erp-input w-full lg:w-40">
                    <option value="">{{ __('All Status') }}</option>
                    <option value="active">{{ __('Active') }}</option>
                    <option value="expired">{{ __('Expired') }}</option>
                    <option value="depleted">{{ __('Depleted') }}</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="erp-table">
                <thead>
                    <tr>
                        <th wire:click="sortBy('batch_number')" class="cursor-pointer hover:bg-slate-100">{{ __('Batch Number') }}</th>
                        <th>{{ __('Product') }}</th>
                        <th>{{ __('Warehouse') }}</th>
                        <th>{{ __('Quantity') }}</th>
                        <th>{{ __('Manufacturing Date') }}</th>
                        <th>{{ __('Expiry Date') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th class="text-center">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($batches as $batch)
                        <tr wire:key="batch-{{ $batch->id }}">
                            <td class="font-mono text-sm font-medium">{{ $batch->batch_number }}</td>
                            <td>{{ $batch->product->name ?? '-' }}</td>
                            <td>{{ $batch->warehouse->name ?? '-' }}</td>
                            <td class="font-medium">{{ number_format($batch->quantity, 2) }}</td>
                            <td>{{ $batch->manufacturing_date?->format('Y-m-d') ?? '-' }}</td>
                            <td>
                                @if($batch->expiry_date)
                                    <span class="@if($batch->isExpired()) text-red-600 @elseif($batch->expiry_date->lte(now()->addDays(30))) text-amber-600 @endif">
                                        {{ $batch->expiry_date->format('Y-m-d') }}
                                    </span>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($batch->status === 'active')
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-emerald-100 text-emerald-700">{{ __('Active') }}</span>
                                @elseif($batch->status === 'expired')
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-700">{{ __('Expired') }}</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-slate-100 text-slate-700">{{ __('Depleted') }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('inventory.batches.edit', $batch) }}" class="text-blue-600 hover:text-blue-800">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-12">
                                <div class="text-slate-400">
                                    <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                    <p class="text-lg font-medium">{{ __('No batches found') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($batches->hasPages())
            <div class="mt-4">{{ $batches->links() }}</div>
        @endif
    </div>
</div>
