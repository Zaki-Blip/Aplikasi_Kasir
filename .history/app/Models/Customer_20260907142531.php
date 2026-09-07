<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    public function order ()
    protected $fillable = [
        'name',
        'phone',
        'address'
    ];
}
