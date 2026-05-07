@extends('layouts.app')

@section('title', 'eVoting Login')

@section('content')

    <div class="container">
        <div class="d-flex  justify-content-center align-items-center vh-100">
            <div class="col-md-6 shadow-lg px-2 py-3">
                <form id="loginForm" onsubmit="return checkLogin(event)">
                    @csrf
                    <h4 class="mb-2 text-center">Students Organization e-Voting</h4>
                    <div class="mb-3">
                        <label class="form-label" for="email">Email</label>
                        <input class="form-control" type="email" name="email" id="email" placeholder="Enter valid email">
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <label class="form-label" for="password">Password</label>
                            <p class="text-danger">
                                <a href="#">Forgot your password?</a>
                            </p>
                        </div>
                        <input class="form-control" type="password" name="password" id="password" placeholder="Enter valid password">
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <button class="btn btn-primary w-100">
                            Login
                        </button>
                    </div>
                    <div id="loginError" class="alert alert-danger mt-3 d-none"></div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function checkLogin(){
            event.preventDefault();

            let form=document.getElementById('loginForm');
            let formData= new FormData(form);

            fetch("{{ route('login.submit') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value
                },
                body: formData
            })
            .then(response => response.json())
            .then(data=>{
                if(data.status==="success"){
                    window.location.href=data.redirect;
                }
                else{
                    let err = document.getElementById('loginError');
                    err.textContent=data.message;
                    err.classList.remove('d-none');
                }
            })

            .catch(error=> {
                console.log("Error:", error);
                let err = document.getElementById('loginError');
                err.textContent="Something went wrong. Please try again.";
                err.classList.remove('d-none');
            });

            return false;


        }
    </script>

@endsection
