@extends('admin.layout.app')

@section('content')
<h1 class="mb-4 text-xl font-bold">Ценовые правила</h1>

<a href="{{ route('admin.price-rules.create') }}"
   class="px-4 py-2 bg-blue-600 text-white rounded">Добавить</a>

<table class="table-auto w-full mt-4 border">
    <thead>
        <tr class="bg-gray-100">
            <th class="px-2 py-1">ID</th>
            <th class="px-2 py-1">Место</th>
            <th class="px-2 py-1">Зона</th>
            <th class="px-2 py-1">День недели</th>
            <th class="px-2 py-1">Время</th>
            <th class="px-2 py-1">Тип</th>
            <th class="px-2 py-1">Значение</th>
            <th class="px-2 py-1">Активно</th>
            <th class="px-2 py-1"></th>
        </tr>
    </thead>
    <tbody>
    @foreach($rules as $rule)
        <tr>
            <td class="border px-2 py-1">{{ $rule->id }}</td>
            <td class="border px-2 py-1">{{ $rule->place->name ?? '' }}</td>
            <td class="border px-2 py-1">{{ $rule->zone->name ?? 'Все' }}</td>
            <td class="border px-2 py-1">{{ $rule->dow ?? 'Все' }}</td>
            <td class="border px-2 py-1">
                {{ $rule->time_from ?? '—' }} - {{ $rule->time_to ?? '—' }}
            </td>
            <td class="border px-2 py-1">{{ $rule->kind }}</td>
            <td class="border px-2 py-1">{{ $rule->value }}</td>
            <td class="border px-2 py-1">{{ $rule->active ? '✅' : '❌' }}</td>
            <td class="border px-2 py-1 text-right">
                <a href="{{ route('admin.price-rules.edit',$rule) }}" class="text-blue-600">✏️</a>
                <form action="{{ route('admin.price-rules.destroy',$rule) }}" method="POST" class="inline">
                    @csrf @method('DELETE')
                    <button class="text-red-600" onclick="return confirm('Удалить?')">🗑</button>
                </form>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

<div class="mt-4">
    {{ $rules->links() }}
</div>
@endsection
