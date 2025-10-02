@extends('admin.layout.app')

@section('content')
    <h1 class="mb-4 text-xl font-bold">Модели продуктов</h1>

    <a href="{{ route('admin.product-models.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded">Добавить модель</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table-auto w-full mt-4 border">
        <thead>
        <tr class="bg-gray-100">
            <th class="px-2 py-1">ID</th>
          <th class="px-2 py-1">Тип продукта</th>
           <th class="px-2 py-1">Название</th>
            <th class="px-2 py-1">Цена/час (коп.)</th>
            <th class="px-2 py-1">Цена/шт (коп.)</th>
            <th class="px-2 py-1">Действия</th>
        </tr>
        </thead>
        <tbody>
        @foreach($models as $model)
            <tr>
                 <td class="border px-2 py-1">{{ $model->id }}</td>
                 <td class="border px-2 py-1">{{ $model->type->name }}</td>
                 <td class="border px-2 py-1">{{ $model->name }}</td>
                 <td class="border px-2 py-1">{{ $model->base_price_hour ?? '—' }}</td>
                 <td class="border px-2 py-1">{{ $model->base_price_each ?? '—' }}</td>
                <td class="border px-2 py-1 text-right">
                    <a href="{{ route('admin.product-models.edit', $model) }}" class="text-blue-600">✏️</a>
                    <form action="{{ route('admin.product-models.destroy', $model) }}" method="POST" class="d-inline"
                          onsubmit="return confirm('Удалить?')">
                        @csrf @method('DELETE')
                       <button class="text-red-600" >Удалить</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

<div class="mt-4">
    {{ $models->links() }}
</div>
@endsection
