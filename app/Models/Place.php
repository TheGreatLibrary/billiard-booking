<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Place extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];

    public function zones(): HasMany { return $this->hasMany(Zone::class); }
    public function priceRules(): HasMany { return $this->hasMany(PriceRule::class); }
    public function bookings(): HasMany { return $this->hasMany(Booking::class); }
    public function orders(): HasMany { return $this->hasMany(Order::class); }
}