<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Place;
use Illuminate\Http\Request;

class PlaceController extends Controller
{
    public function index()
    {
        $places = Place::orderBy('name')->paginate(15);
        return view('admin.places.index', compact('places'));
    }

    public function create()
    {
        return view('admin.places.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Place::create($validated);

        return redirect()->route('admin.places.index')
            ->with('success', 'Локация успешно создана.');
    }

    public function edit(Place $place)
    {
        return view('admin.places.edit', compact('place'));
    }

    public function update(Request $request, Place $place)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $place->update($validated);

        return redirect()->route('admin.places.index')
            ->with('success', 'Локация обновлена.');
    }

    public function destroy(Place $place)
    {
        $place->delete();

        return redirect()->route('admin.places.index')
            ->with('success', 'Локация удалена.');
    }
}
