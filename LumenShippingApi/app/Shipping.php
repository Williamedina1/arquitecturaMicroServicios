<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Shipping extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'address',
        'shipping_method',
        'cost',
        'status',
        'tracking_number',
    ];
}
