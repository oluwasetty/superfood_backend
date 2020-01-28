<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Empowerment extends Model
{
    use SoftDeletes;
    //
    protected $fillable = [
        "user_id",
        "company",
        "dob",
        "gender",
        "city",
        "address",
        "state",
        "education",
        "interest",
        "country"
    ];


    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
}
