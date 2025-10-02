@extends('admin.layout.app')

@section('content')
    <h1 class="mb-4">Типы продуктов</h1>

    <a href="{{ route('admin.product-types.create') }}"  class="px-4 py-2 bg-blue-600 text-white rounded">Добавить</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table-auto w-full mt-4 border">
        <thead>
            <tr class='bg-gray-100'>
                <th class="px-2 py-1">ID</th>
                <th class="px-2 py-1">Название</th>
               <th class="px-2 py-1">Действия</th>
            </tr>
        </thead>
        <tbody>
            @foreach($types as $type)
            <tr>
                 <td class="border px-2 py-1">{{ $type->id }}</td>
                  <td class="border px-2 py-1">{{ $type->name }}</td>
                <td class="border px-2 py-1 text-right">
                    <a href="{{ route('admin.product-types.edit', $type) }}" class="text-blue-600">Редактировать</a>
                    <form action="{{ route('admin.product-types.destroy', $type) }}" method="POST" class="d-inline"
                          onsubmit="return confirm('Удалить?')">
                        @csrf @method('DELETE')
                         <button class="text-red-600">Удалить</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

<div class="mt-4">
    {{ $types->links() }}
</div>
@endsection
