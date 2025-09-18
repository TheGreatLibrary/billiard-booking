<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingResource extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];

    protected $casts = [
        'hour_price_snapshot' => 'integer',
        'rule_value' => 'decimal:3',
        'zone_coef_snapshot' => 'decimal:3',
        'minutes' => 'integer',
        'amount' => 'integer',
    ];

    public function booking(): BelongsTo { return $this->belongsTo(Booking::class); }
    public function resource(): BelongsTo { return $this->belongsTo(Resource::class); }
}