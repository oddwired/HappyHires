<?php

namespace GetJob\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PhotoHandler extends Controller
{
    public function addPhoto(Request $request){
        // dd($request);
        //return $request->cropped;
        //$image = $request->cropped;

        $image_name= md5(time().rand()).'.png';

        $request->cropped->move(public_path('img/'), $image_name);


        return response()->json(['imagename'=>$image_name]);
    }
}
