<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use SoftDeletes;
    //
    protected $table = 'payments';
    protected $fillable = [
        'order_id', 'user_id', 'customer_id', 'transaction_ref', 'customer_id', 'payment_amount', 'payment_method', 'payment_ref',
    ];

    protected $hidden = ['deleted_at'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
}
