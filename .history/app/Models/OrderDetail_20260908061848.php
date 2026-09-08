<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $fillable = [
        'product_id',
        'order_id',
        'qty',
        'subtotal'
    ];
    public function product
}
