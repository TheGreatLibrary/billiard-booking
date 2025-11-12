<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Place extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'address',
        'description',
        'grid_width',
        'grid_height',
        'hall_image',
    ];

    protected $casts = [
        'grid_width' => 'integer',
        'grid_height' => 'integer',
    ];

    public function zones(): HasMany
    {
        return $this->hasMany(Zone::class);
    }

    public function resources(): HasMany
    {
        return $this->hasMany(Resource::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function getHallSizeInPixels(int $cellSize = 60): array
    {
        return [
            'width' => $this->grid_width * $cellSize,
            'height' => $this->grid_height * $cellSize,
        ];
    }

    public function getVisualResources()
    {
        return $this->resources()
            ->whereNotNull('grid_x')
            ->whereNotNull('grid_y')
            ->where('state_id', 1) // только доступные
            ->with(['model', 'zone'])
            ->get();
    }
}