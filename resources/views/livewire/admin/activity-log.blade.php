<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Activity Log') }}</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Track all system activities and changes') }}</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Search') }}</label>
                <input type="text" wire:model.live.debounce.300ms="search" 
                       placeholder="{{ __('Search activities...') }}"
                       class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Log Type') }}</label>
                <select wire:model.live="logType" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm">
                    <option value="">{{ __('All Types') }}</option>
                    @foreach($logTypes as $type)
                        <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Event') }}</label>
                <select wire:model.live="eventType" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm">
                    <option value="">{{ __('All Events') }}</option>
                    @foreach($eventTypes as $event)
                        <option value="{{ $event }}">{{ __(ucfirst($event)) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('From Date') }}</label>
                <input type="date" wire:model.live="dateFrom" 
                       class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('To Date') }}</label>
                <input type="date" wire:model.live="dateTo" 
                       class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm">
            </div>
            <div class="flex items-end sm:col-span-2 lg:col-span-3 xl:col-span-1">
                <button wire:click="clearFilters" class="w-full px-4 py-2 text-sm bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                    {{ __('Clear Filters') }}
                </button>
            </div>
        </div>
    </div>

    <!-- Activity List -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Date/Time') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('User') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Event') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Subject') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Description') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Changes') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($activities as $activity)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            <div>{{ $activity->created_at->format('M d, Y') }}</div>
                            <div class="text-xs">{{ $activity->created_at->format('H:i:s') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($activity->causer)
                                <div class="flex items-center">
                                    <div class="h-8 w-8 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center">
                                        <span class="text-xs font-medium text-indigo-700 dark:text-indigo-300">
                                            {{ strtoupper(substr($activity->causer->name ?? 'S', 0, 2)) }}
                                        </span>
                                    </div>
                                    <div class="ms-3">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $activity->causer->name ?? __('System') }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $activity->causer->email ?? '' }}</div>
                                    </div>
                                </div>
                            @else
                                <span class="text-gray-500 dark:text-gray-400">{{ __('System') }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($activity->event === 'created') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                @elseif($activity->event === 'updated') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300
                                @elseif($activity->event === 'deleted') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300
                                @elseif($activity->event === 'restored') bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300
                                @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                                @endif">
                                {{ __(ucfirst($activity->event ?? 'action')) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            @if($activity->subject)
                                <div class="font-medium text-gray-900 dark:text-white">
                                    {{ class_basename($activity->subject_type) }}
                                </div>
                                <div class="text-xs">ID: {{ $activity->subject_id }}</div>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 max-w-xs truncate">
                            {{ $activity->description }}
                        </td>
                        <td class="px-6 py-4 text-sm" x-data="{ showModal: false }">
                            @if($activity->properties && $activity->properties->count() > 0)
                                <button type="button" 
                                        @click.prevent="showModal = true"
                                        class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 text-xs font-medium">
                                    {{ __('View Changes') }}
                                </button>
                                
                                {{-- Modal --}}
                                <div x-show="showModal" 
                                     x-cloak
                                     @click.self="showModal = false"
                                     class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
                                     style="display: none;">
                                    <div @click.stop 
                                         x-transition:enter="transition ease-out duration-300"
                                         x-transition:enter-start="opacity-0 transform scale-95"
                                         x-transition:enter-end="opacity-100 transform scale-100"
                                         x-transition:leave="transition ease-in duration-200"
                                         x-transition:leave-start="opacity-100 transform scale-100"
                                         x-transition:leave-end="opacity-0 transform scale-95"
                                         class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-3xl max-h-[80vh] overflow-hidden">
                                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Activity Details') }}</h3>
                                            <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </div>
                                        <div class="px-6 py-4 overflow-auto max-h-[60vh]">
                                            <pre class="text-xs bg-gray-50 dark:bg-gray-900 p-4 rounded-lg overflow-auto whitespace-pre-wrap break-words">{{ json_encode($activity->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                        </div>
                                        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700 flex justify-end">
                                            <button @click="showModal = false" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                                                {{ __('Close') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ __('No activities found') }}</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $activities->links() }}
    </div>
</div>
