<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use Illuminate\Http\Request;

class HallController extends Controller
{
    public function index()
    {
        // Загружаем все ресурсы с их моделями
        $resources = Resource::with('model')
            ->orderBy('id')
            ->get();

        return view('admin.hall.index', compact('resources'));
    }

    // AJAX: получение всех ресурсов (для отрисовки)
    public function resources()
    {
        $resources = Resource::with('model')
            ->get(['id', 'code', 'model_id', 'x', 'y', 'width', 'height', 'rotation', 'shape']);

        return response()->json($resources);
    }

    // AJAX: добавление на карту
    public function add($id)
    {
        $resource = Resource::findOrFail($id);

        // Проверяем, не добавлен ли уже
        if ($resource->x !== null && $resource->y !== null) {
            return response()->json(['message' => 'Этот стол уже добавлен на карту'], 400);
        }

        // Устанавливаем дефолтные координаты
        $resource->update([
            'x' => 100,
            'y' => 100,
            'width' => 100,
            'height' => 60,
            'rotation' => 0,
            'shape' => 'rect',
        ]);

        return response()->json($resource);
    }

    // AJAX: обновление позиции
    public function updatePosition(Request $request, $id)
    {
        $resource = Resource::findOrFail($id);
        $resource->update($request->only(['x', 'y', 'width', 'height', 'rotation']));
        return response()->json(['success' => true]);
    }

    // AJAX: удаление с карты
    public function remove($id)
    {
        $resource = Resource::findOrFail($id);
        $resource->update(['x' => null, 'y' => null]); // "удаляем" с карты
        return response()->json(['success' => true]);
    }
}
