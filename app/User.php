<?php

namespace GetJob;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;
    protected $table = "users";
    protected $fillable = [
        "name", "email", "phone", "national_id", "password"
    ];

    protected $hidden = [
        "password", "remember_token"
    ];

    function jobs(){
        return $this->hasMany('GetJob\Job');
    }

    function bids(){
        return $this->hasMany('GetJob\Bid');
    }

    function requests(){
        return $this->hasMany('GetJob\Requests');
    }

    function transactions(){
        return $this->hasMany('GetJob\Transaction');
    }
}
