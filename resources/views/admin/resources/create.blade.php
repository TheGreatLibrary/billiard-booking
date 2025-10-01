@extends('admin.layout.app')

@section('content')
<h1 class="text-2xl font-bold mb-4">Добавить ресурс</h1>

<form method="POST" action="{{ route('admin.resources.store') }}" class="space-y-4">
    @csrf
    <div>
        <label class="block">Модель</label>
        <select name="model_id" class="w-full border p-2">
            @foreach($models as $model)
                <option value="{{ $model->id }}">{{ $model->name }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block">Зона</label>
        <select name="zone_id" class="w-full border p-2">
            @foreach($zones as $zone)
                <option value="{{ $zone->id }}">{{ $zone->name }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block">Состояние</label>
        <select name="state_id" class="w-full border p-2">
            @foreach($states as $state)
                <option value="{{ $state->id }}">{{ $state->name }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block">Код</label>
        <input type="text" name="code" class="w-full border p-2">
    </div>

    <div>
        <label class="block">Примечание</label>
        <textarea name="note" class="w-full border p-2"></textarea>
    </div>

    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">Сохранить</button>
</form>
@endsection
