@extends('admin.layout.app')

@section('content')
<div class="container">
    <h1>Добавить локацию</h1>

    <form method="POST" action="{{ route('admin.places.store') }}">
        @csrf

        <div class="mb-3">
            <label class="form-label">Название</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            @error('name')<div class="text-danger">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Адрес</label>
            <input type="text" name="address" class="form-control" value="{{ old('address') }}" required>
            @error('address')<div class="text-danger">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Описание</label>
            <textarea name="description" class="form-control" rows="4">{{ old('description') }}</textarea>
            @error('description')<div class="text-danger">{{ $message }}</div>@enderror
        </div>

        <button class="btn btn-success">Сохранить</button>
        <a href="{{ route('admin.places.index') }}" class="btn btn-secondary">Отмена</a>
    </form>
</div>
@endsection
