<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Override;

class OrderDetail extends Model
{
    protected $fillable = [
        'product_id',
        'order_id',
        'qty',
        'subtotal'
    ];
    #[Override]
    public function belongsTo($related, $foreignKey = null, $ownerKey = null, $relation = null)
    {
        return parent::belongsTo($related, $foreignKey, $ownerKey, $relation);
    }
}
