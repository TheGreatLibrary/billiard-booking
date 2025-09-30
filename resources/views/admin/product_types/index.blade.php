@extends('admin.layout.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Типы продуктов</h1>

    <a href="{{ route('admin.product-types.create') }}" class="btn btn-primary mb-3">Добавить</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Название</th>
                <th width="180">Действия</th>
            </tr>
        </thead>
        <tbody>
            @foreach($types as $type)
            <tr>
                <td>{{ $type->id }}</td>
                <td>{{ $type->name }}</td>
                <td>
                    <a href="{{ route('admin.product-types.edit', $type) }}" class="btn btn-sm btn-warning">Редактировать</a>
                    <form action="{{ route('admin.product-types.destroy', $type) }}" method="POST" class="d-inline"
                          onsubmit="return confirm('Удалить?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger">Удалить</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $types->links() }}
</div>
@endsection
