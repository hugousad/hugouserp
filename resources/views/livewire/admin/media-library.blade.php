<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">{{ __('Media Library') }}</h1>
            <p class="text-sm text-slate-500">{{ __('Manage your uploaded files and images') }}</p>
        </div>
    </div>

    @if(session()->has('success'))
        <div class="p-3 bg-emerald-50 text-emerald-700 rounded-lg">{{ session('success') }}</div>
    @endif
    @if(session()->has('error'))
        <div class="p-3 bg-red-50 text-red-700 rounded-lg">{{ session('error') }}</div>
    @endif

    <!-- Upload Section -->
    @can('media.upload')
    <div class="erp-card p-6">
        <h2 class="text-lg font-semibold text-slate-800 mb-4">{{ __('Upload') }}</h2>
        <div class="border-2 border-dashed border-slate-300 rounded-lg p-8 text-center hover:border-emerald-500 transition-colors"
             x-data="{ dragging: false }"
             @dragover.prevent="dragging = true"
             @dragleave.prevent="dragging = false"
             @drop.prevent="dragging = false"
             :class="{ 'border-emerald-500 bg-emerald-50': dragging }">
            <input type="file" wire:model="files" multiple class="hidden" id="file-upload" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.txt,.csv,.ppt,.pptx">
            <label for="file-upload" class="cursor-pointer">
                <svg class="mx-auto h-12 w-12 text-slate-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <p class="mt-4 text-sm text-slate-600">{{ __('Drop files here or click to upload') }}</p>
                <p class="mt-1 text-xs text-slate-500">{{ __('Supported formats') }}: JPG, PNG, GIF, WebP, PDF, DOC, XLS, PPT, TXT, CSV</p>
                <p class="mt-1 text-xs text-slate-500">{{ __('Maximum file size') }}: 10 {{ __('MB') }}</p>
            </label>
        </div>
        <div wire:loading wire:target="files" class="mt-4 text-center">
            <div class="inline-flex items-center gap-2 text-emerald-600">
                <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>{{ __('Uploading...') }}</span>
            </div>
        </div>
    </div>
    @endcan

    <!-- Filters -->
    <div class="erp-card p-4">
        <div class="flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-[200px]">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="{{ __('Search by name...') }}" class="erp-input">
            </div>
            <div>
                <select wire:model.live="filterType" class="erp-input">
                    <option value="all">{{ __('All Files') }}</option>
                    <option value="images">{{ __('Images') }}</option>
                    <option value="documents">{{ __('Documents') }}</option>
                </select>
            </div>
            @can('media.view-others')
            <div>
                <select wire:model.live="filterOwner" class="erp-input">
                    <option value="all">{{ __('All Users Files') }}</option>
                    <option value="mine">{{ __('My Files') }}</option>
                </select>
            </div>
            @endcan
            <div>
                <select wire:model.live="sortOrder" class="erp-input">
                    <option value="newest">{{ __('Newest First') }}</option>
                    <option value="oldest">{{ __('Oldest First') }}</option>
                    <option value="name">{{ __('Name (A-Z)') }}</option>
                    <option value="size">{{ __('Size (Largest)') }}</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Media Grid -->
    <div wire:loading.delay wire:target="loadMedia" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
        @for($i = 0; $i < 10; $i++)
        <div class="erp-card overflow-hidden">
            <div class="aspect-square bg-slate-200 animate-pulse"></div>
            <div class="p-3 space-y-2">
                <div class="h-4 bg-slate-200 rounded animate-pulse"></div>
                <div class="h-3 bg-slate-200 rounded w-2/3 animate-pulse"></div>
            </div>
        </div>
        @endfor
    </div>
    
    <div wire:loading.remove wire:target="loadMedia" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
        @forelse($media as $item)
            <div class="erp-card overflow-hidden group">
                <div class="aspect-square bg-slate-100 relative">
                    @if($item['is_image'] && $item['thumbnail_url'])
                        <img src="{{ $item['thumbnail_url'] }}" alt="{{ $item['name'] }}" class="w-full h-full object-cover" loading="lazy">
                    @elseif($item['is_image'])
                        <img src="{{ $item['url'] }}" alt="{{ $item['name'] }}" class="w-full h-full object-cover" loading="lazy">
                    @else
                        {{-- File card view with extension-specific icons --}}
                        <div class="w-full h-full flex flex-col items-center justify-center p-4">
                            @php
                                $ext = strtolower($item['extension'] ?? '');
                            @endphp
                            @if(in_array($ext, ['pdf']))
                                <svg class="h-16 w-16 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M9.5,16V18H8V16H9.5M11,18V16H11.5A1.5,1.5 0 0,0 13,14.5V14.5A1.5,1.5 0 0,0 11.5,13H10V18H11M15,18V13H16V18H15M11.5,14H11V15.5H11.5A0.5,0.5 0 0,0 12,15V14.5A0.5,0.5 0 0,0 11.5,14M13,9V3.5L18.5,9H13Z"/>
                                </svg>
                            @elseif(in_array($ext, ['doc', 'docx']))
                                <svg class="h-16 w-16 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M15.2,20H13.8L12,13.2L10.2,20H8.8L6.6,11H8.1L9.5,17.8L11.3,11H12.6L14.4,17.8L15.8,11H17.3L15.2,20M13,9V3.5L18.5,9H13Z"/>
                                </svg>
                            @elseif(in_array($ext, ['xls', 'xlsx', 'csv']))
                                <svg class="h-16 w-16 text-green-500" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M10,19H8V14H10V19M14,19H12V14H14V19M16,11H8V9H16V11M13,9V3.5L18.5,9H13Z"/>
                                </svg>
                            @elseif(in_array($ext, ['ppt', 'pptx']))
                                <svg class="h-16 w-16 text-orange-500" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M9.5,11.5C9.5,10.12 10.62,9 12,9C13.38,9 14.5,10.12 14.5,11.5C14.5,12.88 13.38,14 12,14H10V18H8V9H10V11.5H11C11,10.95 11.45,10.5 12,10.5C12.55,10.5 13,10.95 13,11.5C13,12.05 12.55,12.5 12,12.5H10V11.5H9.5M13,9V3.5L18.5,9H13Z"/>
                                </svg>
                            @else
                                <svg class="h-16 w-16 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                            @endif
                            <span class="text-sm text-slate-500 mt-2 uppercase font-medium">{{ $ext }}</span>
                        </div>
                    @endif
                    
                    <!-- Actions Overlay -->
                    <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                        <a href="{{ $item['url'] }}" target="_blank" class="p-2 bg-white rounded-full hover:bg-slate-100" title="{{ __('View') }}">
                            <svg class="h-5 w-5 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </a>
                        @if(auth()->user()->can('media.manage') || (auth()->user()->can('media.delete') && $item['user_id'] === auth()->id()))
                        <button wire:click="delete({{ $item['id'] }})" wire:confirm="{{ __('Are you sure you want to delete this file?') }}" class="p-2 bg-white rounded-full hover:bg-red-100" title="{{ __('Delete') }}">
                            <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                        @endif
                    </div>
                </div>
                
                <div class="p-3">
                    <p class="text-sm font-medium text-slate-800 truncate" title="{{ $item['original_name'] }}">{{ $item['name'] }}</p>
                    <div class="flex items-center justify-between mt-1">
                        <span class="text-xs text-slate-500">{{ $item['human_size'] }}</span>
                        @if($item['compression_ratio'])
                            <span class="text-xs text-emerald-600" title="{{ __('Compression ratio') }}">-{{ $item['compression_ratio'] }}%</span>
                        @endif
                    </div>
                    <div class="flex items-center justify-between mt-1">
                        <p class="text-xs text-slate-400">{{ $item['user_name'] }}</p>
                        <p class="text-xs text-slate-400">{{ $item['created_at'] }}</p>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <p class="mt-2 text-sm text-slate-500">{{ __('No media files found') }}</p>
                <p class="mt-1 text-xs text-slate-400">{{ __('Upload your first file to get started') }}</p>
            </div>
        @endforelse
    </div>

    <!-- Load More Button -->
    @if($hasMorePages)
        <div class="mt-6 text-center">
            <button 
                type="button"
                wire:click="loadMore"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-50 cursor-wait"
                wire:target="loadMore"
                class="inline-flex items-center gap-2 px-8 py-3 text-sm font-medium text-emerald-700 bg-emerald-50 hover:bg-emerald-100 rounded-lg transition"
            >
                <span wire:loading.remove wire:target="loadMore">{{ __('Load More') }}</span>
                <span wire:loading wire:target="loadMore" class="flex items-center gap-2">
                    <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ __('Loading...') }}
                </span>
            </button>
        </div>
    @endif
    
    <!-- Media count info -->
    @if(count($media) > 0)
        <div class="text-center text-sm text-slate-500">
            {{ __('Showing :count files', ['count' => count($media)]) }}
            @if($hasMorePages)
                · {{ __('Scroll down or click Load More for more') }}
            @endif
        </div>
    @endif
</div>
