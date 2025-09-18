<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Zone extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];
    protected $casts = ['price_coef' => 'decimal:3'];

    public function place(): BelongsTo { return $this->belongsTo(Place::class); }
    public function resources(): HasMany { return $this->hasMany(Resource::class); }
    public function priceRules(): HasMany { return $this->hasMany(PriceRule::class); }
}