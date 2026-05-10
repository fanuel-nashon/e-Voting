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
                    <div id="success" class="alert alert-success mt-3 d-none"></div>
            </div>
        </div>
    </div>

    <script>
        function sendToken(e) {
            e.preventDefault();

            let form = document.getElementById('resetForm');
            let btn = document.getElementById('btn');
            let formData = new FormData(form);
            let successMsg = document.getElementById('success');
            let errMsg = document.getElementById('err');

            btn.disabled = true;
            btn.textContent = "Sending...";

            fetch("{{ route('reset.password') }}", {
                method: 'POST',
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value,
                    "Accept": "application/json"
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    form.classList.add('d-none');
                    successMsg.textContent = "Email sent successfully. Please check your inbox for the token.";
                    successMsg.classList.remove('d-none');
                    errMsg.classList.add('d-none');
                } else {
                    errMsg.textContent = data.message;
                    errMsg.classList.remove('d-none');
                    btn.disabled = false;
                    btn.textContent = "Get Token";
                }
            })
            .catch(error => {
                console.error("Error:", error);
                errMsg.textContent = "Something went wrong. Please try again later.";
                errMsg.classList.remove('d-none');
                btn.disabled = false;
                btn.textContent = "Get Token";
            });

            return false;
        }
    </script>

@endsection
