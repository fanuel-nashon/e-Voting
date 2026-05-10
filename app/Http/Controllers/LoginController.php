<?php

namespace App\Http\Controllers;

use App\Mail\ResetPassword;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class LoginController extends Controller
{
    public function login(Request $request){
        $credentials=$request->validate([
            'email'=>'email|required',
            'password'=>'required|string'
        ]);

        $user=User::where('email', $credentials['email'])->first();

        if(!$user){
            return response()->json([
                'status'=>'error',
                'message'=>'User not found'
            ]);
        }

        if(Auth::attempt($credentials))
            {
                return response()->json([
                    'status'=>'success',
                    'redirect'=>route('dashboard')
                ]);
            }

            return response()->json([
                'status'=>'error',
                'message'=>'Invalid email or password'
            ]);
    }

    public function resetPassword(Request $request){
        // dd('here');
        try{
            $validated=$request->validate([
                'email'=>'email|required',
            ]);

            $user = User::where('email', $validated['email'])->first();

            if(!$user){
                return response()->json([
                    'status'=>'error',
                    'message'=>'Email not found',
                ]);
            }

            $token = rand(100000, 999999);

            DB::table('password_reset_tokens')->updateOrInsert(
                ['email'=>$validated['email']],
                ['token'=>$token, 'created_at'=>now()]
            );

            $data = (object)[
                'email'=>$validated['email'],
                'token'=>$token
            ];

            Mail::to($validated['email'])->send(new ResetPassword($data));
            return response()->json([
                'status'=>'success',
                'message'=>'Check your email',
            ]);
        }
        catch(\Exception $e){
            Log::error('Password reset error: '.$e->getMessage());
            return response()->json([
                'status'=>'error',
                'message'=>$e->getMessage(),
            ]);
        }

    }

    public function changePassword(Request $request)
    {
        if (!$request->password) {
            $request->validate([
                'token' => 'required|string'
            ]);

            $tokenRecord = DB::table('password_reset_tokens')
                ->where('token', $request->token)
                ->first();

            if (!$tokenRecord) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid or expired token'
                ]);
            }

            return response()->json([
                'status' => 'token_valid'
            ]);
        }

        $request->validate([
            'password' => 'required|min:6|confirmed',
        ]);

        $tokenRecord = DB::table('password_reset_tokens')
            ->where('token', $request->token)
            ->first();

        if (!$tokenRecord) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid or expired token'
            ]);
        }

        User::where('email', $tokenRecord->email)
            ->update([
                'password' => Hash::make($request->password)
            ]);

        DB::table('password_reset_tokens')
            ->where('token', $request->token)
            ->delete();

        return response()->json([
            'status' => 'password_reset',
            'message' => 'Password successfully updated'
        ]);
    }
}
