<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Zone;
use App\Models\Place;
use Illuminate\Http\Request;

class ZoneController extends Controller
{
    public function index()
    {
        $zones = Zone::with('place')->paginate(15);
        return view('admin.zones.index', compact('zones'));
    }

    public function create()
    {
        $places = Place::pluck('name', 'id');
        return view('admin.zones.create', compact('places'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'place_id'   => ['required','exists:places,id'],
            'name'       => ['required','string','max:255'],
            'price_coef' => ['required','numeric','between:0,9999.999'],
        ]);
        Zone::create($data);
        return redirect()->route('admin.zones.index')->with('success','Zone создана');
    }

    public function edit(Zone $zone)
    {
        $places = Place::pluck('name','id');
        return view('admin.zones.edit', compact('zone','places'));
    }

    public function update(Request $request, Zone $zone)
    {
        $data = $request->validate([
            'place_id'   => ['required','exists:places,id'],
            'name'       => ['required','string','max:255'],
            'price_coef' => ['required','numeric','between:0,9999.999'],
        ]);
        $zone->update($data);
        return redirect()->route('admin.zones.index')->with('success','Zone обновлена');
    }

    public function destroy(Zone $zone)
    {
        $zone->delete();
        return back()->with('success','Zone удалена');
    }
}
