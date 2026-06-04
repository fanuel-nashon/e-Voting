@extends('layouts.app')

@section('title', 'Forgot Password - e-Voting')

@section('content')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<div class="auth-bg d-flex align-items-center justify-content-center min-vh-100 p-3">
    <div class="auth-card">

        <!-- Header bar -->
        <div class="auth-card-top">
            <div class="auth-logo-sm">
                <i class="bi bi-shield-check"></i>
            </div>
            <h5 class="fw-bold mb-1">Forgot your password?</h5>
            <p class="mb-0 opacity-75 small">Enter your email and we'll send you a reset token.</p>
        </div>

        <div class="auth-card-body">

            <!-- Step: Email form -->
            <form id="resetForm" onsubmit="return sendToken(event)">
                @csrf
                <div class="mb-4">
                    <label class="form-label fw-semibold text-dark" for="email">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text auth-input-icon"><i class="bi bi-envelope"></i></span>
                        <input class="form-control auth-input" type="email" name="email" id="email"
                               placeholder="you@example.com" required autocomplete="email">
                    </div>
                </div>

                <div id="err" class="alert alert-danger py-2 small d-none mb-3"></div>

                <button id="sendBtn" type="submit" class="btn auth-btn-primary w-100">
                    <span id="sendBtnText"><i class="bi bi-send me-2"></i>Send Reset Token</span>
                </button>
            </form>

            <!-- Step: Success message (shown after submission) -->
            <div id="successState" class="d-none text-center py-2">
                <div class="mb-3" style="font-size:3rem; color:#198754;">
                    <i class="bi bi-envelope-check-fill"></i>
                </div>
                <h6 class="fw-bold text-dark mb-2">Check your email</h6>
                <p class="text-muted small mb-4">We've sent a 6-digit token to your email address. Use it to reset your password.</p>
                <a href="{{ route('enterToken') }}" class="btn auth-btn-primary w-100">
                    <i class="bi bi-key me-2"></i>Enter Token
                </a>
            </div>

            <hr class="my-4">
            <p class="text-center text-muted small mb-0">
                Remember your password?
                <a href="{{ route('login') }}" style="color:#f5951b;" class="fw-semibold">Back to Sign In</a>
            </p>
        </div>
    </div>
</div>

<style>
    *, *::before, *::after { box-sizing: border-box; }
    body { margin: 0; font-family: 'Inter', system-ui, -apple-system, sans-serif; }

    .auth-bg { background: #f8f9fa; }

    .auth-card {
        background: #fff;
        border-radius: 16px;
        width: 100%;
        max-width: 440px;
        box-shadow: 0 4px 24px rgba(9,28,61,0.08);
        overflow: hidden;
    }

    .auth-card-top {
        background: #091c3d;
        color: #fff;
        padding: 2rem 2rem 1.5rem;
        text-align: center;
    }
    .auth-logo-sm {
        width: 56px; height: 56px;
        background: rgba(245,149,27,0.15);
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        color: #f5951b;
        margin-bottom: 1rem;
    }

    .auth-card-body { padding: 2rem; }

    .auth-input-icon { background: #f8f9fa; border-color: #dee2e6; color: #6c757d; }
    .auth-input      { border-color: #dee2e6; padding: 0.55rem 0.75rem; }
    .auth-input:focus {
        border-color: #f5951b;
        box-shadow: 0 0 0 3px rgba(245,149,27,0.12);
    }
    .input-group:focus-within .auth-input-icon { border-color: #f5951b; }

    .auth-btn-primary {
        background-color: #091c3d;
        color: #fff;
        border: none;
        padding: 0.65rem;
        border-radius: 8px;
        font-weight: 600;
        transition: background 0.2s;
    }
    .auth-btn-primary:hover { background-color: #0d2a5a; color: #fff; }
</style>

<script>
    function sendToken(e) {
        e.preventDefault();
        const btn    = document.getElementById('sendBtnText');
        const errMsg = document.getElementById('err');
        errMsg.classList.add('d-none');
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Sending…';

        const form = document.getElementById('resetForm');
        fetch("{{ route('reset.password') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json',
            },
            body: new FormData(form),
        })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'success') {
                form.classList.add('d-none');
                document.getElementById('successState').classList.remove('d-none');
            } else {
                errMsg.textContent = data.message;
                errMsg.classList.remove('d-none');
                btn.innerHTML = '<i class="bi bi-send me-2"></i>Send Reset Token';
            }
        })
        .catch(() => {
            errMsg.textContent = 'Something went wrong. Please try again.';
            errMsg.classList.remove('d-none');
            btn.innerHTML = '<i class="bi bi-send me-2"></i>Send Reset Token';
        });

        return false;
    }
</script>

@endsection
