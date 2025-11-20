<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Zone extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'place_id',
        'name',
        'price_coef',
        'coordinates',
        'color',
    ];

    protected $casts = [
        'price_coef' => 'decimal:3',
        'coordinates' => 'array', // JSON массив координат [{x: 0, y: 0}, ...]
    ];

    public function place(): BelongsTo
    {
        return $this->belongsTo(Place::class);
    }

    public function resources(): HasMany
    {
        return $this->hasMany(Resource::class);
    }

    public function priceRules(): HasMany
    {
        return $this->hasMany(PriceRule::class);
    }

    public function getPriceMultiplier(): float
    {
        return (float) $this->price_coef;
    }

    public function hasCoordinates(): bool
    {
        return !empty($this->coordinates) && is_array($this->coordinates);
    }
}