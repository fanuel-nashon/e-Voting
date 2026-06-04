@extends('layouts.app')
@section('title', 'Enter OTP — e-Voting')
@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<div class="auth-bg d-flex align-items-center justify-content-center min-vh-100 p-3">
  <div class="auth-card">

    <div class="auth-card-top">
      <div class="auth-logo-sm"><i class="bi bi-shield-lock-fill"></i></div>
      <h5 class="fw-bold mb-1">Two-Factor Verification</h5>
      <p class="mb-0 opacity-75 small">Enter the 6-digit OTP sent to your email</p>
    </div>

    <div class="auth-card-body">
      <p class="text-muted small text-center mb-4">
        <i class="bi bi-envelope-check me-1" style="color:#f5951b;"></i>
        Check your registered email inbox. The code expires in <strong>10 minutes</strong>.
      </p>

      <form id="otpForm" onsubmit="verifyOtp(event)">
        @csrf
        <div class="mb-4">
          <label class="form-label fw-semibold text-dark small text-center d-block">One-Time Password</label>
          <div class="otp-input-row" id="otpInputs">
            @for($i = 0; $i < 6; $i++)
            <input class="otp-digit" type="text" maxlength="1" pattern="[0-9]"
                   inputmode="numeric" autocomplete="one-time-code">
            @endfor
          </div>
          <input type="hidden" name="otp" id="otpHidden">
        </div>

        <div id="otpErr" class="d-none mb-3 small p-2 rounded-3 text-center"
             style="background:#fff5f5;border:1px solid #feb2b2;color:#c53030;"></div>

        <button type="submit" class="btn auth-btn-primary w-100" id="otpBtn">
          <span id="otpBtnText"><i class="bi bi-check-circle me-2"></i>Verify &amp; Sign In</span>
        </button>
      </form>

      <hr class="my-4">
      <p class="text-center text-muted small mb-0">
        Didn't receive it?
        <a href="{{ route('login') }}" style="color:#f5951b;" class="fw-semibold">Go back and try again</a>
      </p>
    </div>
  </div>
</div>

<style>
  *, *::before, *::after { box-sizing:border-box; }
  body { margin:0; font-family:'Inter',system-ui,sans-serif; }
  .auth-bg { background:#f8f9fa; }
  .auth-card { background:#fff; border-radius:16px; width:100%; max-width:400px; box-shadow:0 4px 24px rgba(9,28,61,.08); overflow:hidden; }
  .auth-card-top { background:#091c3d; color:#fff; padding:2rem; text-align:center; }
  .auth-logo-sm { width:56px; height:56px; background:rgba(245,149,27,.15); border-radius:14px; display:inline-flex; align-items:center; justify-content:center; font-size:1.75rem; color:#f5951b; margin-bottom:1rem; }
  .auth-card-body { padding:2rem; }
  .auth-btn-primary { background:#091c3d; color:#fff; border:none; padding:.65rem; border-radius:8px; font-weight:600; transition:background .2s; }
  .auth-btn-primary:hover { background:#0d2a5a; color:#fff; }

  .otp-input-row { display:flex; gap:.5rem; justify-content:center; }
  .otp-digit {
    width:48px; height:56px; border:2px solid #dee2e6; border-radius:10px;
    text-align:center; font-size:1.5rem; font-weight:700; color:#091c3d;
    outline:none; transition:border-color .15s;
  }
  .otp-digit:focus { border-color:#f5951b; box-shadow:0 0 0 3px rgba(245,149,27,.15); }
</style>

<script>
  // OTP digit navigation
  const digits = document.querySelectorAll('.otp-digit');
  digits.forEach((input, idx) => {
    input.addEventListener('input', () => {
      input.value = input.value.replace(/\D/, '');
      if (input.value && idx < digits.length - 1) digits[idx + 1].focus();
      syncHidden();
    });
    input.addEventListener('keydown', e => {
      if (e.key === 'Backspace' && !input.value && idx > 0) digits[idx - 1].focus();
    });
    input.addEventListener('paste', e => {
      const text = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '').slice(0, 6);
      [...text].forEach((ch, i) => { if (digits[i]) digits[i].value = ch; });
      syncHidden();
      e.preventDefault();
    });
  });

  function syncHidden() {
    document.getElementById('otpHidden').value = [...digits].map(d => d.value).join('');
  }

  function verifyOtp(e) {
    e.preventDefault();
    syncHidden();
    const otp = document.getElementById('otpHidden').value;
    const err = document.getElementById('otpErr');
    const btn = document.getElementById('otpBtnText');
    err.classList.add('d-none');

    if (otp.length < 6) {
      err.textContent = 'Please enter all 6 digits.';
      err.classList.remove('d-none');
      return;
    }

    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Verifying…';

    fetch("{{ route('voter.otp.verify') }}", {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
      },
      body: JSON.stringify({ otp }),
    })
    .then(r => r.json())
    .then(data => {
      if (data.status === 'success') {
        window.location.href = data.redirect;
      } else {
        err.textContent = data.message;
        err.classList.remove('d-none');
        btn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Verify &amp; Sign In';
        digits.forEach(d => d.value = '');
        digits[0].focus();
      }
    })
    .catch(() => {
      err.textContent = 'Something went wrong. Please try again.';
      err.classList.remove('d-none');
      btn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Verify &amp; Sign In';
    });
  }

  // Auto-focus first digit
  digits[0].focus();
</script>
@endsection
