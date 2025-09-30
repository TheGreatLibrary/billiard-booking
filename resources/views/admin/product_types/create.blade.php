@extends('admin.layout.app')

@section('content')
<div class="container">
    <h1>Добавить тип продукта</h1>
    <form method="POST" action="{{ route('admin.product-types.store') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label">Название</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            @error('name')<div class="text-danger">{{ $message }}</div>@enderror
        </div>
        <button class="btn btn-success">Сохранить</button>
        <a href="{{ route('admin.product-types.index') }}" class="btn btn-secondary">Отмена</a>
    </form>
</div>
@endsection
