<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{

    public function customers ():BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
    protected $fillable = [
        'customer_id',
        'date',
        'total_price',
        'discount',
        'discount_amount',
        'total_payment',
        'payment_method',
        'payment_status'
    ];
    public function orderdetail()
    {
        return $this->hasMany(OrderDetail::class);
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }
}
