@extends('admin.layout.app')

@section('title', 'Редактор зала')

@section('content')
<h1 class="text-2xl font-bold mb-6">Редактор зала</h1>

<div class="flex gap-6">
    <!-- Левая панель -->
    <div class="w-1/4 bg-white shadow rounded p-4">
        <h2 class="text-lg font-semibold mb-3">Доступные столы</h2>
        <div id="resource-list" class="space-y-2">
            @forelse ($resources as $resource)
                <div class="flex justify-between items-center border-b py-2">
                    <span>{{ $resource->model->name }} ({{ $resource->code ?? 'Без кода' }})</span>
                    <button class="add-to-map px-2 py-1 bg-blue-500 text-white text-sm rounded"
                        data-id="{{ $resource->id }}">
                        ➕
                    </button>
                </div>
            @empty
                <p class="text-gray-500">Нет ресурсов</p>
            @endforelse
        </div>
    </div>

    <!-- Карта -->
    <div class="flex-1 relative bg-gray-100 rounded shadow" id="hall-map" style="height: 600px;">
        <p class="text-gray-400 absolute inset-0 flex items-center justify-center">
            Перетащи сюда стол
        </p>
    </div>
</div>

<script src="{{ asset('js/map-editor.js') }}"></script>
@endsection
