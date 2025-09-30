@extends('admin.layout.app')

@section('content')
    <h1 class="mb-4 text-xl font-bold">Зоны</h1>
    <a href="{{ route('admin.zones.create') }}"
       class="px-4 py-2 bg-blue-600 text-white rounded">Добавить</a>

    <table class="table-auto w-full mt-4 border">
        <thead>
            <tr class="bg-gray-100">
                <th class="px-2 py-1">ID</th>
                <th class="px-2 py-1">Название</th>
                <th class="px-2 py-1">Место</th>
                <th class="px-2 py-1">Коэф.</th>
                <th class="px-2 py-1"></th>
            </tr>
        </thead>
        <tbody>
        @foreach($zones as $zone)
            <tr>
                <td class="border px-2 py-1">{{ $zone->id }}</td>
                <td class="border px-2 py-1">{{ $zone->name }}</td>
                <td class="border px-2 py-1">{{ $zone->place->name ?? '' }}</td>
                <td class="border px-2 py-1">{{ $zone->price_coef }}</td>
                <td class="border px-2 py-1 text-right">
                    <a href="{{ route('admin.zones.edit',$zone) }}" class="text-blue-600">✏️</a>
                    <form action="{{ route('admin.zones.destroy',$zone) }}" method="POST" class="inline">
                        @csrf @method('DELETE')
                        <button class="text-red-600" onclick="return confirm('Удалить?')">🗑</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="mt-4">
        {{ $zones->links() }}
    </div>
@endsection
