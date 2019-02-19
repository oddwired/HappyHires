<?php

namespace GetJob\Http\Controllers;

use GetJob\Constants;
use GetJob\Http\Mpesa\Mpesa;
use GetJob\Job;
use GetJob\Requests;
use GetJob\Transaction;
use GetJob\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    private function uploadPhoto(Request $request){
        // dd($request);
        //return $request->cropped;
        //$image = $request->cropped;

        $image_name= md5(time().rand()).'.png';

        $request->cropped->move(public_path('img/'), $image_name);


        return $image_name;
    }

    public function changePhoto(Request $request){
        $user = User::where('id', Auth::guard("user")->id())->first();
        /*if($request->has('cropped')){


            return;
        }*/

        $user->photo = $this->uploadPhoto($request);
        $user->save();

        return;
    }

    public function index(){
        $user_id = Auth::guard('user')->id();

        $pending_transactions = Requests::where("user_id", $user_id)
            ->where("status", Constants::STATUS_PENDING)
            ->get();

        foreach ($pending_transactions as $pending_transaction){
            $mpesa = new Mpesa();

            $response = $mpesa->confirmTransaction($pending_transaction->merchant_request_id);
            if($response != "null"){
                $transaction_status = json_decode($response);

                $table_data = array(
                    'merchant_request_id'=> $pending_transaction->merchant_request_id,
                    'transaction_date'=> $transaction_status->TransactionDate,
                    'phone'=> $transaction_status->PhoneNumber,
                    'result_code'=> $transaction_status->ResultCode,
                    'result_desc'=> $transaction_status->ResultDesc,
                    'user_id'=> $user_id,
                );

                if($transaction_status->ResultCode != 0){
                    // Transaction was not successful
                    // We need to save the transaction details and modify the service status

                    Requests::where('merchant_request_id', $pending_transaction->merchant_request_id)
                        ->update(['status'=> Constants::STATUS_FAILED]);

                    Transaction::create($table_data);

                }else{
                    // Transaction was successful
                    $request = Requests::where('merchant_request_id', $pending_transaction->merchant_request_id)->first();
                    $request->status = Constants::STATUS_SUCCESSFUL;
                    $request->save();

                    $job = Job::where('id', $request->job_id)->first();
                    $job->merchant_request_id = $pending_transaction->merchant_request_id;
                    $job->save();

                    $table_data['mpesa_receipt'] = $transaction_status->MpesaReceiptNumber;
                    $table_data['amount'] = $transaction_status->Amount;

                    Transaction::create($table_data);

                }
            }
        }

        $transactions = Transaction::where("user_id", Auth::guard('user')->user()->id)->get();

        $pending = Requests::where("user_id", Auth::guard('user')->user()->id)
            ->where("status", Constants::STATUS_PENDING)
            ->get();

        $user = User::with('jobs', 'bids', 'jobs.requests')->where('id', $user_id)->first();
        $wonbids = array_map(create_function('$bid', 'if($bid["won"]) return $bid;'), $user->bids->toArray());
        $lostbids = array_map(create_function('$bid', 'if(!$bid["won"]) return $bid;'), $user->bids->toArray());
        $wonjob_ids = array_map(create_function('$bid', 'return $bid["job_id"];'), $wonbids);
        $lostjob_ids = array_map(create_function('$bid', 'return $bid["job_id"];'), $lostbids);
        $user->wonbidjobs = Job::whereIn('id', $wonjob_ids)->get();
        $user->lostbidjobs = Job::whereIn('id', $lostjob_ids)->get();

        //dd(array_filter(array_map(create_function('$job', 'if(($job["winner_id"] == null)) return $job;'), $user->lostbidjobs->toArray())));
        //dd($user);

        return view('user.user', ['user'=>$user,
            'transactions'=> $transactions,
            'pending_transactions'=> $pending]);
    }

    public function settingsIndex(){
        $user = Auth::guard("user")->user();

        return view('user.settings', ['user'=>$user]);
    }

    public function settings(Request $request){
        $this->validate($request, [
            "name"=> 'required',
            "email"=> 'required',
            "phone" => 'required',
            "nationalid" => 'required'
        ]);

        $user = User::where('id', Auth::guard("user")->id())->first();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->national_id = $request->nationalid;

        $user->save();

        return back();
    }

    public function updateTrainee(){
        $user = User::where('id', Auth::guard("user")->id())->first();

        if($user->is_trainee){
            $user->is_trainee = false;
        }else{
            $user->is_trainee = true;
        }

        $user->save();

        return back();
    }
}
