@props([
    'places' => [],
    'wireSelectPlace' => 'selectPlace',
])

<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl dark:shadow-gray-900/40 p-6 sm:p-8 border border-gray-200 dark:border-gray-700">
    <!-- Заголовок -->
    <x-booking.step-header 
        title="Выберите заведение"
        subtitle="Выберите подходящее заведение для бронирования"
        step="1"
    />

    <!-- Карточки мест -->
    @if(count($places) > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-8">
            @foreach($places as $place)
                <x-booking.place-card 
                    :place="$place"
                    :wireSelectPlace="$wireSelectPlace"
                />
            @endforeach
        </div>
    @else
        <x-shared.empty-state 
            icon="location-marker"
            title="Заведения не найдены"
            description="На данный момент нет доступных заведений для бронирования"
        />
    @endif
</div>