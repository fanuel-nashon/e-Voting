@extends('layouts.app')

@section('content')

<div class="container">
    <div class="d-flex justify-content-center align-items-center">
        <form id="form" onsubmit="return changePassword(event)">
            @csrf

            <div id="tokenSection">
                <label class="form-label">Enter Token:</label>
                <input type="text" name="token" id="token" class="form-control">

                <div id="err" class="alert alert-danger mt-2 d-none"></div>

                <button class="btn btn-primary mt-3">Verify Token</button>
            </div>

            <div id="passwordSection" style="display:none;">
                <label class="form-label">New Password</label>
                <input class="form-control" type="password" name="password" id="password">

                <label class="form-label mt-2">Confirm Password</label>
                <input class="form-control" type="password" name="password_confirmation" id="password_confirmation">

                <div id="passErr" class="alert alert-danger mt-2 d-none"></div>

                <button class="btn btn-success mt-3">Change Password</button>
            </div>
        </form>
    </div>
</div>


<script>
function changePassword(e){
    e.preventDefault();

    let form = document.getElementById('form');
    let formData = new FormData(form);

    fetch("{{ route('change.password') }}", {
        method: 'POST',
        headers: {
            "X-CSRF-TOKEN": document.querySelector('input[name=\"_token\"]').value
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {

        if (data.status === "token_valid") {
            document.getElementById("tokenSection").style.display = "none";
            document.getElementById("passwordSection").style.display = "block";
        }

        else if (data.status === "password_reset") {
            window.location.href = "{{ route('login') }}";
        }

        else {
            let err = document.getElementById("err");
            err.textContent = data.message;
            err.classList.remove("d-none");
        }
    })
    .catch(error => {
        console.log(error);
        let err = document.getElementById("err");
        err.textContent = "Something went wrong.";
        err.classList.remove("d-none");
    });

    return false;
}
</script>

@endsection
