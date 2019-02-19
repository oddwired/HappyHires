<?php

namespace GetJob\Http\Controllers;

use GetJob\Producer;
use GetJob\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request){
        $this->validate($request, [
            "email" => 'required',
            "password" => 'required'
        ]);

        $credentials = [
            "email" => $request->email,
            "password" => $request->password
        ];

        if(Auth::guard("user")->attempt($credentials)){
            if($request->has('re'))
                return redirect($request->re);
            return redirect(url("user"));
        }

        if($request->has('re'))
            return redirect(url("login"))->with("re",$request->re)->withErrors(["error"=>"Invalid credentials"]);
        return redirect(url("login"))->withErrors(["error"=>"Invalid credentials"]);
    }

    public function adminLogin(Request $request){
        $this->validate($request, [
            "username" => 'required',
            "password" => 'required'
        ]);
        $credentials = [
            "username" => $request->username,
            "password" => $request->password
        ];

        if(Auth::guard("admin")->attempt($credentials)){
            return redirect(url("admin"));
        }

        return redirect(url("admin/login"))->withErrors(["error"=>"Invalid credentials"]);
    }

    public function register(Request $request){
        $this->validate($request, [
            "name"=> 'required',
            "email"=> 'required',
            "password"=> 'required',
            "phone" => 'required',
            "nationalid" => 'required'
        ]);

        $details = [
            "name"=> $request->name,
            "email"=> $request->email,
            "password"=> bcrypt($request->password),
            "phone"=> $request->phone,
            "national_id"=> $request->nationalid,
        ];

        if(User::where("email", $details["email"])->exists())
            return redirect(url('register'))->withErrors(["error"=> "Email exists"]);

        if(!is_null(User::create($details)))
            return redirect(url('login'))->with(["info"=> "Registration successful"]);

        return redirect(url('register'))->with(["error"=> "An error occured. Please try again"]);
    }
}
