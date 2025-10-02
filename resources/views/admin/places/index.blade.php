@extends('admin.layout.app')

@section('content')

 <h1 class="mb-4 text-xl font-bold">Локации (Places)</h1>
    <a href="{{ route('admin.places.create') }}"
       class="px-4 py-2 bg-blue-600 text-white rounded">Добавить</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table-auto w-full mt-4 border">
        <thead>
        <tr class="bg-gray-100">
            <th class="px-2 py-1">ID</th>
            <th class="px-2 py-1">Название</th>
            <th class="px-2 py-1">Адрес</th>
            <th class="px-2 py-1">Описание</th>
            <th class="px-2 py-1">Действия</th>
        </tr>
        </thead>
        <tbody>
        @foreach($places as $place)
            <tr>
                <td class="border px-2 py-1">{{ $place->id }}</td>
                <td class="border px-2 py-1">{{ $place->name }}</td>
                <td class="border px-2 py-1">{{ $place->address }}</td>
                <td class="border px-2 py-1">{{ Str::limit($place->description, 50) }}</td>
                <td  class="border px-2 py-1 text-right">
                    <a href="{{ route('admin.places.edit', $place) }}" class="btn btn-sm btn-warning">Редактировать</a>
                    <form action="{{ route('admin.places.destroy', $place) }}" method="POST" class="d-inline"
                          onsubmit="return confirm('Удалить?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger">Удалить</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

     <div class="mt-4">
        {{ $places->links() }}
    </div>
@endsection
