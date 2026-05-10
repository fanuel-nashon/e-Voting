@extends('layouts.app')

@section('title', 'Reset Password')

@section('content')

    <div class="container">
        <div class="d-flex justify-content-center align-items-center vh-100">
            <div class="col-md-6 shadow-lg px-2 py-3">
                <form id="resetForm" onsubmit="return sendToken(event)">
                    @csrf
                    <h4 class="mb-2 text-center">Reset Password</h4>
                    <div class="mb-3">
                        <label class="form-label" for="email">Email</label>
                        <input class="form-control" type="email" name="email" id="email" placeholder="Enter valid email">
                    </div>
                    <button id="btn" class="btn btn-primary" style="margin-left: 200px;">Get Token</button>
                    <div id="err" class="alert alert-danger mt-3 d-none"></div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function sendToken(e){
            e.preventDefault();

            let form = document.getElementById('resetForm');
            let formData = new FormData(form);
            // let btn = document.getElementById('btn');

            fetch("{{ route('reset.password') }}", {
                method: 'POST',
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value
                },
                body:formData
            })
            .then(response=>response.json())
            .then(data=>{
                if(data.status==="success"){
                    window.location.href="{{ route('token') }}";
                }
                else{
                    let err = document.getElementById('err');
                    err.textContent=data.message;
                    err.classList.remove('d-none');
                }
            })
            .catch(error=> {
                console.log("Error:", error);
                let err = document.getElementById('err');
                err.classList.remove('d-none');
            });

            return false;
        }
    </script>

@endsection
