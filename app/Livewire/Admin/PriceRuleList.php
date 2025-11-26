<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\PriceRule;
use App\Models\Place;
use App\Models\Zone;
use App\Services\PriceRuleService;

class PriceRuleList extends Component
{
    use WithPagination;

    public $search = '';
    public $place_id = '';
    public $kind = '';
    public $active = '';
    public $places = [];

    protected $queryString = ['search', 'place_id', 'kind', 'active'];

    public function boot(PriceRuleService $service)
    {
        $this->service = $service;
    }

    public function updating($field)
    {
        if (in_array($field, ['search', 'place_id', 'kind', 'active'])) {
            $this->resetPage();
        }
    }

    public function mount()
    {
        $this->places = Place::all();
    }

    public function delete($id)
    {
        $rule = PriceRule::findOrFail($id);
        $this->service->delete($rule);
        session()->flash('success', 'Правило удалено.');
    }

    public function render()
    {
        $rules = PriceRule::with(['place', 'zone'])
            ->when($this->place_id, fn($q) => $q->where('place_id', $this->place_id))
            ->when($this->kind, fn($q) => $q->where('kind', $this->kind))
            ->when($this->active !== '', fn($q) => $q->where('active', $this->active))
            ->when($this->search, function($q) {
                $q->whereHas('place', fn($pq) => $pq->where('name', 'like', "%{$this->search}%"))
                  ->orWhereHas('zone', fn($zq) => $zq->where('name', 'like', "%{$this->search}%"));
            })
            ->latest()
            ->paginate(15);

        return view('livewire.admin.price-rule-list', compact('rules'))
            ->layout('admin.layout.app-livewire', ['title' => 'Правила цен']);
    }
}