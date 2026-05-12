<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [

        'user_id',
        'shopify_order_id',

        'status',
        'payment_status',

        'subtotal',
        'shipping',
        'total',

        'tracking_number',

        'customer_name',
        'customer_email',

        'address',
        'city',
        'postal_code',
        'country'
    ];
}