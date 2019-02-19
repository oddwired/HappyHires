<?php

namespace GetJob\Http\Mpesa;

use Ixudra\Curl\Facades\Curl;

class Mpesa {

    public function __construct()
    {

    }

    public function getAccessToken(){
		
		$url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
		$APP_CONSUMER_KEY = "";	// TODO: Insert your consumer key here
		$APP_CONSUMER_SECRET = "";	// TODO: Insert your consumer secret here
        $credentials = base64_encode($APP_CONSUMER_KEY.":".$APP_CONSUMER_SECRET);
		$response = Curl::to($url)
            ->withHeader('Authorization: Basic '.$credentials)
            ->returnResponseObject()
            ->get();

		$content = json_decode($response->content);

		return $content;
	}
	
	public function makeRequest($phone, $amount, $access_token, $parameters){
		
		$url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
        $time = date('Ymdhis');
        $phone = $this->sanitizePhone($phone);
        $paybill = "174379";

        $post_data = array(
            //Fill in the request parameters with valid values
            "BusinessShortCode"=> $paybill,
            "Password"=> base64_encode($paybill."bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919".$time),
            "Timestamp"=> $time,
            "TransactionType"=> "CustomerPayBillOnline",
            "Amount"=> $amount,
            "PartyA"=> $phone,
            "PartyB"=> $paybill,
            "PhoneNumber"=> $phone,
            "CallBackURL"=> "", //TODO: Enter callback url
            "AccountReference"=> "test",
            "TransactionDesc"=> "test"
        );
        //dd($post_data);
		$response = Curl::to($url)
            ->withHeader('Authorization:Bearer '.$access_token)
            ->withData($post_data)
            ->asJson(true)
            ->returnResponseObject()
            ->post();

		$content = $response->content;

		return $content;
	}

    private function sanitizePhone($phone){
        if(strlen($phone) == 10 && $phone[0] == "0"){
            $phone = "254".substr($phone,1);
        }else if(strlen($phone) == 13 && $phone[0] == "+"){
            $phone = substr($phone, 1);
        }

        return $phone;
    }

    public function confirmTransaction($merchantId){
        $url = "";// TODO: Enter url for confirming transactions

        $response = Curl::to($url)
            ->returnResponseObject()
            ->get();

        $content = $response->content;

        return $content;
    }
}
