<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cart extends Model
{
    //
    use SoftDeletes;
    protected $table = 'carts';
    protected $fillable = [
        'user_id', 'quantity', 'total_price',
    ];

    protected $hidden = ['deleted_at', 'created_at', 'updated_at'];


    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    public function cartItems()
    {
        return $this->hasMany('App\CartItem', 'cart_id')->with('product');
    }

    public function user()
    {
        return $this->belongsTo('App\User')->with('addresses');
    }
}
