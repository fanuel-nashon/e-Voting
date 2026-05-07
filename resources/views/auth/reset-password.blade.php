@extends('layouts.app')

@section('title', 'Reset Password')

@section('content')

    <div class="container">
        <div class="d-flex justify-content-center align-items-center vh-100">
            <div class="col-md-6 shadow-lg px-2 py-3">
                <form id="resetForm" onsubmit="return resetPassword(event)">
                    @csrf
                    <h4 class="mb-2 text-center">Reset Password</h4>
                    <div class="mb-3">
                        <label class="form-label" for="email">Email</label>
                        <input class="form-control" type="email" name="email" id="email" placeholder="Enter valid email">   
                    </div>
                </form>
            </div>
        </div>
    </div>        

@endsection