<?php

namespace GetJob\Http\Controllers;

use GetJob\Job;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(){
        //dd($request);
        $jobs = Job::with('bids')->whereNull("winner_id")
            ->whereNotNull('merchant_request_id')
            ->orderBy('price', 'dec')
            ->get();
        //dd($jobs);
        return view("home", ["jobs"=>$jobs]);
    }
}
