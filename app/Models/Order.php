<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    const UPDATED_AT = null;

    protected $guarded = ['id'];

    protected $casts = [
        'total_amount' => 'integer',
        'paid_at' => 'datetime',
    ];

    // Проверки
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function canEdit(): bool
    {
        return $this->status === 'pending';
    }

    public function canPay(): bool
    {
        return $this->status === 'pending';
    }

    // Связи
    public function booking(): BelongsTo { return $this->belongsTo(Booking::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function place(): BelongsTo { return $this->belongsTo(Place::class); }
    public function items(): HasMany { return $this->hasMany(OrderItem::class); }
}