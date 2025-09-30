@props(['color' => 'gray', 'href' => '#', 'title' => '', 'icon' => ''])

<a href="{{ $href }}"
   class="flex items-center justify-between bg-{{ $color }}-100 hover:bg-{{ $color }}-200
          rounded-lg p-4 shadow transition">
    <div>
        <h3 class="text-lg font-semibold text-gray-700">{{ $title }}</h3>
    </div>
    <span class="text-3xl">{{ $icon }}</span>
</a>
