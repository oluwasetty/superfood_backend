<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    //
    use SoftDeletes;
    protected $table = 'orders';
    protected $fillable = [
        'delivery_method', 'order_total', 'cart_subtotal', 'store', 'payment_method', 'delivery_charge', 'comment', 'delivery_hour'
    ];

    protected $hidden = ['deleted_at'];


    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    public function orderItems()
    {
        return $this->hasMany('App\OrderItem', 'order_id')->with('product');
    }

    public function payments()
    {
        return $this->hasMany('App\Payment', 'order_id');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function address()
    {
        return $this->hasOne('App\OrderAddress', 'id', 'address_id');
    }
}
