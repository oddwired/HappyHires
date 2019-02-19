<?php

namespace GetJob\Http\Controllers;

use GetJob\Bid;
use GetJob\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BidController extends Controller
{
    public function makeBid(Request $request){
        $counter_offer = $request->offer;
        $jobid = $request->jobid;
        $userid = Auth::guard("user")->id();

        $job = Job::where("id", $jobid)->first();

        if($job->user_id == $userid)
            return redirect("/")->withErrors(["error"=> "You cannot bid on your own jobs!"]);

        if(!is_null(Bid::create([
            "job_id"=> $jobid,
            "user_id"=> $userid,
            "price" => $counter_offer
        ])))
            return redirect("/")->with("info", "You have bid on ".$job->name." successfully.");

        return redirect("/")->withErrors(["error"=> "An error occurred. Please try again later!"]);
    }

    public function acceptBid(Request $request){
        $bid = Bid::where("id", $request->bidid)->first();
        $job = Job::where('id', $request->jobid)->first();

        $bid->won = true;
        $bid->save();

        $job->winner_id = $request->bidid;
        $job->save();

        return back();
    }
}
