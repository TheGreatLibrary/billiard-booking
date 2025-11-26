<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\PriceRule;
use App\Models\Place;
use App\Models\Zone;
use App\Services\PriceRuleService;

class PriceRuleForm extends Component
{
    public $ruleId;
    public $place_id;
    public $zone_id;
    public $dow;
    public $time_from;
    public $time_to;
    public $kind;
    public $value;
    public $active = true;
    
    public $places;
    public $zones = [];

    private PriceRuleService $service;

    protected $rules = [
        'place_id'  => 'required|exists:places,id',
        'zone_id'   => 'nullable|exists:zones,id',
        'dow'       => 'nullable|integer|between:0,6',
        'time_from' => 'nullable|date_format:H:i',
        'time_to'   => 'nullable|date_format:H:i',
        'kind'      => 'required|in:coef,override',
        'value'     => 'required|numeric',
        'active'    => 'boolean',
    ];

    public function boot(PriceRuleService $service)
    {
        $this->service = $service;
    }

    public function mount($rule = null)
    {
        $this->places = Place::all();

        if ($rule) {
            $rule = PriceRule::findOrFail($rule);
            $this->ruleId = $rule->id;
            $this->place_id = $rule->place_id;
            $this->zone_id = $rule->zone_id;
            $this->dow = $rule->dow;
            $this->time_from = $rule->time_from;
            $this->time_to = $rule->time_to;
            $this->kind = $rule->kind;
            $this->value = $rule->value;
            $this->active = $rule->active;
            
            $this->loadZones();
        }
    }

    public function updatedPlaceId()
    {
        $this->loadZones();
        $this->zone_id = null;
    }

    private function loadZones()
    {
        $this->zones = $this->place_id 
            ? Zone::where('place_id', $this->place_id)->get() 
            : [];
    }

    public function save()
    {
        $this->validate();

        $data = $this->service->prepareDataForSave([
            'place_id'  => $this->place_id,
            'zone_id'   => $this->zone_id,
            'dow'       => $this->dow,
            'time_from' => $this->time_from,
            'time_to'   => $this->time_to,
            'kind'      => $this->kind,
            'value'     => $this->value,
            'active'    => $this->active,
        ]);

        if ($this->ruleId) {
            $rule = PriceRule::findOrFail($this->ruleId);
            $this->service->update($rule, $data);
            session()->flash('success', 'Правило обновлено.');
        } else {
            $this->service->create($data);
            session()->flash('success', 'Правило создано.');
        }

        return redirect()->route('admin.price-rules.index');
    }

    public function render()
    {
        return view('livewire.admin.price-rule-form')
            ->layout('admin.layout.app-livewire', [
                'title' => $this->ruleId ? 'Редактирование правила' : 'Создание правила'
            ]);
    }
}