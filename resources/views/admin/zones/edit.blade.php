@extends('admin.layout.app')

@section('content')
<h1 class="mb-4 text-xl font-bold">Редактировать зону</h1>
<form action="{{ route('admin.zones.update',$zone) }}" method="POST" class="space-y-4">
    @csrf @method('PUT')
    <div>
        <label class="block">Место</label>
        <select name="place_id" class="border p-1 w-full">
            @foreach($places as $id=>$name)
                <option value="{{ $id }}" @selected($zone->place_id==$id)>{{ $name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block">Название</label>
        <input type="text" name="name" value="{{ $zone->name }}" class="border p-1 w-full" required>
    </div>
    <div>
        <label class="block">Коэффициент цены</label>
        <input type="number" step="0.001" name="price_coef" value="{{ $zone->price_coef }}" class="border p-1 w-full">
    </div>
    <button class="px-4 py-2 bg-blue-600 text-white rounded">Обновить</button>
</form>
@endsection
