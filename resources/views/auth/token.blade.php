@extends('layouts.app')

@section('title', 'Check Your Email - e-Voting')

@section('content')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<div class="auth-bg d-flex align-items-center justify-content-center min-vh-100 p-3">
    <div class="auth-card">

        <div class="auth-card-top">
            <div class="auth-logo-sm">
                <i class="bi bi-envelope-open"></i>
            </div>
            <h5 class="fw-bold mb-1">Check your email</h5>
            <p class="mb-0 opacity-75 small">A 6-digit token has been sent to your email address.</p>
        </div>

        <div class="auth-card-body text-center">
            <p class="text-muted small mb-4">
                Open your inbox and copy the token, then click the button below to set your new password.
            </p>

            <a href="{{ route('enterToken') }}" class="btn auth-btn-primary w-100 mb-3">
                <i class="bi bi-key me-2"></i>Enter Token
            </a>

            <hr class="my-3">
            <p class="text-muted small mb-0">
                Didn't receive it?
                <a href="{{ route('password.reset') }}" style="color:#f5951b;" class="fw-semibold">Resend token</a>
            </p>
            <p class="text-muted small mt-2 mb-0">
                <a href="{{ route('login') }}" style="color:#091c3d;" class="fw-semibold">
                    <i class="bi bi-arrow-left me-1"></i>Back to Sign In
                </a>
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
        max-width: 400px;
        box-shadow: 0 4px 24px rgba(9,28,61,0.08);
        overflow: hidden;
    }
    .auth-card-top {
        background: #091c3d;
        color: #fff;
        padding: 2rem;
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

@endsection
