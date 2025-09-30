@extends('admin.layout.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Локации (Places)</h1>

    <a href="{{ route('admin.places.create') }}" class="btn btn-primary mb-3">Добавить локацию</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
        <tr>
            <th>ID</th>
            <th>Название</th>
            <th>Адрес</th>
            <th>Описание</th>
            <th width="180">Действия</th>
        </tr>
        </thead>
        <tbody>
        @foreach($places as $place)
            <tr>
                <td>{{ $place->id }}</td>
                <td>{{ $place->name }}</td>
                <td>{{ $place->address }}</td>
                <td>{{ Str::limit($place->description, 50) }}</td>
                <td>
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

    {{ $places->links() }}
</div>
@endsection
