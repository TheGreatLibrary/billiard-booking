<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];

    protected $casts = [
        'qty' => 'integer',
        'price_each' => 'integer',
        'amount' => 'integer',
    ];

    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
    public function bookingResource(): BelongsTo { return $this->belongsTo(BookingResource::class); }
    public function productModel(): BelongsTo { return $this->belongsTo(ProductModel::class); }
}