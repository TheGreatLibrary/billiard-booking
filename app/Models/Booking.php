<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

class Booking extends Model
{
    public $timestamps = false;
    
    protected $fillable = [
        'user_id',
        'place_id',
        'resource_id',
        'status',
        'payment_status',
        'payment_method',
        'paid_at',
        'total_amount',
        'comment',
        'guest_name',
        'guest_email',
        'guest_phone',
        'expires_at',
        'created_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function place(): BelongsTo
    {
        return $this->belongsTo(Place::class);
    }

    public function resource(): BelongsTo
    {
        return $this->belongsTo(Resource::class);
    }

    public function slots(): HasMany
    {
        return $this->hasMany(BookingSlot::class);
    }

    public function equipment(): HasMany
    {
        return $this->hasMany(BookingEquipment::class);
    }

    // Helpers
    public function canPay(): bool
    {
        return $this->payment_status === 'pending';
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function getTotalAmountFormatted(): string
    {
        return number_format($this->total_amount, 2, ',', ' ') . ' ₽';
    }

    // Имя клиента (user или guest)
    public function getClientName(): string
    {
        return $this->user?->name ?? $this->guest_name ?? 'Гость';
    }

    public function getClientEmail(): ?string
    {
        return $this->user?->email ?? $this->guest_email;
    }

    public function getClientPhone(): ?string
    {
        return $this->user?->phone ?? $this->guest_phone;
    }
}











class BookingSlot extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'booking_id',
        'slot_date',
        'slot_time',
        'slot_datetime',
    ];

    protected $casts = [
        'slot_datetime' => 'datetime',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}


class BookingEquipment extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'booking_id',
        'product_model_id',
        'qty',
        'price_each',
        'amount',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function productModel(): BelongsTo
    {
        return $this->belongsTo(ProductModel::class);
    }

    public function getAmountFormatted(): string
    {
        return number_format($this->amount / 100, 2, ',', ' ') . ' ₽';
    }
}