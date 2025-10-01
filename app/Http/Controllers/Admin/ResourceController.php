<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use App\Models\ProductModel;
use App\Models\Zone;
use App\Models\StateProduct;
use Illuminate\Http\Request;

class ResourceController extends Controller
{
    public function index()
    {
        $resources = Resource::with(['model', 'zone', 'state'])->paginate(20);
        return view('admin.resources.index', compact('resources'));
    }

    public function create()
    {
        $models = ProductModel::all();
        $zones = Zone::all();
        $states = StateProduct::all();

        return view('admin.resources.create', compact('models', 'zones', 'states'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'model_id' => 'required|exists:product_models,id',
            'zone_id' => 'required|exists:zones,id',
            'state_id' => 'required|exists:state_product,id',
            'code'     => 'nullable|string|max:50',
            'note'     => 'nullable|string',
        ]);

        Resource::create($data);

        return redirect()->route('admin.resources.index')->with('success', 'Ресурс добавлен');
    }

    public function edit(Resource $resource)
    {
        $models = ProductModel::all();
        $zones = Zone::all();
        $states = StateProduct::all();

        return view('admin.resources.edit', compact('resource', 'models', 'zones', 'states'));
    }

    public function update(Request $request, Resource $resource)
    {
        $data = $request->validate([
            'model_id' => 'required|exists:product_models,id',
            'zone_id' => 'required|exists:zones,id',
            'state_id' => 'required|exists:state_product,id',
            'code'     => 'nullable|string|max:50',
            'note'     => 'nullable|string',
        ]);

        $resource->update($data);

        return redirect()->route('admin.resources.index')->with('success', 'Ресурс обновлён');
    }

    public function destroy(Resource $resource)
    {
        $resource->delete();
        return redirect()->route('admin.resources.index')->with('success', 'Ресурс удалён');
    }
}
