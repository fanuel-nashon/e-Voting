@extends('layouts.app')
@section('title', 'My Ballot - e-Voting')
@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<div class="voter-layout">

  <!-- Sidebar -->
  <aside class="voter-sidebar">
    <div>
      <div class="sidebar-brand">
        <i class="bi bi-shield-check" style="color:#f5951b;font-size:1.8rem;"></i>
        <span>e-Voting</span>
      </div>
      <hr class="sidebar-hr">
      <nav class="sidebar-nav">
        <a href="{{ route('voter.dashboard') }}" class="sidebar-link active-link">
          <i class="bi bi-ballot me-2"></i> My Ballot
        </a>
      </nav>
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

  <!-- Main -->
  <div class="voter-main">

    <!-- Top bar -->
    <header class="voter-header">
      <div>
        <h5 class="fw-bold mb-0" style="color:#091c3d;">Student Voting Portal</h5>
        <small class="text-muted">{{ $election->title ?? 'Student Union Election' }}</small>
      </div>
      <div class="d-flex align-items-center gap-2">
        <div class="voter-avatar"><i class="bi bi-person-fill"></i></div>
        <div>
          <div class="fw-semibold small">{{ Auth::user()->name }}</div>
          @if(!isset($noProfile) && isset($student))
          <small class="text-muted">{{ $student->reg_no }} &middot; {{ $student->program->name ?? '' }}</small>
          @endif
        </div>
      </div>
    </header>

    <main class="voter-content">

      {{-- No profile --}}
      @if(isset($noProfile) && $noProfile)
      <div class="alert-box alert-warning-box">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        Your account has no student profile linked. Please contact the election administrator.
      </div>

      {{-- Election not yet open --}}
      @elseif(!$election->hasStarted())
      <div class="status-card">
        <div class="status-icon" style="background:rgba(245,149,27,.12);color:#f5951b;">
          <i class="bi bi-clock-history"></i>
        </div>
        <h5 class="fw-bold" style="color:#091c3d;">Voting has not started yet</h5>
        @if($election->voting_opens_at)
        <p class="text-muted">Opens on <strong>{{ $election->voting_opens_at->format('D, d M Y \a\t H:i') }}</strong></p>
        @else
        <p class="text-muted">The election timeline has not been set. Check back later.</p>
        @endif
      </div>

      {{-- Election ended --}}
      @elseif($election->hasEnded())
      <div class="status-card">
        <div class="status-icon" style="background:rgba(108,117,125,.12);color:#6c757d;">
          <i class="bi bi-lock-fill"></i>
        </div>
        <h5 class="fw-bold" style="color:#091c3d;">Voting has closed</h5>
        <p class="text-muted">The voting period ended on <strong>{{ $election->voting_closes_at->format('D, d M Y \a\t H:i') }}</strong>.</p>
        @if($election->resultsReleased())
        <p class="text-muted">Results have been released — check your email.</p>
        @endif
      </div>

      {{-- Fully voted --}}
      @elseif(count($voted) >= $groups->sum(fn($g) => $g['positions']->count()))
      <div class="status-card">
        <div class="status-icon" style="background:rgba(25,135,84,.12);color:#198754;">
          <i class="bi bi-check-circle-fill"></i>
        </div>
        <h5 class="fw-bold" style="color:#091c3d;">Your ballot has been submitted</h5>
        <p class="text-muted">Thank you for voting. Your votes have been recorded securely.</p>
        <a href="{{ route('voter.done') }}" class="voter-btn mt-2">View confirmation</a>
      </div>

      {{-- Active ballot --}}
      @else

      @if(session('error'))
      <div class="alert-box alert-danger-box mb-4"><i class="bi bi-x-circle me-2"></i>{{ session('error') }}</div>
      @endif

      <!-- Countdown banner -->
      <div class="countdown-banner">
        <i class="bi bi-hourglass-split me-2"></i>
        Voting closes: <strong>{{ $election->voting_closes_at->format('D, d M Y \a\t H:i') }}</strong>
        &nbsp;&nbsp;<span id="countdown" class="countdown-chip"></span>
      </div>

      <form method="POST" action="{{ route('voter.review') }}">
        @csrf

        @foreach($groups as $type => $group)
        <div class="ballot-section">
          <div class="ballot-section-header">
            <span class="ballot-type-badge">{{ $group['label'] }}</span>
          </div>

          @foreach($group['positions'] as $position)
          @php $alreadyVoted = in_array($position->id, $voted); @endphp

          <div class="position-card {{ $alreadyVoted ? 'position-voted' : '' }}">
            <div class="position-title">
              {{ $position->name }}
              @if($alreadyVoted)
              <span class="voted-badge"><i class="bi bi-check-circle-fill me-1"></i>Voted</span>
              @endif
            </div>

            @if($alreadyVoted)
            <p class="text-muted small mt-1 mb-0">You have already cast your vote for this position.</p>
            @else
            <div class="candidates-grid">
              @foreach($position->candidates as $candidate)
              <label class="candidate-card" for="vote_{{ $position->id }}_{{ $candidate->id }}">
                <input type="radio" name="votes[{{ $position->id }}]"
                       id="vote_{{ $position->id }}_{{ $candidate->id }}"
                       value="{{ $candidate->id }}" required>
                <div class="candidate-avatar">
                  @if($candidate->image)
                  <img src="{{ asset('storage/'.$candidate->image) }}" alt="{{ $candidate->name }}">
                  @else
                  <i class="bi bi-person-fill"></i>
                  @endif
                </div>
                <span class="candidate-name">{{ $candidate->name }}</span>
                <div class="candidate-check"><i class="bi bi-check-lg"></i></div>
              </label>
              @endforeach
            </div>
            @endif
          </div>
          @endforeach
        </div>
        @endforeach

        <div class="d-flex justify-content-end mt-4">
          <button type="submit" class="voter-btn voter-btn-lg">
            <i class="bi bi-eye me-2"></i>Review My Ballot
          </button>
        </div>
      </form>
      @endif

    </main>
  </div>
