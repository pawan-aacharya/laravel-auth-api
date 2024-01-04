<?php

namespace App\Http\Controllers;

use App\Models\PasswordReset;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\support\Str;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Hash;

class PasswordResetController extends Controller
{
    public function send_reset_password_email(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $email = $request->email;

        //check user's email exists or not
        $user = User::where('email', $email)->first();
        if (!$user) {
            return response([
                'message' => 'email doesnt exist',
                'status' => 'failed'
            ], 404);
        }

        //generate token
        $token = Str::random(60);

        PasswordReset::create([
            'email' => $email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        // dump("http://127.0.0.1:8000/api/user/reset/" .$token);

        //sending email with password reset view
        Mail::send('reset', ['token' => $token], function (Message $message) use ($email) {
            $message->subject('reset your passwrod');
            $message->to($email);
        });

        return response([
            'message' => 'password reset email sent....... check your email',
            'status' => 'success'
        ], 200);
    }

    public function reset(Request $request, $token)
    {
        $request->validate([
            'password' => 'required|confirmed'
        ]);

        $passwordReset = PasswordReset::where('token', $token)->first();

        if (!$passwordReset) {
            return response([
                'message' => 'token is invalid',
                'status' => 'failed'
            ], 400);
        }
        $user = User::where('email', $passwordReset->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        //delete the token after reseting password
        PasswordReset::where('email', $user->email)->delete();

        return response([
            'message' => 'password reset success',
            'status' => 'success'
        ], 200);
    }
}
