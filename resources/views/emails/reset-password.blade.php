@extends('layouts.app')

@section('content')
    <div style="font-family: sans-serif; line-height: 1.5;">
        <p>Hi <strong>{{ $data->email }}</strong>,</p>

        <p>Please find the token below to reset your password. If you did not request this change, please ignore this email.</p>

        <div style="font-size: 24px; font-weight: bold; margin: 20px 0; color: #091c3d;">
            {{ $data->token }}
        </div>

        <p>Click the button below to enter your token and choose a new password:</p>

        <a href="{{ route('enterToken') }}"
           style="background-color: #f5951b;
                  color: white;
                  padding: 10px 20px;
                  text-decoration: none;
                  border-radius: 5px;
                  display: inline-block;">
            Reset Password
        </a>
    </div>
@endsection
