@extends('admin.layout.app')

@section('content')
<div class="flex justify-between items-center mb-4">
    <h1 class="text-2xl font-bold">Ресурсы</h1>
    <a href="{{ route('admin.resources.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded">Добавить</a>
</div>

@if(session('success'))
    <div class="bg-green-100 text-green-800 p-2 mb-4">{{ session('success') }}</div>
@endif

<table class="w-full border">
    <thead>
        <tr class="bg-gray-200">
            <th class="p-2 border">ID</th>
            <th class="p-2 border">Код</th>
            <th class="p-2 border">Модель</th>
            <th class="p-2 border">Зона</th>
            <th class="p-2 border">Состояние</th>
            <th class="p-2 border">Примечание</th>
            <th class="p-2 border">Действия</th>
        </tr>
    </thead>
    <tbody>
        @foreach($resources as $resource)
            <tr>
                <td class="p-2 border">{{ $resource->id }}</td>
                <td class="p-2 border">{{ $resource->code }}</td>
                <td class="p-2 border">{{ $resource->model->name ?? '' }}</td>
                <td class="p-2 border">{{ $resource->zone->name ?? '' }}</td>
                <td class="p-2 border">{{ $resource->state->name ?? '' }}</td>
                <td class="p-2 border">{{ $resource->note }}</td>
                <td class="p-2 border">
                    <a href="{{ route('admin.resources.edit', $resource) }}" class="text-blue-600">Редактировать</a>
                    <form action="{{ route('admin.resources.destroy', $resource) }}" method="POST" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-600 ml-2" onclick="return confirm('Удалить ресурс?')">Удалить</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="mt-4">
    {{ $resources->links() }}
</div>
@endsection
