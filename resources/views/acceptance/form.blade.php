@extends('layouts.app')
@section('title', 'Election Result Acceptance')
@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<div class="acc-bg">
  <div class="acc-card">

    <div class="acc-header">
      <i class="bi bi-shield-check acc-logo-icon"></i>
      <h5 class="fw-bold mb-1">Election Result Acceptance</h5>
      <p class="mb-0 opacity-75 small">Student Union Election</p>
    </div>

    <div class="acc-body">

      @if(isset($alreadyResponded) && $alreadyResponded)
      {{-- Already responded --}}
      <div class="acc-status-icon" style="background:#e8f5e9;color:#198754;">
        <i class="bi bi-check-circle-fill"></i>
      </div>
      <h6 class="fw-bold text-center mb-2">Response Already Submitted</h6>
      <p class="text-muted text-center small">You submitted your response on <strong>{{ $acceptance->responded_at->format('d M Y H:i') }}</strong>.</p>
      <div class="acc-result-box {{ $acceptance->won ? 'acc-won' : '' }}">
        <div><strong>Position:</strong> {{ $acceptance->position->name }}</div>
        <div><strong>Votes received:</strong> {{ $acceptance->votes_received }}</div>
        <div><strong>Your response:</strong> {{ $acceptance->accepted ? 'Accepted' : 'Declined' }}</div>
      </div>

      @elseif(isset($justSubmitted) && $justSubmitted)
      {{-- Just submitted --}}
      <div class="acc-status-icon" style="background:#e8f5e9;color:#198754;">
        <i class="bi bi-check-circle-fill"></i>
      </div>
      <h6 class="fw-bold text-center mb-2">Response Recorded</h6>
      <p class="text-muted text-center small">Thank you, <strong>{{ $acceptance->candidate->name }}</strong>. Your response has been submitted to the Election Commission.</p>
      <div class="acc-result-box {{ $acceptance->won ? 'acc-won' : '' }}">
        <div><strong>Position:</strong> {{ $acceptance->position->name }}</div>
        <div><strong>Your response:</strong> {{ $acceptance->accepted ? '✅ Accepted' : '❌ Declined' }}</div>
      </div>

      @elseif(isset($expired) && $expired)
      {{-- Deadline passed --}}
      <div class="acc-status-icon" style="background:#fff8e1;color:#f57f17;">
        <i class="bi bi-hourglass-bottom"></i>
      </div>
      <h6 class="fw-bold text-center mb-2">Acceptance Deadline Passed</h6>
      <p class="text-muted text-center small">The deadline for responding has passed. Please contact the Election Commission directly.</p>

      @else
      {{-- Active form --}}
      <div class="acc-candidate-info">
        <div class="acc-avatar"><i class="bi bi-person-fill"></i></div>
        <div>
          <div class="fw-bold">{{ $acceptance->candidate->name }}</div>
          <div class="text-muted small">{{ $acceptance->position->name }}</div>
        </div>
        <span class="ms-auto badge rounded-pill px-3 py-2"
              style="{{ $acceptance->won ? 'background:#198754' : 'background:#6c757d' }};color:#fff;">
          {{ $acceptance->won ? '🏆 Winner' : 'Runner-up' }}
        </span>
      </div>

      <div class="acc-result-box {{ $acceptance->won ? 'acc-won' : '' }} mb-4">
        <div><strong>Votes received:</strong> {{ $acceptance->votes_received }}</div>
        @if($acceptance->won)
        <div class="mt-1 small" style="color:#1b5e20;">You have been elected to this position. Please indicate whether you accept.</div>
        @else
        <div class="mt-1 small text-muted">Thank you for contesting. Please acknowledge this result.</div>
        @endif
      </div>

      @if(session('error'))
      <div class="alert py-2 small" style="background:#fff5f5;border:1px solid #feb2b2;color:#c53030;border-radius:8px;">
        {{ session('error') }}
      </div>
      @endif

      <form method="POST" action="{{ route('acceptance.submit', $acceptance->token) }}">
        @csrf
        <div class="mb-3">
          <label class="form-label fw-semibold small">Your Decision</label>
          <div class="d-flex gap-3">
            <label class="acc-radio-card" for="accept_yes">
              <input type="radio" name="accepted" id="accept_yes" value="1" required>
              <i class="bi bi-check-circle-fill" style="color:#198754;font-size:1.3rem;"></i>
              <span>{{ $acceptance->won ? 'Accept Position' : 'Acknowledge' }}</span>
            </label>
            @if($acceptance->won)
            <label class="acc-radio-card" for="accept_no">
              <input type="radio" name="accepted" id="accept_no" value="0">
              <i class="bi bi-x-circle-fill" style="color:#dc3545;font-size:1.3rem;"></i>
              <span>Decline Position</span>
            </label>
            @endif
          </div>
        </div>
        <div class="mb-4">
          <label class="form-label fw-semibold small">Note <span class="text-muted fw-normal">(optional)</span></label>
          <textarea class="form-control" name="response_note" rows="3"
                    placeholder="Add a brief message to the Election Commission…"></textarea>
        </div>
        <button type="submit" class="acc-submit-btn">
          <i class="bi bi-send me-2"></i>Submit Response
        </button>
      </form>
      @endif

    </div>
  </div>
</div>

<style>
  *, *::before, *::after { box-sizing:border-box; }
  body { margin:0; font-family:'Inter',system-ui,sans-serif; }
  .acc-bg   { min-height:100vh; background:#f8f9fa; display:flex; align-items:center; justify-content:center; padding:1.5rem; }
  .acc-card { background:#fff; border-radius:16px; width:100%; max-width:480px; box-shadow:0 4px 24px rgba(9,28,61,.08); overflow:hidden; }
  .acc-header { background:#091c3d; color:#fff; padding:2rem; text-align:center; }
  .acc-logo-icon { font-size:2rem; color:#f5951b; display:block; margin-bottom:.75rem; }
  .acc-body   { padding:2rem; }

  .acc-status-icon { width:64px; height:64px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:2rem; margin:0 auto 1rem; }

  .acc-candidate-info { display:flex; align-items:center; gap:.75rem; padding:1rem; background:#f8f9fa; border-radius:10px; margin-bottom:1rem; flex-wrap:wrap; }
  .acc-avatar { width:40px; height:40px; border-radius:50%; background:#e9ecef; display:flex; align-items:center; justify-content:center; font-size:1.2rem; color:#6c757d; flex-shrink:0; }

  .acc-result-box { background:#f8f9fa; border-left:4px solid #6c757d; padding:.85rem 1rem; border-radius:4px; font-size:.9rem; }
  .acc-won        { border-color:#198754; background:#f0fff4; }

  .acc-radio-card { flex:1; border:2px solid #e9ecef; border-radius:10px; padding:.85rem; display:flex; flex-direction:column; align-items:center; gap:.4rem; cursor:pointer; text-align:center; font-size:.88rem; font-weight:600; transition:border-color .15s; }
  .acc-radio-card input { display:none; }
  .acc-radio-card:has(input:checked) { border-color:#091c3d; background:#f0f4ff; }

  .acc-submit-btn { width:100%; background:#091c3d; color:#fff; border:none; padding:.75rem; border-radius:8px; font-weight:600; font-size:.95rem; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:background .2s; }
  .acc-submit-btn:hover { background:#f5951b; }
</style>
@endsection
