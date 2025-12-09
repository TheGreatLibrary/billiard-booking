@props([
    'placeData' => [],
    'resourceId' => null,
    'wireSelectResource' => 'selectResource',
])

@php
    $gridWidth = $placeData['place']['grid_width'] ?? 20;
    $gridHeight = $placeData['place']['grid_height'] ?? 10;
    $zones = $placeData['zones'] ?? [];
    $resources = $placeData['resources'] ?? [];
@endphp

<div class="border-2 border-gray-300 dark:border-gray-700 rounded-xl overflow-auto bg-gray-50 dark:bg-gray-800 mb-8 shadow-inner"
     style="max-height: calc(100vh - 400px);">
    <div class="inline-block min-w-full p-6">
        <div class="relative grid gap-0 bg-white dark:bg-gray-900 rounded-lg p-4 shadow-sm" 
             style="grid-template-columns: repeat({{ $gridWidth }}, 1fr); 
                    grid-template-rows: repeat({{ $gridHeight }}, 1fr);
                    min-width: {{ $gridWidth * 50 }}px;
                    min-height: {{ $gridHeight * 50 }}px;">
            
            <!-- Сетка с зонами -->
            @for($y = 0; $y < $gridHeight; $y++)
                @for($x = 0; $x < $gridWidth; $x++)
                    @php
                        $cellZone = null;
                        foreach($zones as $zone) {
                            $coordinates = is_string($zone['coordinates']) 
                                ? json_decode($zone['coordinates'], true) 
                                : ($zone['coordinates'] ?? []);
                            
                            if(is_array($coordinates) && in_array(['x' => $x, 'y' => $y], $coordinates)) {
                                $cellZone = $zone;
                                break;
                            }
                        }
                    @endphp
                    
                    <div class="aspect-square border border-gray-200 dark:border-gray-700 relative transition-colors"
                         style="background-color: {{ $cellZone ? ($cellZone['color'] ?? '#3B82F6') : 'transparent' }};
                                opacity: {{ $cellZone ? '0.15' : '1' }};
                                min-width: 50px;
                                min-height: 50px;">
                        @if($cellZone)
                            <div class="absolute inset-0 border-2 border-dashed" 
                                 style="border-color: {{ $cellZone['color'] ?? '#3B82F6' }}; opacity: 0.3;"></div>
                        @endif
                    </div>
                @endfor
            @endfor

            <!-- Столы -->
            @foreach($resources as $resource)
                <x-booking.table-item 
                    :resource="$resource"
                    :resourceId="$resourceId"
                    :wireSelectResource="$wireSelectResource"
                />
            @endforeach
        </div>
    </div>
</div>