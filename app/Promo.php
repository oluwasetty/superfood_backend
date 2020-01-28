<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Promo extends Model
{
    use SoftDeletes;
    //
    protected $table = 'promos';
    protected $fillable = [
        'name', 'type', 'value_percent', 'start_date', 'end_date',
    ];

    public function products()
    {
        return $this->hasMany('App\Product');
    }

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
}
