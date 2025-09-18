<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriceRule extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];

    protected $casts = [
        'value' => 'decimal:3',
    ];

    public function place(): BelongsTo { return $this->belongsTo(Place::class); }
    public function zone(): BelongsTo { return $this->belongsTo(Zone::class); }
}