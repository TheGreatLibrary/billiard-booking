<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductType extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];

    public function models(): HasMany { return $this->hasMany(ProductModel::class); }
}