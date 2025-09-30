<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PriceRule;
use App\Models\Place;
use App\Models\Zone;
use Illuminate\Http\Request;

class PriceRuleController extends Controller
{
    public function index()
    {
        $rules = PriceRule::with(['place','zone'])->paginate(15);
        return view('admin.price-rules.index', compact('rules'));
    }

    public function create()
    {
        $places = Place::pluck('name','id');
        $zones  = Zone::pluck('name','id');
        return view('admin.price-rules.create', compact('places','zones'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'place_id'  => ['required','exists:places,id'],
            'zone_id'   => ['nullable','exists:zones,id'],
            'dow'       => ['nullable','integer','between:0,6'],
            'time_from' => ['nullable','date_format:H:i'],
            'time_to'   => ['nullable','date_format:H:i'],
            'kind'      => ['required','in:coef,override'],
            'value'     => ['required','numeric'],
            'active'    => ['boolean'],
        ]);
        $data['active'] = $request->boolean('active');
        PriceRule::create($data);
        return redirect()->route('admin.price-rules.index')->with('success','Правило создано');
    }

    public function edit(PriceRule $price_rule)
    {
        $places = Place::pluck('name','id');
        $zones  = Zone::pluck('name','id');
        return view('admin.price-rules.edit', [
            'rule'   => $price_rule,
            'places' => $places,
            'zones'  => $zones
        ]);
    }

    public function update(Request $request, PriceRule $price_rule)
    {
        $data = $request->validate([
            'place_id'  => ['required','exists:places,id'],
            'zone_id'   => ['nullable','exists:zones,id'],
            'dow'       => ['nullable','integer','between:0,6'],
            'time_from' => ['nullable','date_format:H:i'],
            'time_to'   => ['nullable','date_format:H:i'],
            'kind'      => ['required','in:coef,override'],
            'value'     => ['required','numeric'],
            'active'    => ['boolean'],
        ]);
        $data['active'] = $request->boolean('active');
        $price_rule->update($data);
        return redirect()->route('admin.price-rules.index')->with('success','Правило обновлено');
    }

    public function destroy(PriceRule $price_rule)
    {
        $price_rule->delete();
        return back()->with('success','Правило удалено');
    }
}
