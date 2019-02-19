<?php

namespace GetJob;

use Illuminate\Database\Eloquent\Model;

class Requests extends Model
{
    protected $table = "mpesa_requests";
    protected $fillable = [
        "user_id", "merchant_request_id", "status", "date", "amount", "job_id"
    ];

    function user(){
        return $this->belongsTo('GetJob\User');
    }

    function job(){
        return $this->belongsTo('GetJob\Job');
    }
}
