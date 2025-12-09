@props([
    'name' => null,
    'label' => '',
    'type' => 'text',
    'placeholder' => '',
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'wireModel' => null,  // Это строка с именем модели, например "form.email"
    'autocomplete' => null,
    'autofocus' => false,
])

@php
    $inputName = $name ?? str_replace('.', '_', $wireModel);
    $modelName = $wireModel ?? $inputName;
    $hasError = $errors->has($modelName);
    
    // Генерируем wire:model директиву ПРАВИЛЬНО
    $wireModelDirective = $wireModel ? "wire:model=\"{$wireModel}\"" : '';
@endphp

<div {{ $attributes->merge(['class' => 'mb-4']) }}>
    @if($label)
        <label for="{{ $inputName }}" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    
    <input
        type="{{ $type }}"
        id="{{ $inputName }}"
        name="{{ $inputName }}"
        {!! $wireModelDirective !!}
        placeholder="{{ $placeholder }}"
        @if($required) required @endif
        @if($disabled) disabled @endif
        @if($readonly) readonly @endif
        @if($autofocus) autofocus @endif
        autocomplete="{{ $autocomplete ?? ($type === 'password' ? 'new-password' : 'off') }}"
        class="w-full px-4 py-3 rounded-lg border 
               {{ $hasError 
                  ? 'border-red-500 dark:border-red-400 focus:ring-red-500' 
                  : 'border-gray-300 dark:border-gray-600 focus:ring-amber-500' 
               }}
               bg-white dark:bg-gray-700 text-gray-900 dark:text-white
               focus:ring-2 focus:border-transparent
               transition-colors duration-200
               disabled:opacity-50 disabled:cursor-not-allowed"
        {{ $attributes->except(['class']) }}
    >
    
    @if($hasError)
        <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center gap-1">
            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
            </svg>
            {{ $errors->first($modelName) }}
        </p>
    @endif
</div>