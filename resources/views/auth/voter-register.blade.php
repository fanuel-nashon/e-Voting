@extends('layouts.app')
@section('title', 'Voter Registration — Mzumbe University')
@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<div class="auth-bg d-flex align-items-center justify-content-center min-vh-100 p-3">
  <div class="auth-card" style="max-width:520px;">

    <div class="auth-card-top">
      <div class="auth-logo-sm"><i class="bi bi-person-plus-fill"></i></div>
      <h5 class="fw-bold mb-1">Voter Registration</h5>
      <p class="mb-0 opacity-75 small">Mzumbe University — Student Union Elections</p>
    </div>

    <div class="auth-card-body">

      {{-- Success state --}}
      <div id="successState" class="d-none text-center py-2">
        <div style="font-size:3rem;color:#198754;margin-bottom:1rem;"><i class="bi bi-check-circle-fill"></i></div>
        <h6 class="fw-bold text-dark mb-2">Registration Submitted!</h6>
        <p class="text-muted small mb-3">Your application is pending approval from your Faculty Election Admin. Once approved, your login credentials will be sent to the email below.</p>
        <div class="p-3 rounded-3 small" style="background:#f0f4ff;color:#091c3d;line-height:1.8;">
          <div><strong>Your account email:</strong></div>
          <div id="assignedEmail" class="fw-bold" style="color:#f5951b;"></div>
          <div class="mt-2 text-muted">This is also where your OTP codes and election notifications will be sent.</div>
        </div>
        <a href="{{ route('login') }}" class="btn auth-btn-primary w-100 mt-4">Back to Sign In</a>
      </div>

      {{-- Registration form --}}
      <form id="regForm" onsubmit="submitRegistration(event)" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
          <label class="form-label fw-semibold text-dark small">Full Name</label>
          <div class="input-group">
            <span class="input-group-text auth-input-icon"><i class="bi bi-person"></i></span>
            <input class="form-control auth-input" type="text" name="name" id="regName"
                   placeholder="e.g. John Doe" required>
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label fw-semibold text-dark small">Email Address</label>
          <div class="input-group">
            <span class="input-group-text auth-input-icon"><i class="bi bi-envelope"></i></span>
            <input class="form-control auth-input" type="email" name="email" id="regEmail"
                   placeholder="e.g. john.doe22@mustudent.ac.tz" required>
          </div>
          <div class="form-text">Must be firstname.lastname + last 2 digits of your enrolment year, e.g. <strong>john.doe22@mustudent.ac.tz</strong>. This is where your login credentials, OTP codes, and election notifications will be sent.</div>
        </div>

        <div class="mb-3">
          <label class="form-label fw-semibold text-dark small">Registration Number</label>
          <div class="input-group">
            <span class="input-group-text auth-input-icon"><i class="bi bi-hash"></i></span>
            <input class="form-control auth-input" type="text" name="reg_number" id="regNumber"
                   placeholder="e.g. MZ/ICT/2022/001" required>
          </div>
          <div class="form-text">Must contain your enrolment year (e.g. 2022).</div>
        </div>

        <div class="mb-3">
          <label class="form-label fw-semibold text-dark small">Programme</label>
          <div class="input-group">
            <span class="input-group-text auth-input-icon"><i class="bi bi-journal-bookmark"></i></span>
            <select class="form-control auth-input" name="program_id" id="programSelect" required
                    onchange="autoFillFaculty(this)">
              <option value="">— Select your programme —</option>
              @foreach($programs as $prog)
              <option value="{{ $prog->id }}" data-faculty="{{ $prog->faculty->name }}">
                {{ $prog->name }} ({{ $prog->faculty->name }})
              </option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label fw-semibold text-dark small">Faculty</label>
          <div class="input-group">
            <span class="input-group-text auth-input-icon"><i class="bi bi-building"></i></span>
            <input class="form-control auth-input bg-light" type="text" id="facultyDisplay"
                   placeholder="Auto-filled from programme" readonly>
          </div>
        </div>

        <div class="mb-4">
          <label class="form-label fw-semibold text-dark small">
            Passport Photo <span class="text-muted fw-normal">(JPG/PNG, max 2 MB)</span>
          </label>
          <div id="photoDropZone" class="photo-drop-zone" onclick="document.getElementById('photoInput').click()">
            <div id="photoPreviewWrap" class="d-none">
              <img id="photoPreview" src="" alt="Preview" class="photo-preview-img">
            </div>
            <div id="photoPlaceholder">
              <i class="bi bi-camera fs-2 text-muted"></i>
              <div class="small text-muted mt-1">Click to upload photo</div>
            </div>
          </div>
          <input type="file" id="photoInput" name="photo" accept="image/jpeg,image/png"
                 class="d-none" onchange="previewPhoto(this)" required>
        </div>

        <div id="regErr" class="d-none mb-3 small p-2 rounded-3" style="background:#fff5f5;border:1px solid #feb2b2;color:#c53030;"></div>

        <button type="submit" class="btn auth-btn-primary w-100">
          <span id="regBtnText"><i class="bi bi-send me-2"></i>Submit Registration</span>
        </button>
      </form>

      <hr class="my-4">
      <p class="text-center text-muted small mb-0">
        Already registered? <a href="{{ route('login') }}" style="color:#f5951b;" class="fw-semibold">Sign In</a>
      </p>
    </div>
  </div>
