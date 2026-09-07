<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    public
    protected $fillable = [
        'name',
        'phone',
        'address'
    ];
}
