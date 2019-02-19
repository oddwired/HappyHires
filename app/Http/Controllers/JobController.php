<?php

namespace GetJob\Http\Controllers;

use GetJob\Bid;
use GetJob\Constants;
use GetJob\Http\Mpesa\Mpesa;
use GetJob\Job;
use GetJob\Requests;
use GetJob\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobController extends Controller
{

    private function doPay($job, $user){
        $mpesa = new Mpesa();

        $token_request = $mpesa->getAccessToken();
        if($token_request == null){
            return redirect("user")->withErrors(["error"=>"An error occurred Please try again later"]);
        }

        $access_token = $token_request->access_token;

        $amount = (5/100) * $job->price;
        $phone = $user->phone;
        $callback_parameters ="&phone=".$phone;

        $transaction_request = $mpesa->makeRequest($phone, $amount, $access_token, $callback_parameters);
        //dd($transaction_request);
        if($transaction_request["ResponseCode"] != "0"){
            return redirect(url('user'))->withErrors(["error"=>"An error occurred. Please try again later!"]);
        }

        Requests::create([
            "date" => date('Y-m-d', time()),
            "user_id" => $user->id,
            "merchant_request_id"=> $transaction_request["MerchantRequestID"],
            "amount"=>$amount,
            "status" => Constants::STATUS_PENDING,
            "job_id"=> $job->id
        ]);

        return redirect(url('user'))->with('info', "Job added to pending list until the transaction is complete");
    }

    public function performTransaction(Request $request){
        $job = Job::where('id', $request->id)->first();
        $user = User::where('id', Auth::guard('user')->id())->first();

        return $this->doPay($job, $user);
    }

    public function postJob(Request $request){
        $user = User::where('id', Auth::guard('user')->id())->first();
        $this->validate($request, [
            'name'=> 'required',
            'description'=> 'required',
            'location'=> 'required',
            'price'=> 'required',
        ]);

        $data = [
            'user_id'=> $user->id,
            'name'=> $request->name,
            'description'=> $request->description,
            'location'=> $request->location,
            'price'=> $request->price
        ];

        $job = Job::create($data);
        if(is_null($job)){
            return redirect(url("postjob"))->withErrors(["error"=> "An error occurred. Plase try again"]);
        }
        return $this->doPay($job, $user);
    }

    public function getJob(Request $request){
        $job = Job::with('bids')->where('id', $request->id)->first();
        $user = User::where('id', $job->user_id)->first();

        if(!is_null($job->winner_id)){
            $winner_bid = Bid::where('id', $job->winner_id)->first();
            $winner_user = User::where('id', $winner_bid->user_id)->first();

            return view('job.job', ['job'=>$job,
                'user'=> $user,
                'winner_bid'=>$winner_bid,
                'winner_user'=> $winner_user]);
        }

        return view('job.job', ['job'=>$job, 'user'=> $user]);
    }
}
