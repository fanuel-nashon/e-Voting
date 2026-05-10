@extends('layouts.app')

@section('content')
    <p>Hi {{$data->email}}</p>

    <p>Please find the token below to reset your password, If it is not you who requested this change please ignore</p>

    <p>{{$data->token}}</p>

    <button class="btn btn-primary" onclick="window.location.href='{{ route('enterToken') }}'">
        Reset Password
    </button>

@endsection
