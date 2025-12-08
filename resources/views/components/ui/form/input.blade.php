{{-- resources/views/components/ui/form/input.blade.php --}}
@props([
    'label' => null,
    'name' => null,
    'type' => 'text',
    'placeholder' => null,
    'required' => false,
    'error' => null,
    'hint' => null,
    'icon' => null,
    'iconPosition' => 'left', // left, right
])

<div class="space-y-1">
    @if($label)
    <label for="{{ $name }}" class="block text-sm font-medium text-slate-700 dark:text-slate-300">
        {{ $label }}
        @if($required)
        <span class="text-red-500">*</span>
        @endif
    </label>
    @endif
    
    <div class="relative">
        @if($icon && $iconPosition === 'left')
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            {!! $icon !!}
        </div>
        @endif
        
        <input
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $name }}"
            placeholder="{{ $placeholder }}"
            {{ $required ? 'required' : '' }}
            {{ $attributes->merge([
                'class' => 'block w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 focus:ring-emerald-500 focus:border-emerald-500 ' . 
                ($icon && $iconPosition === 'left' ? 'pl-10 ' : '') . 
                ($icon && $iconPosition === 'right' ? 'pr-10 ' : '') . 
                ($error ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : '')
            ]) }}
        />
        
        @if($icon && $iconPosition === 'right')
        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
            {!! $icon !!}
        </div>
        @endif
    </div>
    
    @if($error)
    <p class="text-sm text-red-600 dark:text-red-400">{{ $error }}</p>
    @endif
    
    @if($hint && !$error)
    <p class="text-sm text-slate-500 dark:text-slate-400">{{ $hint }}</p>
    @endif
</div>
