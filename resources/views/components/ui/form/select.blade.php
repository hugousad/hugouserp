{{-- resources/views/components/ui/form/select.blade.php --}}
@props([
    'label' => null,
    'name' => null,
    'options' => [],
    'placeholder' => null,
    'required' => false,
    'error' => null,
    'hint' => null,
    'selected' => null,
    'autocomplete' => null,
])

@php
    $describedBy = trim(($error && $name ? $name . '-error ' : '') . ($hint && $name ? $name . '-hint' : ''));
@endphp

<div class="space-y-1">
    @if($label)
    <label for="{{ $name }}" class="block text-sm font-medium text-slate-700 dark:text-slate-300">
        {{ $label }}
        @if($required)
        <span class="text-red-500">*</span>
        @endif
    </label>
    @endif
    
    <select
        name="{{ $name }}"
        id="{{ $name }}"
        {{ $required ? 'required' : '' }}
        {{ $autocomplete ? "autocomplete=\"$autocomplete\"" : '' }}
        {{ $attributes->merge([
            'class' => 'block w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 focus:ring-emerald-500 focus:border-emerald-500 shadow-sm min-h-[44px] text-sm sm:text-base ' .
            ($error ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : '')
        ])->merge([
            'aria-invalid' => $error ? 'true' : 'false',
            'aria-required' => $required ? 'true' : 'false',
            'aria-describedby' => $describedBy ?: null,
        ]) }}
    >
        @if($placeholder)
        <option value="">{{ $placeholder }}</option>
        @endif
        
        @foreach($options as $value => $label)
        <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }}>
            {{ $label }}
        </option>
        @endforeach
        
        {{ $slot }}
    </select>

    @if($error)
    <p id="{{ $name }}-error" class="text-sm text-red-600 dark:text-red-400">{{ $error }}</p>
    @endif

    @if($hint && !$error)
    <p id="{{ $name }}-hint" class="text-sm text-slate-500 dark:text-slate-400">{{ $hint }}</p>
    @endif
</div>
