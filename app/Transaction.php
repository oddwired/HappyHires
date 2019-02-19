<?php

namespace GetJob;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = "transactions";
    protected $fillable = [
        'merchant_request_id',
        'transaction_date',
        'mpesa_receipt',
        'amount',
        'phone',
        'result_code',
        'result_desc',
        'user_id'
    ];

    protected $dates = [
        'transaction_date'
    ];

    function user(){
        return $this->belongsTo('GetJob\User');
    }
}
