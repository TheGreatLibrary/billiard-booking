<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use Illuminate\Http\Request;

class HallController extends Controller
{
    // Просмотр карты
    public function index()
    {
 $resources = Resource::whereHas('model.type', function ($q) {
        $q->where('name', 'table');
    })->get();

    return view('admin.hall.index', compact('resources'));
    }

    // Обновление позиций через AJAX
public function updatePositions(Request $request)
{
    $data = $request->validate([
        'positions' => 'required|array',
        'positions.*.id' => 'required|integer|exists:resources,id',
        'positions.*.x' => 'required|numeric',
        'positions.*.y' => 'required|numeric',
        'positions.*.width' => 'required|numeric',
        'positions.*.height' => 'required|numeric',
        'positions.*.rotation' => 'required|numeric',
        'positions.*.shape' => 'nullable|string',
        'positions.*.image_url' => 'nullable|url',
    ]);

    foreach ($data['positions'] as $pos) {
        $update = [
            'x' => $pos['x'],
            'y' => $pos['y'],
            'width' => $pos['width'],
            'height' => $pos['height'],
            'rotation' => $pos['rotation'],
        ];
        if (isset($pos['shape'])) $update['shape'] = $pos['shape'];
        if (isset($pos['image_url'])) $update['image_url'] = $pos['image_url'];

        \App\Models\Resource::where('id', $pos['id'])->update($update);
    }

    return response()->json(['success' => true]);
}
}
