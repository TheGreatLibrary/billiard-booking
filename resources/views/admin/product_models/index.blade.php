@extends('admin.layout.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Модели продуктов</h1>

    <a href="{{ route('admin.product-models.create') }}" class="btn btn-primary mb-3">Добавить модель</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
        <tr>
            <th>ID</th>
            <th>Тип продукта</th>
            <th>Название</th>
            <th>Цена/час (коп.)</th>
            <th>Цена/шт (коп.)</th>
            <th width="180">Действия</th>
        </tr>
        </thead>
        <tbody>
        @foreach($models as $model)
            <tr>
                <td>{{ $model->id }}</td>
                <td>{{ $model->type->name }}</td>
                <td>{{ $model->name }}</td>
                <td>{{ $model->base_price_hour ?? '—' }}</td>
                <td>{{ $model->base_price_each ?? '—' }}</td>
                <td>
                    <a href="{{ route('admin.product-models.edit', $model) }}" class="btn btn-sm btn-warning">Редактировать</a>
                    <form action="{{ route('admin.product-models.destroy', $model) }}" method="POST" class="d-inline"
                          onsubmit="return confirm('Удалить?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger">Удалить</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    {{ $models->links() }}
</div>
@endsection
