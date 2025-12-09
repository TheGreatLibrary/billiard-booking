@props([
    'resource' => [],
    'resourceId' => null,
    'wireSelectResource' => 'selectResource',
])

@php
    $resourceData = is_array($resource) ? $resource : $resource->toArray();
    $displayWidth = (($resourceData['rotation'] ?? 0) === 90 || ($resourceData['rotation'] ?? 0) === 270) 
        ? ($resourceData['grid_height'] ?? 1) 
        : ($resourceData['grid_width'] ?? 1);
    $displayHeight = (($resourceData['rotation'] ?? 0) === 90 || ($resourceData['rotation'] ?? 0) === 270) 
        ? ($resourceData['grid_width'] ?? 1) 
        : ($resourceData['grid_height'] ?? 1);
    
    $isSelected = $resourceId === ($resourceData['id'] ?? null);
    $isAvailable = true;
    
    if (isset($resource['state'])) {
        $state = strtolower($resourceData['state']);
        $isAvailable = in_array($state, ['available', 'доступен', 'active']);
    }
@endphp

<button
    wire:click="{{ $wireSelectResource }}({{ $resourceData['id'] ?? 0 }})"
    @disabled(!$isAvailable)
    class="absolute flex flex-col items-center justify-center
           border-2 rounded-xl transition-all duration-300 group
           {{ $isSelected 
              ? 'border-blue-500 dark:border-blue-400 bg-blue-100 dark:bg-blue-900/40 shadow-2xl ring-4 ring-blue-200 dark:ring-blue-800 z-20 scale-110' 
              : ($isAvailable 
                 ? 'border-green-500 dark:border-green-400 bg-white dark:bg-gray-700 hover:border-green-600 dark:hover:border-green-300 hover:bg-green-50 dark:hover:bg-green-900/20 hover:shadow-xl z-10 cursor-pointer' 
                 : 'border-gray-400 dark:border-gray-600 bg-gray-200 dark:bg-gray-800 cursor-not-allowed z-10 opacity-60') }}"
    style="grid-column: {{ ($resourceData['grid_x'] ?? 0) + 1 }} / span {{ $displayWidth }};
           grid-row: {{ ($resourceData['grid_y'] ?? 0) + 1 }} / span {{ $displayHeight }};
           transform: rotate({{ $resourceData['rotation'] ?? 0 }}deg);
           transform-origin: center center;
           min-width: {{ max(80, $displayWidth * 25) }}px;
           min-height: {{ max(80, $displayHeight * 25) }}px;">
    <div class="text-center pointer-events-none transform group-hover:scale-105 transition-transform">
        <div class="flex items-center justify-center gap-1 mb-1">
            <div class="text-base font-bold {{ $isSelected ? 'text-blue-900 dark:text-blue-100' : ($isAvailable ? 'text-green-900 dark:text-green-100' : 'text-gray-600 dark:text-gray-400') }}">
                {{ $resourceData['code'] ?? '??' }}
            </div>
            @if($isSelected)
                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
            @endif
        </div>
        <div class="text-xs {{ $isSelected ? 'text-blue-700 dark:text-blue-300' : ($isAvailable ? 'text-gray-600 dark:text-gray-300' : 'text-gray-500 dark:text-gray-500') }} truncate max-w-full px-1">
            {{ $resourceData['model_name'] ?? 'Стол' }}
        </div>
        @if(!$isAvailable)
            <div class="text-xs text-red-600 dark:text-red-400 font-medium mt-1 bg-red-100 dark:bg-red-900/30 px-2 py-1 rounded">
                Занят
            </div>
        @endif
    </div>
</button>