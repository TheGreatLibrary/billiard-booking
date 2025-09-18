<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    // у нас есть только created_at
    const UPDATED_AT = null;
    const CREATED_AT = 'created_at';

    protected $guarded = ['id'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function place(): BelongsTo { return $this->belongsTo(Place::class); }
    public function bookingResources(): HasMany { return $this->hasMany(BookingResource::class); }
    public function order(): HasOne { return $this->hasOne(Order::class); }
}