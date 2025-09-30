@extends('admin.layout.app')

@section('content')
<h1 class="mb-4 text-xl font-bold">Добавить ценовое правило</h1>

<form action="{{ route('admin.price-rules.store') }}" method="POST" class="space-y-4">
    @csrf
    <div>
        <label class="block">Место</label>
        <select name="place_id" class="border p-1 w-full">
            @foreach($places as $id=>$name)
                <option value="{{ $id }}">{{ $name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block">Зона (опц.)</label>
        <select name="zone_id" class="border p-1 w-full">
            <option value="">Все</option>
            @foreach($zones as $id=>$name)
                <option value="{{ $id }}">{{ $name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block">День недели (0=Вс ... 6=Сб, опц.)</label>
        <input type="number" name="dow" min="0" max="6" class="border p-1 w-full">
    </div>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block">Время с</label>
            <input type="time" name="time_from" class="border p-1 w-full">
        </div>
        <div>
            <label class="block">Время до</label>
            <input type="time" name="time_to" class="border p-1 w-full">
        </div>
    </div>
    <div>
        <label class="block">Тип</label>
        <select name="kind" class="border p-1 w-full">
            <option value="coef">Множитель</option>
            <option value="override">Фикс. цена</option>
        </select>
    </div>
    <div>
        <label class="block">Значение</label>
        <input type="number" step="0.001" name="value" class="border p-1 w-full" required>
    </div>
    <div>
        <label><input type="checkbox" name="active" value="1" checked> Активно</label>
    </div>
    <button class="px-4 py-2 bg-green-600 text-white rounded">Сохранить</button>
</form>
@endsection
