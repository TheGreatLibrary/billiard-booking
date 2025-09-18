<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StateProduct extends Model
{
    public $timestamps = false;
    protected $table = 'state_product';
    protected $guarded = ['id'];

    public function resources(): HasMany { return $this->hasMany(Resource::class, 'state_id'); }
}