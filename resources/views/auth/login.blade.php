@extends('layouts.app')

@section('title', 'Sign In - e-Voting')

@section('content')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<div class="login-bg">

    <!-- Floating decorative circles -->
    <div class="deco deco-1"></div>
    <div class="deco deco-2"></div>
    <div class="deco deco-3"></div>

    <div class="login-card">

        <!-- Brand -->
        <div class="login-brand">
            <div class="login-icon-wrap">
                <i class="bi bi-shield-check"></i>
            </div>
            <h4 class="login-title">e-Voting System</h4>
            <p class="login-subtitle">Sign in to your account</p>
        </div>

        <!-- Form -->
        <form id="loginForm" onsubmit="return checkLogin(event)">
            @csrf

            <div class="mb-3">
                <label class="login-label" for="email">Email Address</label>
                <div class="login-input-wrap">
                    <i class="bi bi-envelope login-input-icon"></i>
                    <input class="login-input" type="email" name="email" id="email"
                           placeholder="you@example.com" required autocomplete="email">
                </div>
            </div>

            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label class="login-label mb-0" for="password">Password</label>
                    <a href="#" id="resetLink" onclick="return goReset(event)" class="login-forgot">Forgot password?</a>
                </div>
                <div class="login-input-wrap">
                    <i class="bi bi-lock login-input-icon"></i>
                    <input class="login-input" type="password" name="password" id="password"
                           placeholder="••••••••" required autocomplete="current-password">
                    <button type="button" class="login-eye" onclick="togglePassword(this)">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>

            <div id="loginError" class="login-error d-none"></div>

            <p class="text-center small mb-3" style="color:rgba(255,255,255,.6);">
              New student?
              <a href="{{ route('voter.register') }}" style="color:#f5951b;font-weight:600;">Register to vote</a>
            </p>

            <button id="loginBtn" type="submit" class="login-btn">
                <span id="loginBtnText">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                </span>
            </button>
        </form>

    </div>
</div>

<style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Inter', system-ui, -apple-system, sans-serif; }

    .login-bg {
        min-height: 100vh;
        background: #091c3d;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1.5rem;
        position: relative;
        overflow: hidden;
    }

    /* Decorative blobs */
    .deco {
        position: absolute;
        border-radius: 50%;
        pointer-events: none;
    }
    .deco-1 {
        width: 420px; height: 420px;
        top: -140px; right: -100px;
        background: rgba(245,149,27,0.07);
    }
    .deco-2 {
        width: 300px; height: 300px;
        bottom: -100px; left: -80px;
        background: rgba(255,255,255,0.03);
    }
    .deco-3 {
        width: 180px; height: 180px;
        top: 50%; left: 5%;
        transform: translateY(-50%);
        background: rgba(245,149,27,0.04);
    }

    /* Card */
    .login-card {
        background: #ffffff;
        border-radius: 20px;
        padding: 2.5rem 2.25rem;
        width: 100%;
        max-width: 420px;
        position: relative;
        z-index: 1;
        box-shadow: 0 24px 60px rgba(0,0,0,0.35);
    }

    /* Brand block */
    .login-brand   { text-align: center; margin-bottom: 2rem; }
    .login-icon-wrap {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 64px; height: 64px;
        background: #091c3d;
        border-radius: 16px;
        font-size: 1.9rem;
        color: #f5951b;
        margin-bottom: 1rem;
        box-shadow: 0 4px 16px rgba(9,28,61,0.25);
    }
    .login-title    { font-size: 1.35rem; font-weight: 700; color: #091c3d; margin-bottom: 0.2rem; }
    .login-subtitle { font-size: 0.85rem; color: #6c757d; }

    /* Labels */
    .login-label { font-size: 0.875rem; font-weight: 600; color: #333; display: block; margin-bottom: 0.35rem; }

    /* Inputs */
    .login-input-wrap {
        position: relative;
        display: flex;
        align-items: center;
    }
    .login-input-icon {
        position: absolute;
        left: 0.85rem;
        color: #adb5bd;
        font-size: 0.95rem;
        pointer-events: none;
        z-index: 1;
    }
    .login-input {
        width: 100%;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        padding: 0.65rem 2.75rem 0.65rem 2.5rem;
        font-size: 0.9rem;
        color: #1a202c;
        background: #f9fafb;
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .login-input:focus {
        border-color: #f5951b;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(245,149,27,0.15);
    }
    .login-input::placeholder { color: #c4c9d4; }

    /* Eye toggle */
    .login-eye {
        position: absolute;
        right: 0.75rem;
        background: none;
        border: none;
        color: #adb5bd;
        cursor: pointer;
        padding: 0.2rem;
        font-size: 1rem;
        line-height: 1;
    }
    .login-eye:hover { color: #091c3d; }

    /* Forgot link */
    .login-forgot { font-size: 0.8rem; color: #f5951b; font-weight: 600; text-decoration: none; }
    .login-forgot:hover { text-decoration: underline; }

    /* Error */
    .login-error {
        background: #fff5f5;
        border: 1px solid #feb2b2;
        color: #c53030;
        border-radius: 8px;
        padding: 0.6rem 0.85rem;
        font-size: 0.85rem;
        margin-bottom: 1rem;
    }

    /* Submit button */
    .login-btn {
        width: 100%;
        background: #091c3d;
        color: #fff;
        border: none;
        border-radius: 10px;
        padding: 0.75rem;
        font-size: 0.95rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s, transform 0.1s;
        letter-spacing: 0.01em;
    }
    .login-btn:hover  { background: #0d2a5a; }
    .login-btn:active { transform: scale(0.99); background: #f5951b; }
</style>

<script>
    function checkLogin(event) {
        event.preventDefault();
        const err = document.getElementById('loginError');
        const btn = document.getElementById('loginBtnText');
        err.classList.add('d-none');
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Signing in…';

        fetch("{{ route('login.submit') }}", {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value },
            body: new FormData(document.getElementById('loginForm')),
        })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'success' || data.status === 'otp_required') {
                window.location.href = data.redirect;
            } else {
                err.textContent = data.message;
                err.classList.remove('d-none');
                btn.innerHTML = '<i class="bi bi-box-arrow-in-right me-2"></i>Sign In';
            }
        })
        .catch(() => {
            err.textContent = 'Something went wrong. Please try again.';
            err.classList.remove('d-none');
            btn.innerHTML = '<i class="bi bi-box-arrow-in-right me-2"></i>Sign In';
        });

        return false;
    }

    function goReset(event) {
        event.preventDefault();
        document.getElementById('resetLink').textContent = 'Redirecting…';
        window.location.href = "{{ route('password.reset') }}";
    }

    function togglePassword(btn) {
        const input = btn.closest('.login-input-wrap').querySelector('.login-input');
        const icon  = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'bi bi-eye-slash';
        } else {
            input.type = 'password';
            icon.className = 'bi bi-eye';
        }
    }

    window.addEventListener('pageshow', () => {
        const link = document.getElementById('resetLink');
        if (link) link.textContent = 'Forgot password?';
    });
</script>

@endsection
