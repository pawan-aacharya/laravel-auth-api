<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(Request $request){
        //validate the request
        $request->validate([
            'name'=>'required',
            'email'=>'required|email|unique:users',
            'password'=>'required|min:6|confirmed',
            'tc'=>'required'
        ]);
        if(User::where('email',$request->email)->count()){
            return response()->json([
                'message'=>'email already exist',
                'status'=>'failed'
            ],200);
        }
        $user=User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>Hash::make($request->password),
            'tc'=>json_decode($request->tc)
        ]);
        $token=$user->createToken($request->email)->plainTextToken;

        return response()->json([
            'token'=>$token,
            'message'=>'registration successfully',
            'status'=>'success',
            'data'=>$user
        ],201);
    }

    public function login(Request $request){
        //validate the request
        $request->validate([
            'email'=>'required|email',
            'password'=>'required'
        ]);

       $user=User::where('email',$request->email)->first();

       if($user && Hash::check($request->password, $user->password)){
        $token=$user->createToken($request->email)->plainTextToken;
        return response()->json([
            'token'=>$token,
            'message'=>'login successfull',
            'status'=>'success'
        ],201);
       }
    }

    public function logout(){
        auth()->user()->tokens()->delete();
        return response([
            'message'=>'logged out successfully',
            'status'=>'success'
        ],200);
    }

    public function logged_user(){
        $loggeduser=auth()->user();
        return response([
            'message'=>'logged user data',
            'user'=>$loggeduser
        ]);
    }

    public function change_password(Request $request){
        $request->validate([
            'password'=>'required|confirmed'
        ]);
        $loggeduser=auth()->user();
        $loggeduser->password=Hash::make($request->password);
        $loggeduser->save();
        return response()->json([
            'message'=>'password changed successfully',
            'status'=>'success'
        ],200);
    }
}
