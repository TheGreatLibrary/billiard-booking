@props([
    'type' => 'info',
    'message' => null,
    'title' => null,
])

@if($message)
    @php
        $typeClasses = [
            'success' => 'bg-green-50 dark:bg-green-900/30 border-green-200 dark:border-green-800 text-green-800 dark:text-green-200',
            'error' => 'bg-red-50 dark:bg-red-900/30 border-red-200 dark:border-red-800 text-red-800 dark:text-red-200',
            'warning' => 'bg-yellow-50 dark:bg-yellow-900/30 border-yellow-200 dark:border-yellow-800 text-yellow-800 dark:text-yellow-200',
            'info' => 'bg-blue-50 dark:bg-blue-900/30 border-blue-200 dark:border-blue-800 text-blue-800 dark:text-blue-200',
        ][$type] ?? 'bg-gray-50 dark:bg-gray-900/30 border-gray-200 dark:border-gray-800 text-gray-800 dark:text-gray-200';
        
        $icons = [
            'success' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
            'error' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
            'warning' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
            'info' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        ][$type] ?? 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z';
    @endphp

    <div {{ $attributes->merge(['class' => 'rounded-xl border p-6 mb-6 transition-all duration-300 ' . $typeClasses]) }}>
        <div class="flex items-start gap-4">
            <div class="flex-shrink-0 mt-1">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icons }}"></path>
                </svg>
            </div>
            <div class="flex-1">
                @if($title)
                    <h3 class="font-semibold text-lg mb-1">{{ $title }}</h3>
                @endif
                <p class="{{ $title ? 'text-sm' : '' }}">{{ $message }}</p>
            </div>
            <button type="button" 
                    class="flex-shrink-0 text-current hover:opacity-75 transition-opacity"
                    onclick="this.parentElement.parentElement.remove()">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </div>
@endif