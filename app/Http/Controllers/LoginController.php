<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(Request $request){
        $credentials=$request->validate([
            'email'=>'email|required',
            'password'=>'required|string'
        ]);

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
