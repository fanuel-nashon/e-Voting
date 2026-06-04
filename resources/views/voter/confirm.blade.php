@extends('layouts.app')
@section('title', 'Review Your Ballot')
@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<div class="voter-layout">
  <aside class="voter-sidebar">
    <div>
      <div class="sidebar-brand">
        <i class="bi bi-shield-check" style="color:#f5951b;font-size:1.8rem;"></i>
        <span>e-Voting</span>
      </div>
      <hr class="sidebar-hr">
    </div>
    <div>
      <hr class="sidebar-hr">
      <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="sidebar-logout">
          <i class="bi bi-box-arrow-left text-danger me-2"></i> Sign Out
        </button>
      </form>
    </div>
  </aside>

  <div class="voter-main">
    <header class="voter-header">
      <div>
        <h5 class="fw-bold mb-0" style="color:#091c3d;">Review Your Ballot</h5>
        <small class="text-muted">Confirm your selections before submitting</small>
      </div>
      <a href="{{ route('voter.dashboard') }}" class="back-btn">
        <i class="bi bi-arrow-left me-1"></i> Go Back &amp; Change
      </a>
    </header>

    <main class="voter-content">

      <div class="review-card">
        <div class="review-header">
          <i class="bi bi-clipboard-check review-icon"></i>
          <div>
            <h5 class="fw-bold mb-1" style="color:#091c3d;">Ballot Summary</h5>
            <p class="text-muted small mb-0">Please review carefully. You <strong>cannot change your vote</strong> after confirming.</p>
          </div>
        </div>

        <div class="review-list">
          @foreach($reviewed as $item)
          <div class="review-row">
            <div class="review-position">
              <span class="review-type-chip">{{ str_replace('_',' ', ucfirst($item['position_type'])) }}</span>
              <span class="review-position-name">{{ $item['position_name'] }}</span>
            </div>
            <div class="review-candidate">
              <div class="review-avatar">
                @if($item['candidate_image'])
                <img src="{{ asset('storage/'.$item['candidate_image']) }}" alt="">
                @else
                <i class="bi bi-person-fill"></i>
                @endif
              </div>
              <span class="fw-semibold">{{ $item['candidate_name'] }}</span>
            </div>
          </div>
          @endforeach
        </div>

        <div class="review-footer">
          <div class="warning-note">
            <i class="bi bi-lock-fill me-2"></i>
            Your vote is secret and encrypted. Once submitted it cannot be changed.
          </div>
          <div class="review-actions">
            <a href="{{ route('voter.dashboard') }}" class="btn-back">
              <i class="bi bi-pencil me-1"></i>Change Selections
            </a>
            <form method="POST" action="{{ route('voter.confirm') }}" id="confirmForm">
              @csrf
              <button type="submit" class="btn-confirm" id="confirmBtn">
                <i class="bi bi-check-lg me-2"></i>Confirm &amp; Submit Ballot
              </button>
            </form>
          </div>
        </div>
      </div>

    </main>
  </div>
</div>

<style>
  *, *::before, *::after { box-sizing:border-box; }
  body { margin:0; font-family:'Inter',system-ui,sans-serif; background:#f8f9fa; }
  .voter-layout  { display:flex; min-height:100vh; }
  .voter-sidebar { width:230px; background:#091c3d; color:#fff; display:flex; flex-direction:column; justify-content:space-between; padding:1.5rem 1rem; position:fixed; top:0; left:0; min-height:100vh; }
  .sidebar-brand { display:flex; align-items:center; gap:.75rem; font-size:1.1rem; font-weight:700; padding:.5rem 0 1rem; }
  .sidebar-hr    { border-color:rgba(255,255,255,.15); }
  .sidebar-logout { background:none; border:none; color:rgba(255,255,255,.7); padding:.6rem .9rem; width:100%; text-align:left; border-radius:8px; font-size:.9rem; cursor:pointer; display:flex; align-items:center; }
  .voter-main    { margin-left:230px; flex:1; display:flex; flex-direction:column; }
  .voter-header  { background:#fff; border-bottom:1px solid #e9ecef; padding:1rem 2rem; display:flex; justify-content:space-between; align-items:center; position:sticky; top:0; z-index:50; box-shadow:0 1px 4px rgba(0,0,0,.05); }
  .voter-content { padding:2rem; }
  .back-btn { background:#f0f2f5; color:#091c3d; text-decoration:none; padding:.5rem 1rem; border-radius:8px; font-size:.88rem; font-weight:600; display:flex; align-items:center; }
  .back-btn:hover { background:#e2e6ea; }

  .review-card   { background:#fff; border-radius:16px; box-shadow:0 2px 16px rgba(0,0,0,.07); max-width:640px; margin:0 auto; overflow:hidden; }
  .review-header { display:flex; align-items:flex-start; gap:1rem; padding:1.75rem; border-bottom:1px solid #f0f2f5; }
  .review-icon   { font-size:2rem; color:#f5951b; flex-shrink:0; }

  .review-list   { padding:0 1.75rem; }
  .review-row    { display:flex; justify-content:space-between; align-items:center; padding:1rem 0; border-bottom:1px solid #f0f2f5; gap:1rem; flex-wrap:wrap; }
  .review-row:last-child { border-bottom:none; }
  .review-position { display:flex; flex-direction:column; gap:.3rem; }
  .review-type-chip { background:#e9ecef; color:#495057; font-size:.75rem; font-weight:600; padding:.2rem .65rem; border-radius:10px; width:fit-content; }
  .review-position-name { font-size:.88rem; color:#6c757d; }
  .review-candidate { display:flex; align-items:center; gap:.65rem; }
  .review-avatar { width:36px; height:36px; border-radius:50%; background:#f0f2f5; display:flex; align-items:center; justify-content:center; overflow:hidden; flex-shrink:0; }
  .review-avatar img { width:100%; height:100%; object-fit:cover; }
  .review-avatar i   { color:#6c757d; font-size:1rem; }

  .review-footer { padding:1.5rem 1.75rem; background:#f8f9fa; }
  .warning-note  { background:#fff8e1; border:1px solid #ffe082; border-radius:8px; padding:.75rem 1rem; font-size:.85rem; color:#7c5a00; margin-bottom:1.25rem; display:flex; align-items:center; }
  .review-actions { display:flex; gap:.75rem; justify-content:flex-end; flex-wrap:wrap; }

  .btn-back    { background:#f0f2f5; color:#091c3d; text-decoration:none; padding:.65rem 1.25rem; border-radius:8px; font-size:.9rem; font-weight:600; display:flex; align-items:center; border:none; cursor:pointer; }
  .btn-back:hover { background:#e2e6ea; }
  .btn-confirm { background:#091c3d; color:#fff; border:none; padding:.65rem 1.75rem; border-radius:8px; font-size:.95rem; font-weight:700; cursor:pointer; display:flex; align-items:center; transition:background .2s; }
  .btn-confirm:hover { background:#f5951b; }
  .btn-confirm:disabled { opacity:.6; cursor:not-allowed; }
</style>

<script>
  document.getElementById('confirmForm').addEventListener('submit', function() {
    const btn = document.getElementById('confirmBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Submitting…';
  });
</script>
@endsection
