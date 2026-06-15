@extends('layouts.app')
@section('title', 'Election Control Centre')
@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<div class="container-fluid p-0 d-flex min-vh-100 flex-column flex-md-row">

  <!-- Sidebar -->
  <aside class="text-white d-flex flex-column justify-content-between p-3 position-fixed top-0 start-0 h-100 shadow"
         style="width:260px;background:#091c3d;z-index:1030;">
    <div class="w-100">
      <div class="d-flex align-items-center mb-4 px-2 pt-2">
        <i class="bi bi-shield-check fs-3 me-2" style="color:#f5951b;"></i>
        <span class="fs-5 fw-bold">Election Centre</span>
      </div>
      <hr class="opacity-25 mb-4">
      <ul class="nav flex-column gap-2 w-100">
        <li><a href="{{ route('dashboard') }}" class="nav-link text-white d-flex align-items-center px-3 py-2 rounded-3 hover-link"><i class="bi bi-speedometer2 me-3 fs-5"></i>Admin Console</a></li>
        <li><a href="{{ route('election.dashboard') }}" class="nav-link text-white d-flex align-items-center px-3 py-2 rounded-3 active-link"><i class="bi bi-broadcast me-3 fs-5"></i>Election Dashboard</a></li>
        <li><a href="{{ route('users.index') }}" class="nav-link text-white d-flex align-items-center px-3 py-2 rounded-3 hover-link"><i class="bi bi-people me-3 fs-5"></i>Users / Voters</a></li>
        <li><a href="{{ route('reports.index') }}" class="nav-link text-white d-flex align-items-center px-3 py-2 rounded-3 hover-link"><i class="bi bi-bar-chart-line me-3 fs-5"></i>Reports</a></li>
      </ul>
    </div>
    <div class="w-100 pb-2">
      <hr class="opacity-25">
      <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-link text-white text-decoration-none d-flex align-items-center px-3 py-2 w-100 rounded-3 hover-logout">
          <i class="bi bi-box-arrow-left me-3 fs-5 text-danger"></i>Sign Out
        </button>
      </form>
    </div>
  </aside>

  <!-- Main -->
  <div class="flex-grow-1 d-flex flex-column" style="margin-left:260px;background:#f8f9fa;">

    <nav class="navbar bg-white border-bottom px-4 py-3 sticky-top shadow-sm">
      <span class="fw-bold" style="color:#091c3d;">Election Control Centre</span>
      <div class="ms-auto d-flex align-items-center gap-2">
        <span class="badge rounded-pill px-3 py-2" id="electionStatusBadge"
              style="background:{{ $election->isOpen() ? '#198754' : ($election->hasEnded() ? '#6c757d' : '#f5951b') }};color:#fff;font-size:.8rem;">
          {{ $election->isOpen() ? 'LIVE' : ($election->hasEnded() ? 'ENDED' : 'NOT STARTED') }}
        </span>
        <div class="fw-semibold small text-dark">{{ Auth::user()->name }}</div>
      </div>
    </nav>

    <main class="p-4">

      <!-- Stats row -->
      <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3">
          <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
            <div class="text-primary mb-2"><i class="bi bi-check2-circle fs-4"></i></div>
            <div class="small text-muted fw-semibold">Total Votes Cast</div>
            <h3 class="fw-bold m-0" id="statTotalVotes" style="color:#091c3d;">{{ $stats['total_votes'] }}</h3>
          </div>
        </div>
        <div class="col-6 col-xl-3">
          <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
            <div class="text-success mb-2"><i class="bi bi-people fs-4"></i></div>
            <div class="small text-muted fw-semibold">Registered Voters</div>
            <h3 class="fw-bold m-0" style="color:#091c3d;">{{ $stats['total_voters'] }}</h3>
          </div>
        </div>
        <div class="col-6 col-xl-3">
          <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
            <div class="text-warning mb-2"><i class="bi bi-percent fs-4"></i></div>
            <div class="small text-muted fw-semibold">Participation Rate</div>
            <h3 class="fw-bold m-0" id="statParticipation" style="color:#091c3d;">{{ $stats['participation'] }}%</h3>
          </div>
        </div>
        <div class="col-6 col-xl-3">
          <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
            <div class="text-danger mb-2"><i class="bi bi-list-task fs-4"></i></div>
            <div class="small text-muted fw-semibold">Positions</div>
            <h3 class="fw-bold m-0" style="color:#091c3d;">{{ $stats['positions'] }}</h3>
          </div>
        </div>
      </div>

      <div class="row g-4">

        <!-- Timeline settings -->
        <div class="col-12 col-xl-5">
          <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
            <h5 class="fw-bold mb-4" style="color:#091c3d;"><i class="bi bi-calendar-event me-2" style="color:#f5951b;"></i>Election Timeline</h5>
            <form id="timelineForm" onsubmit="saveTimeline(event)">
              @csrf
              <div class="mb-3">
                <label class="form-label fw-semibold small text-dark">Voting Opens</label>
                <input type="datetime-local" class="form-control" id="votingOpens" name="voting_opens_at"
                       value="{{ $election->voting_opens_at?->format('Y-m-d\TH:i') }}">
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold small text-dark">Voting Closes</label>
                <input type="datetime-local" class="form-control" id="votingCloses" name="voting_closes_at"
                       value="{{ $election->voting_closes_at?->format('Y-m-d\TH:i') }}">
              </div>
              <div class="mb-4">
                <label class="form-label fw-semibold small text-dark">Acceptance Deadline</label>
                <input type="datetime-local" class="form-control" id="acceptanceDeadline" name="acceptance_deadline_at"
                       value="{{ $election->acceptance_deadline_at?->format('Y-m-d\TH:i') }}">
              </div>
              <p class="text-danger small d-none" id="timelineErr"></p>
              <button type="submit" class="btn w-100 text-white fw-semibold" style="background:#091c3d;">
                <span id="timelineBtnText"><i class="bi bi-save me-2"></i>Save Timeline</span>
              </button>
            </form>

            @if($election->hasEnded())
            <hr class="my-4">
            <h6 class="fw-bold mb-3" style="color:#091c3d;">Results &amp; Publication</h6>
            @if(!$election->resultsReleased())
            <button class="btn text-white w-100 mb-2 fw-semibold" style="background:#198754;" onclick="releaseResults()">
              <i class="bi bi-send me-2"></i>Release Results &amp; Email Candidates
            </button>
            @else
            <div class="alert py-2 small" style="background:#e8f5e9;border:1px solid #a5d6a7;color:#1b5e20;">
              <i class="bi bi-check-circle-fill me-1"></i> Results released on {{ $election->results_released_at->format('d M Y H:i') }}
            </div>
            <button class="btn text-white w-100 fw-semibold" style="background:#0d6efd;" onclick="publishResults()">
              <i class="bi bi-envelope-paper me-2"></i>Send Results to All Voters
            </button>
            @endif
            @endif

            <div id="actionMsg" class="mt-3 small d-none"></div>
          </div>
        </div>

        <!-- Live vote log -->
        <div class="col-12 col-xl-7">
          <div class="card border-0 shadow-sm rounded-4 p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h5 class="fw-bold mb-0" style="color:#091c3d;"><i class="bi bi-activity me-2" style="color:#f5951b;"></i>Live Activity Log</h5>
              <span class="badge rounded-pill" style="background:#198754;color:#fff;font-size:.75rem;" id="liveDot">● LIVE</span>
            </div>
            <div class="table-responsive" style="max-height:380px;overflow-y:auto;" id="logScroll">
              <table class="table table-sm align-middle mb-0" style="font-size:.83rem;">
                <thead class="table-light sticky-top">
                  <tr>
                    <th class="py-2">Time</th>
                    <th>Voter ID</th>
                    <th>Faculty</th>
                    <th>Position</th>
                    <th>IP</th>
                  </tr>
                </thead>
                <tbody id="logTbody">
                  <tr><td colspan="5" class="text-center text-muted py-3">Loading logs…</td></tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Pending Voter Registrations -->
        <div class="col-12">
          <div class="card border-0 shadow-sm rounded-4 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
              <h5 class="fw-bold mb-0" style="color:#091c3d;">
                <i class="bi bi-person-plus me-2" style="color:#f5951b;"></i>
                Pending Voter Registrations
                <span class="badge rounded-pill ms-2" style="background:#dc3545;color:#fff;font-size:.75rem;" id="pendingCount">{{ $pendingRegistrations->count() }}</span>
              </h5>
              <button class="btn btn-sm btn-outline-secondary rounded-3" onclick="refreshRegistrations()">
                <i class="bi bi-arrow-clockwise me-1"></i>Refresh
              </button>
            </div>
            <div class="table-responsive">
              <table class="table table-hover align-middle border-0">
                <thead class="table-light">
                  <tr>
                    <th class="py-3 px-4">Photo</th>
                    <th class="py-3 px-4">Name</th>
                    <th class="py-3 px-4">Reg Number</th>
                    <th class="py-3 px-4">Programme</th>
                    <th class="py-3 px-4">Faculty</th>
                    <th class="py-3 px-4">Submitted</th>
                    <th class="py-3 px-4">Actions</th>
                  </tr>
                </thead>
                <tbody id="pendingRegTbody">
                  @forelse($pendingRegistrations as $reg)
                  <tr id="reg-row-{{ $reg->id }}">
                    <td class="px-4">
                      @if($reg->photo)
                      <img src="{{ asset('storage/'.$reg->photo) }}" style="width:40px;height:40px;border-radius:50%;object-fit:cover;border:2px solid #e9ecef;">
                      @else
                      <div style="width:40px;height:40px;border-radius:50%;background:#e9ecef;display:flex;align-items:center;justify-content:center;"><i class="bi bi-person-fill text-secondary"></i></div>
                      @endif
                    </td>
                    <td class="px-4 fw-semibold text-dark">{{ $reg->name }}</td>
                    <td class="px-4 text-muted small">{{ $reg->reg_number }}</td>
                    <td class="px-4 small">{{ $reg->program->name }}</td>
                    <td class="px-4 small text-muted">{{ $reg->faculty->name }}</td>
                    <td class="px-4 small text-muted">{{ $reg->created_at->format('d M Y') }}</td>
                    <td class="px-4">
                      <button class="btn btn-sm btn-success rounded-3 me-1"
                              onclick="approveReg({{ $reg->id }}, this)">
                        <i class="bi bi-check-lg me-1"></i>Approve
                      </button>
                      <button class="btn btn-sm btn-outline-danger rounded-3"
                              onclick="rejectReg({{ $reg->id }}, this)">
                        <i class="bi bi-x-lg me-1"></i>Reject
                      </button>
                    </td>
                  </tr>
                  @empty
                  <tr id="noRegRow"><td colspan="7" class="text-center text-muted py-4">No pending registrations.</td></tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Candidate acceptances -->
        @if($acceptances->isNotEmpty())
        <div class="col-12">
          <div class="card border-0 shadow-sm rounded-4 p-4">
            <h5 class="fw-bold mb-4" style="color:#091c3d;"><i class="bi bi-person-check me-2" style="color:#f5951b;"></i>Candidate Acceptances</h5>
            <div class="table-responsive">
              <table class="table table-hover align-middle border-0">
                <thead class="table-light">
                  <tr>
                    <th class="py-3 px-4">Candidate</th>
                    <th class="py-3 px-4">Position</th>
                    <th class="py-3 px-4">Votes</th>
                    <th class="py-3 px-4">Outcome</th>
                    <th class="py-3 px-4">Response</th>
                    <th class="py-3 px-4">Verified</th>
                    <th class="py-3 px-4">Action</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($acceptances as $a)
                  <tr id="acc-row-{{ $a->id }}">
                    <td class="px-4 fw-semibold text-dark">{{ $a->candidate->name }}</td>
                    <td class="px-4 text-muted small">{{ $a->position->name }}</td>
                    <td class="px-4">{{ $a->votes_received }}</td>
                    <td class="px-4">
                      <span class="badge rounded-pill px-3" style="{{ $a->won ? 'background:#198754' : 'background:#6c757d' }};color:#fff;">
                        {{ $a->won ? 'Winner' : 'Runner-up' }}
                      </span>
                    </td>
                    <td class="px-4">
                      @if(!$a->responded_at)
                        <span class="badge bg-warning text-dark rounded-pill">Pending</span>
                      @elseif($a->accepted)
                        <span class="badge bg-success rounded-pill">Accepted</span>
                      @else
                        <span class="badge bg-secondary rounded-pill">Declined</span>
                      @endif
                    </td>
                    <td class="px-4">
                      @if($a->verified_at)
                        <span class="badge bg-primary rounded-pill">Verified</span>
                      @else
                        <span class="text-muted small">—</span>
                      @endif
                    </td>
                    <td class="px-4">
                      @if($a->responded_at && !$a->verified_at)
                      <button class="btn btn-sm btn-outline-primary rounded-3"
                              onclick="verifyAcceptance({{ $a->id }}, this)">
                        <i class="bi bi-patch-check me-1"></i>Verify
                      </button>
                      @endif
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
        @endif

      </div>
    </main>
  </div>
