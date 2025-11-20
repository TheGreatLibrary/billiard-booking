<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductModel extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];

    protected $casts = [
        'base_price_hour' => 'integer',
        'base_price_each' => 'integer',
    ];

    public function type(): BelongsTo { return $this->belongsTo(ProductType::class, 'product_type_id'); }
    public function resources(): HasMany { return $this->hasMany(Resource::class, 'model_id'); }
    public function orderItems(): HasMany { return $this->hasMany(OrderItem::class); }
}