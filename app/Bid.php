<?php

namespace GetJob;

use Illuminate\Database\Eloquent\Model;

class Bid extends Model
{
    protected $table = "bids";
    protected $fillable = [
        "job_id", "user_id", "price"
    ];

    function user(){
        return $this->belongsTo('GetJob\User');
    }

    function job(){
        return $this->belongsTo('GetJob\Job');
    }
}