</div>

<style>
  body { font-family:'Inter',system-ui,sans-serif; background:#f8f9fa; }
  .active-link  { background:#f5951b !important; font-weight:600; }
  .hover-link:hover { background:rgba(255,255,255,.08); }
  .hover-logout:hover { background:rgba(220,53,69,.1); }
</style>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';
let lastLogId = 0;

// ── Live log polling ───────────────────────────────────────────────────────
function fetchLogs() {
  fetch(`{{ route('election.logs') }}?after=${lastLogId}`, {
    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
  })
  .then(r => r.json())
  .then(data => {
    if (data.logs && data.logs.length) {
      const tbody = document.getElementById('logTbody');
      if (tbody.querySelector('td[colspan]')) tbody.innerHTML = '';

      data.logs.forEach(log => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td class="text-muted">${new Date(log.created_at).toLocaleTimeString()}</td>
          <td><code style="font-size:.78rem;color:#091c3d;">${log.voter_hash.substring(0,12)}…</code></td>
          <td>${log.faculty_name || '—'}</td>
          <td><span class="badge bg-light text-dark border">${log.position_name}</span></td>
          <td class="text-muted">${log.ip_prefix || '—'}</td>
        `;
        tbody.insertBefore(tr, tbody.firstChild);
      });
      lastLogId = data.last_id;

      // Auto-scroll to top
      const scroll = document.getElementById('logScroll');
      scroll.scrollTop = 0;
    }
  })
  .catch(() => {});
}

// ── Live stats polling ─────────────────────────────────────────────────────
function fetchStats() {
  fetch(`{{ route('election.stats') }}`, { headers: { 'Accept': 'application/json' } })
  .then(r => r.json())
  .then(data => {
    document.getElementById('statTotalVotes').textContent   = data.total_votes;
    document.getElementById('statParticipation').textContent = data.participation + '%';
  })
  .catch(() => {});
}

fetchLogs();
setInterval(fetchLogs, 5000);
setInterval(fetchStats, 10000);

// ── Timeline save ──────────────────────────────────────────────────────────
function saveTimeline(e) {
  e.preventDefault();
  const btn = document.getElementById('timelineBtnText');
  const err = document.getElementById('timelineErr');
  err.classList.add('d-none');
  btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving…';

  fetch("{{ route('election.timeline') }}", {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
    body: JSON.stringify({
      voting_opens_at:        document.getElementById('votingOpens').value,
      voting_closes_at:       document.getElementById('votingCloses').value,
      acceptance_deadline_at: document.getElementById('acceptanceDeadline').value || null,
    })
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      btn.innerHTML = '<i class="bi bi-check-lg me-2"></i>Saved!';
      setTimeout(() => { btn.innerHTML = '<i class="bi bi-save me-2"></i>Save Timeline'; }, 2500);
    } else {
      err.textContent = data.message || 'Error saving.';
      err.classList.remove('d-none');
      btn.innerHTML = '<i class="bi bi-save me-2"></i>Save Timeline';
    }
  })
  .catch(() => { btn.innerHTML = '<i class="bi bi-save me-2"></i>Save Timeline'; });
}

// ── Release results ────────────────────────────────────────────────────────
function releaseResults() {
  if (!confirm('Release results and email all candidates now?')) return;
  showAction('Releasing results…');
  fetch("{{ route('election.release') }}", {
    method: 'POST',
    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' }
  })
  .then(r => r.json())
  .then(data => { showAction(data.message, data.success); if (data.success) setTimeout(() => location.reload(), 2000); })
  .catch(() => showAction('Error. Please try again.', false));
}

// ── Publish results ────────────────────────────────────────────────────────
function publishResults() {
  if (!confirm('Email final results to all voters now?')) return;
  showAction('Sending voter emails…');
  fetch("{{ route('election.publish') }}", {
    method: 'POST',
    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' }
  })
  .then(r => r.json())
  .then(data => showAction(data.message, data.success))
  .catch(() => showAction('Error. Please try again.', false));
}

// ── Verify acceptance ──────────────────────────────────────────────────────
function verifyAcceptance(id, btn) {
  btn.disabled = true;
  fetch(`/election/acceptances/${id}/verify`, {
    method: 'POST',
    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' }
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      const cell = btn.closest('td');
      cell.innerHTML = '<span class="badge bg-primary rounded-pill">Verified</span>';
    }
  });
}

// ── Voter registration approvals ──────────────────────────────────────────
function approveReg(id, btn) {
  if (!confirm('Approve this voter registration and send their login credentials?')) return;
  btn.disabled = true;
  btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

  fetch(`/voter-registrations/${id}/approve`, {
    method: 'POST',
    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' }
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      document.getElementById(`reg-row-${id}`).remove();
      updatePendingCount(-1);
      showAction(data.message, true);
    } else {
      btn.disabled = false;
      btn.innerHTML = '<i class="bi bi-check-lg me-1"></i>Approve';
      alert(data.message);
    }
  });
}

function rejectReg(id, btn) {
  const reason = prompt('Reason for rejection (optional):');
  if (reason === null) return;
  btn.disabled = true;

  fetch(`/voter-registrations/${id}/reject`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
    body: JSON.stringify({ reason })
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      document.getElementById(`reg-row-${id}`).remove();
      updatePendingCount(-1);
      showAction(data.message, true);
    } else {
      btn.disabled = false;
      alert(data.message);
    }
  });
}

function updatePendingCount(delta) {
  const el = document.getElementById('pendingCount');
  const n = Math.max(0, parseInt(el.textContent) + delta);
  el.textContent = n;
  if (n === 0) {
    const tbody = document.getElementById('pendingRegTbody');
    if (!tbody.querySelector('tr')) {
      tbody.innerHTML = '<tr id="noRegRow"><td colspan="7" class="text-center text-muted py-4">No pending registrations.</td></tr>';
    }
  }
}

function refreshRegistrations() { location.reload(); }

function showAction(msg, success = null) {
  const el = document.getElementById('actionMsg');
  el.textContent = msg;
  el.className = `mt-3 small ${success === null ? 'text-muted' : (success ? 'text-success' : 'text-danger')}`;
  el.classList.remove('d-none');
}
</script>
@endsection
