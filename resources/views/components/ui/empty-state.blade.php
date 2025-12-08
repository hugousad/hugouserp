{{-- resources/views/components/ui/empty-state.blade.php --}}
@props([
    'icon' => '📭',
    'title' => 'No data found',
    'description' => null,
    'action' => null,
    'actionLabel' => null,
])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center py-12 px-4']) }}>
    <div class="text-6xl mb-4">
        {!! $icon !!}
    </div>
    
    <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-2">
        {{ $title }}
    </h3>
    
    @if($description)
    <p class="text-sm text-slate-500 dark:text-slate-400 text-center max-w-md mb-6">
        {{ $description }}
    </p>
    @endif
    
    @if($action && $actionLabel)
    <x-ui.button href="{{ $action }}" variant="primary">
        {{ $actionLabel }}
    </x-ui.button>
    @endif
    
    {{ $slot }}
</div>
