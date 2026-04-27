@props([
    'placeData' => [],
    'resourceId' => null,
    'wireSelectResource' => 'selectResource',
    'multiSelect' => false,
    'selectedResources' => [],
    'availableResourceIds' => [],
    'resourcePrices' => [],
])

@php
    $gridWidth = $placeData['place']['grid_width'] ?? 20;
    $gridHeight = $placeData['place']['grid_height'] ?? 10;
    $zones = $placeData['zones'] ?? [];
    $resources = $placeData['resources'] ?? [];
    $cellSize = 100 / $gridWidth;
    $aspectRatio = ($gridHeight / $gridWidth) * 100;
@endphp

<div class="hall-map-wrapper mb-8">
    <div class="relative rounded-2xl overflow-hidden border border-gray-200/60 dark:border-gray-700/60"
         style="background: 
            radial-gradient(circle at 20% 50%, rgba(99, 102, 241, 0.03) 0%, transparent 50%),
            radial-gradient(circle at 80% 50%, rgba(16, 185, 129, 0.03) 0%, transparent 50%),
            var(--hall-bg, #f8fafc);">

        {{-- Тонкая сетка пола --}}
        <div class="absolute inset-0 opacity-[0.04] dark:opacity-[0.06]" 
             style="background-image: 
                linear-gradient(rgba(0,0,0,0.3) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0,0,0,0.3) 1px, transparent 1px);
                background-size: {{ $cellSize }}% {{ 100 / $gridHeight }}%;"></div>

        {{-- Контейнер с пропорциями --}}
        <div class="relative w-full" style="padding-bottom: {{ $aspectRatio }}%;">
            
            {{-- Зоны — поячеечная отрисовка (сохраняет форму с пробелами) --}}
            @foreach($zones as $zone)
                @php
                    $coordinates = is_string($zone['coordinates']) 
                        ? json_decode($zone['coordinates'], true) 
                        : ($zone['coordinates'] ?? []);
                    if (empty($coordinates)) continue;
                    $zoneColor = $zone['color'] ?? '#3B82F6';
                    
                    // Bounds для надписи
                    $minX = PHP_INT_MAX; $minY = PHP_INT_MAX;
                    $maxX = 0; $maxY = 0;
                    foreach ($coordinates as $coord) {
                        $cx = (int)($coord['x'] ?? 0); $cy = (int)($coord['y'] ?? 0);
                        $minX = min($minX, $cx); $minY = min($minY, $cy);
                        $maxX = max($maxX, $cx); $maxY = max($maxY, $cy);
                    }
                @endphp

                {{-- Каждая ячейка зоны --}}
                @foreach($coordinates as $coord)
                    @php
                        $cx = (int)($coord['x'] ?? 0);
                        $cy = (int)($coord['y'] ?? 0);
                    @endphp
                    <div class="absolute"
                         style="left: {{ ($cx / $gridWidth) * 100 }}%;
                                top: {{ ($cy / $gridHeight) * 100 }}%;
                                width: {{ (1 / $gridWidth) * 100 }}%;
                                height: {{ (1 / $gridHeight) * 100 }}%;
                                background: {{ $zoneColor }}18;
                                border: 0.5px solid {{ $zoneColor }}25;"></div>
                @endforeach

                {{-- Надпись по центру bounds --}}
                <div class="absolute flex items-center justify-center pointer-events-none"
                     style="left: {{ ($minX / $gridWidth) * 100 }}%;
                            top: {{ ($minY / $gridHeight) * 100 }}%;
                            width: {{ (($maxX - $minX + 1) / $gridWidth) * 100 }}%;
                            height: {{ (($maxY - $minY + 1) / $gridHeight) * 100 }}%;">
                    <span class="text-[10px] sm:text-xs font-bold uppercase tracking-wider drop-shadow-sm opacity-50"
                          style="color: {{ $zoneColor }};">{{ $zone['name'] ?? '' }}</span>
                </div>
            @endforeach

            {{-- Столы --}}
            @foreach($resources as $resource)
                @php
                    $rid = $resource['id'] ?? 0;
                    $rw = $resource['grid_width'] ?? 2;
                    $rh = $resource['grid_height'] ?? 1;
                    $rx = $resource['grid_x'] ?? 0;
                    $ry = $resource['grid_y'] ?? 0;

                    $left = ($rx / $gridWidth) * 100;
                    $top = ($ry / $gridHeight) * 100;
                    $width = ($rw / $gridWidth) * 100;
                    $height = ($rh / $gridHeight) * 100;

                    if ($multiSelect) {
                        $isSelected = in_array($rid, $selectedResources);
                        $isAvailable = in_array($rid, $availableResourceIds);
                        $price = $resourcePrices[$rid] ?? 0;
                    } else {
                        $isSelected = ($resourceId === $rid);
                        $isAvailable = true;
                        $price = 0;
                        if (isset($resource['state'])) {
                            $isAvailable = in_array(strtolower($resource['state']), ['available', 'active', 'доступен']);
                        }
                    }
                @endphp

                <button wire:click="{{ $wireSelectResource }}({{ $rid }})"
                    @disabled(!$isAvailable && !$isSelected)
                    class="hall-table absolute flex items-center justify-center
                           {{ $isSelected ? 'z-30 hall-table--selected' 
                              : ($isAvailable ? 'z-10 hall-table--available' : 'z-10 hall-table--occupied') }}"
                    style="left: {{ $left }}%; top: {{ $top }}%; width: {{ $width }}%; height: {{ $height }}%;"
                    title="{{ $resource['code'] ?? '' }} · {{ $resource['model_name'] ?? '' }}">

                    <div class="relative w-[90%] h-[82%] rounded-lg flex flex-col items-center justify-center gap-0.5 pointer-events-none
                                {{ $isSelected 
                                   ? 'bg-blue-500 dark:bg-blue-600 shadow-lg shadow-blue-500/30' 
                                   : ($isAvailable 
                                      ? 'bg-emerald-500/90 dark:bg-emerald-600/90 shadow-md shadow-emerald-500/20' 
                                      : 'bg-gray-300 dark:bg-gray-600 shadow-sm') }}">

                        {{-- Сукно --}}
                        <div class="absolute inset-[3px] rounded-md opacity-30
                                    {{ $isSelected ? 'bg-blue-300' : ($isAvailable ? 'bg-emerald-300' : 'bg-gray-200 dark:bg-gray-500') }}"></div>

                        <span class="relative text-white font-bold text-[11px] sm:text-xs leading-none drop-shadow-sm
                                     {{ !$isAvailable && !$isSelected ? 'text-gray-500 dark:text-gray-400' : '' }}">
                            {{ $resource['code'] ?? '??' }}
                        </span>

                        @if($multiSelect && $isAvailable && $price > 0)
                            <span class="relative text-[8px] sm:text-[9px] leading-none font-medium
                                         {{ $isSelected ? 'text-blue-100' : 'text-emerald-100' }}">
                                {{ number_format($price, 0) }}₽
                            </span>
                        @endif

                        @if($isSelected)
                            <div class="absolute -top-1.5 -right-1.5 w-4 h-4 bg-white dark:bg-gray-900 rounded-full flex items-center justify-center shadow-md">
                                <svg class="w-2.5 h-2.5 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                        @endif

                        @if(!$isAvailable && !$isSelected)
                            <div class="absolute inset-0 rounded-md flex items-center justify-center bg-gray-900/20 dark:bg-gray-900/40">
                                <svg class="w-3.5 h-3.5 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </div>
                        @endif
                    </div>
                </button>
            @endforeach
        </div>
    </div>
</div>

@pushOnce('styles')
<style>
    :root { --hall-bg: #f8fafc; }
    .dark { --hall-bg: #111827; }
    .hall-table { cursor: pointer; transition: transform 0.2s ease, z-index 0s; }
    .hall-table--occupied { cursor: not-allowed; }
    .hall-table--available:hover { transform: scale(1.08); z-index: 25 !important; }
    .hall-table--available:hover > div { box-shadow: 0 8px 25px -5px rgba(16, 185, 129, 0.4); }
    .hall-table--selected { transform: scale(1.06); animation: table-pulse 2s ease-in-out infinite; }
    @keyframes table-pulse { 0%, 100% { filter: brightness(1); } 50% { filter: brightness(1.1); } }
    .hall-table--occupied > div { filter: grayscale(0.6); }
</style>
@endPushOnce