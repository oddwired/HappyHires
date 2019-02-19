<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get("/", "HomeController@index");
Route::get("job/{id}", "JobController@getJob");

/* User specific routes */

Route::get("login", function(){
    return view("auth.user_login");
});

Route::post("login", "AuthController@login");

Route::get("register", function(){
    return view('auth.user_register');
});

Route::post('register', "AuthController@register");

Route::group(["middleware"=>["user"]], function(){
    Route::get("user", "UserController@index");
    Route::get('postjob', function(){
        return view('user.postjob');
    });

    Route::post("postjob", "JobController@postJob");

    Route::get("bid","BidController@makeBid");

    Route::post("changephoto", "UserController@changePhoto");
    Route::get("acceptbid/{jobid}/{bidid}", "BidController@acceptBid");
    Route::get("settings", "UserController@settingsIndex");
    Route::post("settings", "UserController@settings");

    Route::get("changetrainee", "UserController@updateTrainee");

    Route::get("retrytransaction/{id}", "JobController@performTransaction");
});

Route::post("logout", function (){
    Auth::guard("user")->logout();
    Auth::guard("admin")->logout();

    return redirect(url("/"));
});




/* Admin Routes */
Route::get("admin/login", function (){
    return view("auth.admin_login");
});

Route::post("admin/login", "AuthController@adminLogin");

Route::group(["middleware"=>["admin"]], function (){

});