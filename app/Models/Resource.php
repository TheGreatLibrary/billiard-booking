<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Resource extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'model_id',
        'place_id',
        'zone_id',
        'code',
        'state_id',
        'note',
        'grid_x',
        'grid_y',
        'grid_width',
        'grid_height',
        'rotation',
    ];

    protected $casts = [
        'grid_x' => 'integer',
        'grid_y' => 'integer',
        'grid_width' => 'integer',
        'grid_height' => 'integer',
        'rotation' => 'integer',
    ];

    public function model(): BelongsTo
    {
        return $this->belongsTo(ProductModel::class, 'model_id');
    }

    public function place(): BelongsTo
    {
        return $this->belongsTo(Place::class);
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(StateProduct::class, 'state_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function hasGridPosition(): bool
    {
        return !is_null($this->grid_x) && !is_null($this->grid_y);
    }

    public function getFullName(): string
    {
        return $this->code 
            ? "{$this->code} - {$this->model->name}" 
            : $this->model->name;
    }
}