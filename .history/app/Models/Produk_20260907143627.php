<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Produk extends Model
{
    protected $fillable = [
        'name',
        'price',
        'stok'
    ];
    public function orderdetail():HasMany{
        return $this->hasMany()
    }
}
