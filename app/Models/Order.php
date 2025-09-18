<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    const UPDATED_AT = null;
    const CREATED_AT = 'created_at';

    protected $guarded = ['id'];

    protected $casts = [
        'total_amount' => 'integer',
    ];

    public function booking(): BelongsTo { return $this->belongsTo(Booking::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function place(): BelongsTo { return $this->belongsTo(Place::class); }
    public function items(): HasMany { return $this->hasMany(OrderItem::class); }
    public function payments(): HasMany { return $this->hasMany(Payment::class); }
}