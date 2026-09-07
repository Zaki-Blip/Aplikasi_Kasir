<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public function customers
    protected $fillable = [
        'customer_id',
        'date',
        'total_price'
    ];
}
