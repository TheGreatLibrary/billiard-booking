@props(['color' => 'gray', 'title', 'value', 'subtitle' => '', 'icon' => ''])

<div class="bg-{{ $color }}-100 rounded-lg p-4 shadow">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-lg font-semibold text-gray-700">{{ $title }}</h3>
            <p class="text-2xl font-bold text-gray-900">{{ $value }}</p>
            @if($subtitle)
                <p class="text-sm text-gray-600">{{ $subtitle }}</p>
            @endif
        </div>
        <span class="text-3xl">{{ $icon }}</span>
    </div>
</div>
