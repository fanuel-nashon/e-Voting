@extends('layouts.app')

@section('title', 'Reset Password - e-Voting')

@section('content')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<div class="auth-bg d-flex align-items-center justify-content-center min-vh-100 p-3">
    <div class="auth-card">

        <!-- Header -->
        <div class="auth-card-top">
            <div class="auth-logo-sm">
                <i class="bi bi-key-fill"></i>
            </div>
            <h5 class="fw-bold mb-1" id="cardTitle">Enter Your Token</h5>
            <p class="mb-0 opacity-75 small" id="cardSubtitle">Enter the 6-digit code sent to your email.</p>

            <!-- Step indicator -->
            <div class="step-indicator mt-3">
                <div class="step active" id="step1Dot"><span>1</span></div>
                <div class="step-line" id="stepLine"></div>
                <div class="step" id="step2Dot"><span>2</span></div>
            </div>
        </div>

        <div class="auth-card-body">
            <form id="form" onsubmit="return changePassword(event)">
                @csrf

                <!-- Step 1: Token -->
                <div id="tokenSection">
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-dark">6-Digit Token</label>
                        <div class="input-group">
                            <span class="input-group-text auth-input-icon"><i class="bi bi-hash"></i></span>
                            <input type="text" name="token" id="token" class="form-control auth-input"
                                   placeholder="e.g. 482931" maxlength="6" pattern="\d{6}"
                                   inputmode="numeric" required>
                        </div>
                    </div>

                    <div id="err" class="alert alert-danger py-2 small d-none mb-3"></div>

                    <button type="submit" class="btn auth-btn-primary w-100">
                        <span id="verifyBtnText"><i class="bi bi-check-circle me-2"></i>Verify Token</span>
                    </button>
                </div>

                <!-- Step 2: New password -->
                <div id="passwordSection" style="display:none;">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">New Password</label>
                        <div class="input-group">
                            <span class="input-group-text auth-input-icon"><i class="bi bi-lock"></i></span>
                            <input class="form-control auth-input" type="password" name="password"
                                   id="password" placeholder="Min. 6 characters" required minlength="6">
                            <button type="button" class="input-group-text auth-input-icon" style="cursor:pointer;"
                                    onclick="togglePwd('password', this)">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold text-dark">Confirm Password</label>
                        <div class="input-group">
                            <span class="input-group-text auth-input-icon"><i class="bi bi-lock-fill"></i></span>
                            <input class="form-control auth-input" type="password" name="password_confirmation"
                                   id="password_confirmation" placeholder="Repeat password" required>
                            <button type="button" class="input-group-text auth-input-icon" style="cursor:pointer;"
                                    onclick="togglePwd('password_confirmation', this)">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div id="passErr" class="alert alert-danger py-2 small d-none mb-3"></div>

                    <button type="submit" class="btn auth-btn-primary w-100">
                        <span id="resetBtnText"><i class="bi bi-shield-lock me-2"></i>Reset Password</span>
                    </button>
                </div>
            </form>

            <!-- Success state -->
            <div id="successState" class="d-none text-center py-2">
                <div class="mb-3" style="font-size:3rem; color:#198754;">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <h6 class="fw-bold text-dark mb-2">Password Reset!</h6>
                <p class="text-muted small mb-4">Your password has been updated. You can now sign in.</p>
                <a href="{{ route('login') }}" class="btn auth-btn-primary w-100">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Back to Sign In
                </a>
            </div>

            <hr class="my-4">
            <p class="text-center text-muted small mb-0">
                <a href="{{ route('login') }}" style="color:#f5951b;" class="fw-semibold">
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
        max-width: 440px;
        box-shadow: 0 4px 24px rgba(9,28,61,0.08);
        overflow: hidden;
    }

    .auth-card-top {
        background: #091c3d;
        color: #fff;
        padding: 2rem 2rem 1.75rem;
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

    /* Step indicator */
    .step-indicator {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0;
    }
    .step {
        width: 32px; height: 32px;
        border-radius: 50%;
        border: 2px solid rgba(255,255,255,0.3);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        font-weight: 600;
        color: rgba(255,255,255,0.5);
        transition: all 0.3s;
    }
    .step.active {
        border-color: #f5951b;
        background: #f5951b;
        color: #fff;
    }
    .step.done {
        border-color: #198754;
        background: #198754;
        color: #fff;
    }
    .step-line {
        width: 60px;
        height: 2px;
        background: rgba(255,255,255,0.2);
        margin: 0 6px;
        transition: background 0.3s;
    }
    .step-line.done { background: #198754; }

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

        const csrfToken = document.querySelector('input[name="_token"]').value;
        const btnText   = document.getElementById(inPasswordStep ? 'resetBtnText' : 'verifyBtnText');
        btnText.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Please wait…';

        fetch("{{ route('change.password') }}", {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            body: new FormData(document.getElementById('form')),
        })
        .then(async res => {
            const data = await res.json();
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
                // Advance to step 2
                inPasswordStep = true;
                document.getElementById('tokenSection').style.display = 'none';
                document.getElementById('passwordSection').style.display = 'block';

                // Update header & steps
                document.getElementById('cardTitle').textContent    = 'Set New Password';
                document.getElementById('cardSubtitle').textContent = 'Choose a strong password for your account.';
                document.getElementById('step1Dot').classList.remove('active');
                document.getElementById('step1Dot').classList.add('done');
                document.getElementById('stepLine').classList.add('done');
                document.getElementById('step2Dot').classList.add('active');

            } else if (data.status === 'password_reset') {
                document.getElementById('form').classList.add('d-none');
                document.getElementById('successState').classList.remove('d-none');

                // Mark both steps done
                document.getElementById('step1Dot').classList.add('done');
                document.getElementById('step2Dot').classList.remove('active');
                document.getElementById('step2Dot').classList.add('done');
                document.getElementById('stepLine').classList.add('done');

                setTimeout(() => window.location.href = "{{ route('login') }}", 2500);

            } else {
                showError(data.message || 'Something went wrong.');
                btnText.innerHTML = inPasswordStep
                    ? '<i class="bi bi-shield-lock me-2"></i>Reset Password'
                    : '<i class="bi bi-check-circle me-2"></i>Verify Token';
            }
        })
        .catch(err => {
            showError(err.message);
            const btnText = document.getElementById(inPasswordStep ? 'resetBtnText' : 'verifyBtnText');
            btnText.innerHTML = inPasswordStep
                ? '<i class="bi bi-shield-lock me-2"></i>Reset Password'
                : '<i class="bi bi-check-circle me-2"></i>Verify Token';
        });

        return false;
    }

    function togglePwd(fieldId, btn) {
        const field = document.getElementById(fieldId);
        const icon  = btn.querySelector('i');
        if (field.type === 'password') {
            field.type = 'text';
            icon.className = 'bi bi-eye-slash';
        } else {
            field.type = 'password';
            icon.className = 'bi bi-eye';
        }
    }
</script>

@endsection
