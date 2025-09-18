<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Resource extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];

    public function model(): BelongsTo { return $this->belongsTo(ProductModel::class, 'model_id'); }
    public function zone(): BelongsTo { return $this->belongsTo(Zone::class); }
    public function state(): BelongsTo { return $this->belongsTo(StateProduct::class, 'state_id'); }
    public function bookingResources(): HasMany { return $this->hasMany(BookingResource::class); }
}