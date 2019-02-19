<?php

namespace GetJob;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    protected $table = "jobs";
    protected $fillable = [
        "user_id", "name", "description", "location", "price"
    ];

    function user(){
        return $this->belongsTo('GetJob\User');
    }

    function bids(){
        return $this->hasMany('GetJob\Bid');
    }

    function requests(){
        return $this->hasMany('GetJob\Requests');
    }
}
