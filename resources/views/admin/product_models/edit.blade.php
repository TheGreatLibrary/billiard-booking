@extends('admin.layout.app')

@section('content')
<div class="container">
    <h1>Редактировать модель продукта</h1>
    <form method="POST" action="{{ route('admin.product-models.update', $productModel) }}">
        @csrf @method('PUT')

        <div class="mb-3">
            <label class="form-label">Тип продукта</label>
            <select name="product_type_id" class="form-select" required>
                @foreach($types as $type)
                    <option value="{{ $type->id }}" {{ old('product_type_id', $productModel->product_type_id) == $type->id ? 'selected' : '' }}>
                        {{ $type->name }}
                    </option>
                @endforeach
            </select>
            @error('product_type_id')<div class="text-danger">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Название модели</label>
            <input type="text" name="name" class="form-control"
                   value="{{ old('name', $productModel->name) }}" required>
            @error('name')<div class="text-danger">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Базовая цена за час (копейки)</label>
            <input type="number" name="base_price_hour" class="form-control"
                   value="{{ old('base_price_hour', $productModel->base_price_hour) }}">
            @error('base_price_hour')<div class="text-danger">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Базовая цена за единицу (копейки)</label>
            <input type="number" name="base_price_each" class="form-control"
                   value="{{ old('base_price_each', $productModel->base_price_each) }}">
            @error('base_price_each')<div class="text-danger">{{ $message }}</div>@enderror
        </div>

        <button class="btn btn-success">Обновить</button>
        <a href="{{ route('admin.product-models.index') }}" class="btn btn-secondary">Отмена</a>
    </form>
</div>
@endsection
