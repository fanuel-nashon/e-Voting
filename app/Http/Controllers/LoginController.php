<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
}
