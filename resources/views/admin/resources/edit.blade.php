@extends('admin.layout.app')

@section('content')
<h1 class="text-2xl font-bold mb-4">Редактировать ресурс</h1>

<form method="POST" action="{{ route('admin.resources.update', $resource) }}" class="space-y-4">
    @csrf
    @method('PUT')

    <div>
        <label class="block mb-1">Модель</label>
        <select name="model_id" class="w-full border p-2">
            @foreach($models as $model)
                <option value="{{ $model->id }}" 
                    {{ old('model_id', $resource->model_id) == $model->id ? 'selected' : '' }}>
                    {{ $model->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block mb-1">Зона</label>
        <select name="zone_id" class="w-full border p-2">
            @foreach($zones as $zone)
                <option value="{{ $zone->id }}" 
                    {{ old('zone_id', $resource->zone_id) == $zone->id ? 'selected' : '' }}>
                    {{ $zone->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block mb-1">Состояние</label>
        <select name="state_id" class="w-full border p-2">
            @foreach($states as $state)
                <option value="{{ $state->id }}" 
                    {{ old('state_id', $resource->state_id) == $state->id ? 'selected' : '' }}>
                    {{ $state->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block mb-1">Код</label>
        <input type="text" name="code" class="w-full border p-2"
               value="{{ old('code', $resource->code) }}">
    </div>

    <div>
        <label class="block mb-1">Примечание</label>
        <textarea name="note" class="w-full border p-2">{{ old('note', $resource->note) }}</textarea>
    </div>

    <div class="flex gap-2">
        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">Сохранить</button>
        <a href="{{ route('admin.resources.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded">Назад</a>
    </div>
</form>
@endsection
