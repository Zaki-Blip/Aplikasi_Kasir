<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Produk extends Model
{
    protected $fillable = [
        'name',
        'price',
        'stok',
        'image',
        'brand_id',
        'category_id',
        'subcategory_id'
    ];
    public function orderdetail():HasMany{
        return $this->hasMany(OrderDetail::class);
    }
    public function brand():BelongsTo{
        return $this->belongsTo(Brand::class);
    }
    public function category():BelongsTo{
        return $this->belongsTo(Category::class);
    }
    public function subcategory():BelongsTo{
        return $this->belongsTo(Category::class);
    }
}
