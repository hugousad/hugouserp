{{-- resources/views/components/ui/form/textarea.blade.php --}}
@props([
    'label' => null,
    'name' => null,
    'placeholder' => null,
    'required' => false,
    'error' => null,
    'hint' => null,
    'rows' => 4,
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
    
    <textarea
        name="{{ $name }}"
        id="{{ $name }}"
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        {{ $required ? 'required' : '' }}
        {{ $autocomplete ? "autocomplete=\"$autocomplete\"" : '' }}
        {{ $attributes->merge([
            'class' => 'block w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 focus:ring-emerald-500 focus:border-emerald-500 shadow-sm text-sm sm:text-base placeholder:text-slate-400 dark:placeholder:text-slate-500 ' .
            ($error ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : '')
        ])->merge([
            'aria-invalid' => $error ? 'true' : 'false',
            'aria-required' => $required ? 'true' : 'false',
            'aria-describedby' => $describedBy ?: null,
        ]) }}
    >{{ $slot }}</textarea>

    @if($error)
    <p id="{{ $name }}-error" class="text-sm text-red-600 dark:text-red-400">{{ $error }}</p>
    @endif

    @if($hint && !$error)
    <p id="{{ $name }}-hint" class="text-sm text-slate-500 dark:text-slate-400">{{ $hint }}</p>
    @endif
</div>