</div>

<style>
  *, *::before, *::after { box-sizing:border-box; }
  body { margin:0; font-family:'Inter',system-ui,sans-serif; }
  .auth-bg { background:#f8f9fa; }
  .auth-card { background:#fff; border-radius:16px; width:100%; box-shadow:0 4px 24px rgba(9,28,61,.08); overflow:hidden; }
  .auth-card-top { background:#091c3d; color:#fff; padding:2rem; text-align:center; }
  .auth-logo-sm { width:56px; height:56px; background:rgba(245,149,27,.15); border-radius:14px; display:inline-flex; align-items:center; justify-content:center; font-size:1.75rem; color:#f5951b; margin-bottom:1rem; }
  .auth-card-body { padding:2rem; }
  .auth-input-icon { background:#f8f9fa; border-color:#dee2e6; color:#6c757d; }
  .auth-input { border-color:#dee2e6; padding:.55rem .75rem; }
  .auth-input:focus { border-color:#f5951b; box-shadow:0 0 0 3px rgba(245,149,27,.12); }
  .input-group:focus-within .auth-input-icon { border-color:#f5951b; }
  .auth-btn-primary { background:#091c3d; color:#fff; border:none; padding:.65rem; border-radius:8px; font-weight:600; transition:background .2s; }
  .auth-btn-primary:hover { background:#0d2a5a; color:#fff; }

  .photo-drop-zone { border:2px dashed #dee2e6; border-radius:12px; padding:1.5rem; text-align:center; cursor:pointer; transition:border-color .2s; min-height:120px; display:flex; align-items:center; justify-content:center; }
  .photo-drop-zone:hover { border-color:#f5951b; }
  .photo-preview-img { width:90px; height:90px; border-radius:50%; object-fit:cover; border:3px solid #091c3d; }
</style>

<script>
  function autoFillFaculty(sel) {
    const opt = sel.options[sel.selectedIndex];
    document.getElementById('facultyDisplay').value = opt.dataset.faculty || '';
  }

  function previewPhoto(input) {
    if (!input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
      document.getElementById('photoPreview').src = e.target.result;
      document.getElementById('photoPreviewWrap').classList.remove('d-none');
      document.getElementById('photoPlaceholder').classList.add('d-none');
    };
    reader.readAsDataURL(input.files[0]);
  }

  function submitRegistration(e) {
    e.preventDefault();
    const err = document.getElementById('regErr');
    const btn = document.getElementById('regBtnText');
    err.classList.add('d-none');
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Submitting…';

    const form = document.getElementById('regForm');
    fetch("{{ route('voter.register.submit') }}", {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value, 'Accept': 'application/json' },
      body: new FormData(form),
    })
    .then(async r => {
      const data = await r.json();
      if (!r.ok) {
        if (r.status === 422 && data.errors) {
          const first = Object.values(data.errors)[0];
          throw new Error(Array.isArray(first) ? first[0] : first);
        }
        throw new Error(data.message || 'Submission failed.');
      }
      return data;
    })
    .then(data => {
      if (data.success) {
        document.getElementById('assignedEmail').textContent = data.email;
        form.classList.add('d-none');
        document.getElementById('successState').classList.remove('d-none');
      }
    })
    .catch(err2 => {
      err.textContent = err2.message;
      err.classList.remove('d-none');
      btn.innerHTML = '<i class="bi bi-send me-2"></i>Submit Registration';
    });
  }
</script>
@endsection