</div>

<style>
  *, *::before, *::after { box-sizing: border-box; }
  body { margin:0; font-family:'Inter',system-ui,sans-serif; background:#f8f9fa; }

  .voter-layout { display:flex; min-height:100vh; }

  /* Sidebar */
  .voter-sidebar {
    width:230px; min-height:100vh; background:#091c3d; color:#fff;
    display:flex; flex-direction:column; justify-content:space-between;
    padding:1.5rem 1rem; position:fixed; top:0; left:0; z-index:100;
  }
  .sidebar-brand { display:flex; align-items:center; gap:.75rem; font-size:1.1rem; font-weight:700; padding:.5rem 0 1rem; }
  .sidebar-hr    { border-color:rgba(255,255,255,.15); }
  .sidebar-nav   { display:flex; flex-direction:column; gap:.35rem; }
  .sidebar-link  { color:rgba(255,255,255,.75); text-decoration:none; padding:.6rem .9rem; border-radius:8px; font-size:.9rem; display:flex; align-items:center; }
  .sidebar-link:hover, .active-link { background:#f5951b; color:#fff !important; font-weight:600; }
  .sidebar-logout { background:none; border:none; color:rgba(255,255,255,.7); padding:.6rem .9rem; width:100%; text-align:left; border-radius:8px; font-size:.9rem; cursor:pointer; display:flex; align-items:center; }
  .sidebar-logout:hover { background:rgba(220,53,69,.15); }

  /* Main */
  .voter-main    { margin-left:230px; flex:1; display:flex; flex-direction:column; }
  .voter-header  { background:#fff; border-bottom:1px solid #e9ecef; padding:1rem 2rem; display:flex; justify-content:space-between; align-items:center; position:sticky; top:0; z-index:50; box-shadow:0 1px 4px rgba(0,0,0,.05); }
  .voter-avatar  { width:36px; height:36px; background:#f0f2f5; border-radius:50%; display:flex; align-items:center; justify-content:center; color:#6c757d; }
  .voter-content { padding:2rem; flex:1; }

  /* Status / alert cards */
  .status-card   { background:#fff; border-radius:16px; padding:3rem; text-align:center; box-shadow:0 2px 12px rgba(0,0,0,.06); max-width:480px; margin:3rem auto; }
  .status-icon   { width:72px; height:72px; border-radius:50%; display:inline-flex; align-items:center; justify-content:center; font-size:2rem; margin-bottom:1.25rem; }
  .alert-box     { padding:.85rem 1.1rem; border-radius:10px; font-size:.9rem; margin-bottom:1rem; }
  .alert-warning-box { background:#fff8e1; border:1px solid #ffe082; color:#7c5a00; }
  .alert-danger-box  { background:#fff5f5; border:1px solid #feb2b2; color:#c53030; }

  /* Countdown */
  .countdown-banner { background:#091c3d; color:#fff; padding:.75rem 1.25rem; border-radius:10px; font-size:.88rem; margin-bottom:1.5rem; display:flex; align-items:center; flex-wrap:wrap; gap:.5rem; }
  .countdown-chip   { background:#f5951b; color:#fff; border-radius:6px; padding:.15rem .6rem; font-weight:700; font-size:.85rem; }

  /* Ballot */
  .ballot-section        { margin-bottom:2rem; }
  .ballot-section-header { margin-bottom:1rem; }
  .ballot-type-badge     { background:#091c3d; color:#fff; padding:.35rem 1rem; border-radius:20px; font-size:.82rem; font-weight:600; letter-spacing:.02em; }

  .position-card  { background:#fff; border-radius:12px; padding:1.25rem 1.5rem; margin-bottom:1rem; box-shadow:0 1px 6px rgba(0,0,0,.06); }
  .position-voted { opacity:.65; }
  .position-title { font-weight:700; color:#091c3d; font-size:.95rem; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:.5rem; }
  .voted-badge    { background:#e8f5e9; color:#2e7d32; border-radius:20px; padding:.2rem .75rem; font-size:.78rem; font-weight:600; }

  .candidates-grid { display:flex; flex-wrap:wrap; gap:.75rem; margin-top:1rem; }
  .candidate-card  {
    position:relative; cursor:pointer; border:2px solid #e9ecef; border-radius:12px;
    padding:.85rem 1rem; display:flex; align-items:center; gap:.75rem; min-width:180px;
    flex:1; transition:border-color .15s, box-shadow .15s;
  }
  .candidate-card:hover      { border-color:#091c3d; }
  .candidate-card input      { position:absolute; opacity:0; width:0; height:0; }
  .candidate-card input:checked ~ .candidate-check { opacity:1; }
  .candidate-card:has(input:checked) { border-color:#091c3d; background:#f0f4ff; box-shadow:0 0 0 3px rgba(9,28,61,.1); }

  .candidate-avatar { width:40px; height:40px; border-radius:50%; background:#f0f2f5; display:flex; align-items:center; justify-content:center; overflow:hidden; flex-shrink:0; }
  .candidate-avatar img { width:100%; height:100%; object-fit:cover; }
  .candidate-avatar i   { font-size:1.2rem; color:#6c757d; }
  .candidate-name  { font-size:.9rem; font-weight:600; color:#1a202c; flex:1; }
  .candidate-check { position:absolute; top:.5rem; right:.5rem; background:#091c3d; color:#fff; border-radius:50%; width:20px; height:20px; display:flex; align-items:center; justify-content:center; font-size:.7rem; opacity:0; transition:opacity .15s; }

  /* Buttons */
  .voter-btn    { background:#091c3d; color:#fff; border:none; padding:.6rem 1.4rem; border-radius:8px; font-weight:600; font-size:.9rem; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; transition:background .2s; }
  .voter-btn:hover { background:#0d2a5a; color:#fff; }
  .voter-btn-lg { padding:.75rem 2rem; font-size:1rem; }
</style>

<script>
  // Countdown timer
  (function() {
    const closes = new Date("{{ $election->voting_closes_at?->toIso8601String() ?? '' }}");
    const el = document.getElementById('countdown');
    if (!el || !closes || isNaN(closes)) return;

    function tick() {
      const diff = closes - Date.now();
      if (diff <= 0) { el.textContent = 'Closed'; return; }
      const h = Math.floor(diff / 3600000);
      const m = Math.floor((diff % 3600000) / 60000);
      const s = Math.floor((diff % 60000) / 1000);
      el.textContent = `${h}h ${String(m).padStart(2,'0')}m ${String(s).padStart(2,'0')}s remaining`;
    }
    tick();
    setInterval(tick, 1000);
  })();
</script>
@endsection
