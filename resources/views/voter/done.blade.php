@extends('layouts.app')
@section('title', 'Vote Submitted')
@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<div class="done-bg">
  <div class="done-card">
    <div class="done-icon-wrap">
      <i class="bi bi-check-circle-fill"></i>
    </div>
    <h3 class="fw-bold mt-3 mb-1" style="color:#091c3d;">Vote Submitted!</h3>
    <p class="text-muted mb-4">Your ballot has been recorded securely. Thank you for participating in the <strong>{{ $election->title ?? 'Student Union Election' }}</strong>.</p>

    <div class="done-info-box">
      <i class="bi bi-shield-lock-fill me-2"></i>
      Your vote is anonymous and encrypted. No one can link your identity to your selections.
    </div>

    @if($election->voting_closes_at)
    <p class="text-muted small mt-4 mb-0">
      Voting closes: <strong>{{ $election->voting_closes_at->format('D, d M Y \a\t H:i') }}</strong>
    </p>
    <p class="text-muted small">Results will be communicated via email after the Election Commission reviews them.</p>
    @endif

    <form action="{{ route('logout') }}" method="POST" class="mt-4">
      @csrf
      <button type="submit" class="done-btn">
        <i class="bi bi-box-arrow-left me-2"></i>Sign Out
      </button>
    </form>
  </div>
</div>

<style>
  body { margin:0; font-family:'Inter',system-ui,sans-serif; }
  .done-bg   { min-height:100vh; background:#091c3d; display:flex; align-items:center; justify-content:center; padding:2rem; }
  .done-card { background:#fff; border-radius:20px; padding:3rem 2.5rem; max-width:460px; width:100%; text-align:center; box-shadow:0 20px 60px rgba(0,0,0,.3); }
  .done-icon-wrap { width:80px; height:80px; background:#e8f5e9; border-radius:50%; display:inline-flex; align-items:center; justify-content:center; font-size:2.5rem; color:#198754; }
  .done-info-box { background:#f0f4ff; border:1px solid #c7d4f7; border-radius:10px; padding:.85rem 1rem; font-size:.85rem; color:#091c3d; display:flex; align-items:flex-start; text-align:left; gap:.5rem; }
  .done-btn  { background:#091c3d; color:#fff; border:none; padding:.75rem 2rem; border-radius:10px; font-weight:600; font-size:.95rem; cursor:pointer; transition:background .2s; }
  .done-btn:hover { background:#0d2a5a; }
</style>
@endsection
