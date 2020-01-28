<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserAddress extends Model
{
    use SoftDeletes;
    //
    protected $table = 'orderaddress';
    protected $fillable = [
        'order_id', 'user_id', 'user_id', 'customer_id', 'city', 'address', 'landmark',
    ];

    protected $hidden = ['deleted_at', 'created_at', 'updated_at'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function orders()
    {
        return $this->hasMany('App\Order', 'address_id');
    }
}
