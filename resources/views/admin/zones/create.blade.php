@extends('admin.layout.app')

@section('content')
<h1 class="mb-4 text-xl font-bold">Добавить зону</h1>
<form action="{{ route('admin.zones.store') }}" method="POST" class="space-y-4">
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
        <label class="block">Название</label>
        <input type="text" name="name" class="border p-1 w-full" required>
    </div>
    <div>
        <label class="block">Коэффициент цены</label>
        <input type="number" step="0.001" name="price_coef" value="1.0" class="border p-1 w-full">
    </div>
    <button class="px-4 py-2 bg-green-600 text-white rounded">Сохранить</button>
</form>
@endsection
