<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CartItem extends Model
{
    //
    use SoftDeletes;
    protected $table = 'cartitems';
    protected $fillable = [
        'cart_id', 'product_id', 'quantity', 'price'
    ];

    protected $hidden = ['deleted_at', 'created_at', 'updated_at'];


    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    public function cart()
    {
        return $this->belongsTo('App\Cart');
    }

    public function product()
    {
        return $this->hasOne('App\Product', 'id', 'product_id');
    }
}
