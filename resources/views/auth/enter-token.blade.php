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
let inPasswordStep = false;

function showError(message) {
    const el = document.getElementById(inPasswordStep ? 'passErr' : 'err');
    el.textContent = message;
    el.classList.remove('d-none');
}

function clearErrors() {
    ['err', 'passErr'].forEach(id => {
        const el = document.getElementById(id);
        el.textContent = '';
        el.classList.add('d-none');
    });
}

function changePassword(e) {
    e.preventDefault();
    clearErrors();

    const formData = new FormData(document.getElementById('form'));
    const csrfToken = document.querySelector('input[name="_token"]').value;

    fetch("{{ route('change.password') }}", {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken },
        body: formData
    })
    .then(async res => {
        const data = await res.json();

        // Laravel validation failure (422) — extract the first error message
        if (res.status === 422) {
            const msg = data.errors
                ? Object.values(data.errors).flat()[0]
                : (data.message || 'Validation failed.');
            throw new Error(msg);
        }

        return data;
    })
    .then(data => {
        if (data.status === 'token_valid') {
            inPasswordStep = true;
            document.getElementById('tokenSection').style.display = 'none';
            document.getElementById('passwordSection').style.display = 'block';

        } else if (data.status === 'password_reset') {
            window.location.href = "{{ route('login') }}";

        } else {
            showError(data.message || 'Something went wrong.');
        }
    })
    .catch(err => showError(err.message));

    return false;
}
</script>

@endsection
