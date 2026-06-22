@extends('layouts.app')
@section('title', 'Voting Reports')
@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<div class="container-fluid p-0 d-flex min-vh-100 flex-column flex-md-row">

  {{-- ── Sidebar ───────────────────────────────────────────────────────────── --}}
  <aside class="text-white d-flex flex-column justify-content-between p-3 position-fixed top-0 start-0 h-100 shadow"
         style="width:260px;background:#091c3d;z-index:1030;">
    <div class="w-100">
      <div class="d-flex align-items-center mb-4 px-2 pt-2">
        <i class="bi bi-shield-check fs-3 me-2" style="color:#f5951b;"></i>
        <span class="fs-5 fw-bold">e-Voting Admin</span>
      </div>
      <hr class="opacity-25 mb-4">
      <ul class="nav flex-column gap-2 w-100">
        @if(auth()->user()->hasRole('admin'))
        <li><a href="{{ route('dashboard') }}"         class="nav-link text-white d-flex align-items-center px-3 py-2 rounded-3 hover-link"><i class="bi bi-speedometer2 me-3 fs-5"></i>Admin Console</a></li>
        @endif
        <li><a href="{{ route('election.dashboard') }}" class="nav-link text-white d-flex align-items-center px-3 py-2 rounded-3 hover-link"><i class="bi bi-broadcast me-3 fs-5"></i>Election Dashboard</a></li>
        @if(auth()->user()->hasRole('admin'))
        <li><a href="{{ route('users.index') }}"       class="nav-link text-white d-flex align-items-center px-3 py-2 rounded-3 hover-link"><i class="bi bi-people me-3 fs-5"></i>Users / Voters</a></li>
        @endif
        <li><a href="{{ route('reports.index') }}"     class="nav-link text-white d-flex align-items-center px-3 py-2 rounded-3 active-link"><i class="bi bi-bar-chart-line me-3 fs-5"></i>Reports</a></li>
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

  {{-- ── Main ─────────────────────────────────────────────────────────────── --}}
  <div class="flex-grow-1 d-flex flex-column" style="margin-left:260px;background:#f8f9fa;">

    {{-- Navbar --}}
    <nav class="navbar bg-white border-bottom px-4 py-3 sticky-top shadow-sm">
      <span class="fw-bold" style="color:#091c3d;"><i class="bi bi-bar-chart-line me-2" style="color:#f5951b;"></i>Voting Reports</span>
      <div class="ms-auto d-flex align-items-center gap-3">
        @if($election->title)
          <span class="text-muted small fw-semibold">{{ $election->title }}</span>
        @endif
        <span class="badge rounded-pill px-3 py-2"
              style="background:{{ $election->isOpen() ? '#198754' : ($election->hasEnded() ? '#6c757d' : '#f5951b') }};color:#fff;font-size:.8rem;">
          {{ $election->isOpen() ? 'LIVE' : ($election->hasEnded() ? 'ENDED' : 'NOT STARTED') }}
        </span>
        <span class="fw-semibold small text-dark">{{ Auth::user()->name }}</span>
      </div>
    </nav>

    <main class="p-4">

      {{-- ── Top stat cards ──────────────────────────────────────────────── --}}
      <div class="row g-3 mb-4">
        <div class="col-6 col-xl-2">
          <div class="card border-0 shadow-sm rounded-4 p-3 h-100 text-center">
            <div class="text-primary mb-1"><i class="bi bi-people fs-3"></i></div>
            <div class="small text-muted fw-semibold">Registered Voters</div>
            <h3 class="fw-bold m-0" style="color:#091c3d;">{{ number_format($totalVoters) }}</h3>
          </div>
        </div>
        <div class="col-6 col-xl-2">
          <div class="card border-0 shadow-sm rounded-4 p-3 h-100 text-center">
            <div class="text-success mb-1"><i class="bi bi-check2-circle fs-3"></i></div>
            <div class="small text-muted fw-semibold">Voters Who Voted</div>
            <h3 class="fw-bold m-0" style="color:#091c3d;">{{ number_format($totalVoted) }}</h3>
          </div>
        </div>
        <div class="col-6 col-xl-2">
          <div class="card border-0 shadow-sm rounded-4 p-3 h-100 text-center">
            <div class="text-danger mb-1"><i class="bi bi-person-x fs-3"></i></div>
            <div class="small text-muted fw-semibold">Did Not Vote</div>
            <h3 class="fw-bold m-0" style="color:#091c3d;">{{ number_format($notVoted) }}</h3>
          </div>
        </div>
        <div class="col-6 col-xl-2">
          <div class="card border-0 shadow-sm rounded-4 p-3 h-100 text-center">
            <div class="text-warning mb-1"><i class="bi bi-ballot fs-3"></i></div>
            <div class="small text-muted fw-semibold">Total Votes Cast</div>
            <h3 class="fw-bold m-0" style="color:#091c3d;">{{ number_format($totalVotes) }}</h3>
          </div>
        </div>
        <div class="col-6 col-xl-2">
          <div class="card border-0 shadow-sm rounded-4 p-3 h-100 text-center">
            <div class="mb-1" style="color:#f5951b;"><i class="bi bi-percent fs-3"></i></div>
            <div class="small text-muted fw-semibold">Participation Rate</div>
            <h3 class="fw-bold m-0" style="color:#091c3d;">{{ $participation }}%</h3>
          </div>
        </div>
        <div class="col-6 col-xl-2">
          <div class="card border-0 shadow-sm rounded-4 p-3 h-100 text-center">
            <div class="text-info mb-1"><i class="bi bi-person-badge fs-3"></i></div>
            <div class="small text-muted fw-semibold">Candidates / Positions</div>
            <h3 class="fw-bold m-0" style="color:#091c3d;">{{ $totalCandidates }} / {{ $totalPositions }}</h3>
          </div>
        </div>
      </div>

      {{-- ── Tab navigation ───────────────────────────────────────────────── --}}
      <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white border-bottom px-4 pt-3 pb-0">
          <ul class="nav nav-tabs border-0" id="reportTabs" role="tablist">
            <li class="nav-item" role="presentation">
              <a class="nav-link active fw-semibold" id="overview-tab" data-toggle="tab" href="#overview" role="tab" style="color:#091c3d;">
                <i class="bi bi-grid-3x3-gap me-1"></i>Overview
              </a>
            </li>
            <li class="nav-item" role="presentation">
              <a class="nav-link fw-semibold" id="positions-tab" data-toggle="tab" href="#tab-positions" role="tab" style="color:#091c3d;">
                <i class="bi bi-list-task me-1"></i>By Position
                <span class="badge ml-1 rounded-pill" style="background:#091c3d;color:#fff;font-size:.7rem;">{{ $positions->count() }}</span>
              </a>
            </li>
            <li class="nav-item" role="presentation">
              <a class="nav-link fw-semibold" id="faculties-tab" data-toggle="tab" href="#tab-faculties" role="tab" style="color:#091c3d;">
                <i class="bi bi-building me-1"></i>By Faculty
                <span class="badge ml-1 rounded-pill" style="background:#091c3d;color:#fff;font-size:.7rem;">{{ $faculties->count() }}</span>
              </a>
            </li>
            <li class="nav-item" role="presentation">
              <a class="nav-link fw-semibold" id="candidates-tab" data-toggle="tab" href="#tab-candidates" role="tab" style="color:#091c3d;">
                <i class="bi bi-person-lines-fill me-1"></i>All Candidates
                <span class="badge ml-1 rounded-pill" style="background:#091c3d;color:#fff;font-size:.7rem;">{{ $candidates->count() }}</span>
              </a>
            </li>
          </ul>
        </div>

        <div class="tab-content p-4" id="reportTabContent">

          {{-- ═══════════════════════════════════════════════════════════════ --}}
          {{-- TAB 1 — OVERVIEW                                               --}}
          {{-- ═══════════════════════════════════════════════════════════════ --}}
          <div class="tab-pane fade show active" id="overview" role="tabpanel">

            <div class="row g-4 mb-4">

              {{-- Participation donut --}}
              <div class="col-12 col-lg-4">
                <div class="card border-0 bg-light rounded-4 p-3 h-100">
                  <h6 class="fw-bold mb-3" style="color:#091c3d;"><i class="bi bi-pie-chart me-2" style="color:#f5951b;"></i>Voter Participation</h6>
                  <div style="max-height:240px;" class="d-flex align-items-center justify-content-center">
                    <canvas id="participationChart"></canvas>
                  </div>
                  <div class="mt-3 d-flex justify-content-center gap-4 small">
                    <span><span class="me-1" style="color:#198754;">●</span>Voted ({{ $totalVoted }})</span>
                    <span><span class="me-1" style="color:#dee2e6;">●</span>Not voted ({{ $notVoted }})</span>
                  </div>
                </div>
              </div>

              {{-- Faculty participation bars --}}
              <div class="col-12 col-lg-8">
                <div class="card border-0 bg-light rounded-4 p-3 h-100">
                  <h6 class="fw-bold mb-3" style="color:#091c3d;"><i class="bi bi-building me-2" style="color:#f5951b;"></i>Participation by Faculty</h6>
                  @forelse($faculties as $f)
                    @if($f['total_voters'] > 0)
                    <div class="mb-3">
                      <div class="d-flex justify-content-between small fw-semibold mb-1">
                        <span>{{ $f['name'] }}</span>
                        <span>{{ $f['voted_count'] }} / {{ $f['total_voters'] }} &nbsp;<span class="text-muted">({{ $f['participation'] }}%)</span></span>
                      </div>
                      <div class="progress rounded-pill" style="height:10px;">
                        <div class="progress-bar" role="progressbar"
                             style="width:{{ $f['participation'] }}%;background:{{ $f['participation'] >= 75 ? '#198754' : ($f['participation'] >= 50 ? '#f5951b' : '#dc3545') }};"
                             aria-valuenow="{{ $f['participation'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                      </div>
                    </div>
                    @endif
                  @empty
                    <p class="text-muted small">No faculty data available.</p>
                  @endforelse
                </div>
              </div>

            </div>

            <div class="row g-4 mb-4">

              {{-- Vote timeline chart --}}
              <div class="col-12 col-lg-7">
                <div class="card border-0 bg-light rounded-4 p-3">
                  <h6 class="fw-bold mb-3" style="color:#091c3d;"><i class="bi bi-graph-up-arrow me-2" style="color:#f5951b;"></i>Voting Activity Over Time</h6>
                  @if($votingActivity->isEmpty())
                    <div class="text-center text-muted py-4 small"><i class="bi bi-clock-history fs-2 d-block mb-2 opacity-25"></i>No voting activity recorded yet.</div>
                  @else
                    <canvas id="timelineChart" height="160"></canvas>
                  @endif
                </div>
              </div>

              {{-- Votes by program --}}
              <div class="col-12 col-lg-5">
                <div class="card border-0 bg-light rounded-4 p-3">
                  <h6 class="fw-bold mb-3" style="color:#091c3d;"><i class="bi bi-journal-bookmark me-2" style="color:#f5951b;"></i>Voter Turnout by Programme</h6>
                  @if($votesByProgram->isEmpty())
                    <div class="text-center text-muted py-4 small"><i class="bi bi-inbox fs-2 d-block mb-2 opacity-25"></i>No data available.</div>
                  @else
                    <canvas id="programChart" height="180"></canvas>
                  @endif
                </div>
              </div>

            </div>

            {{-- Election timeline summary --}}
            <div class="card border-0 bg-light rounded-4 p-4 mb-4">
              <h6 class="fw-bold mb-3" style="color:#091c3d;"><i class="bi bi-calendar3 me-2" style="color:#f5951b;"></i>Election Timeline</h6>
              <div class="row g-3 small">
                <div class="col-6 col-md-3">
                  <div class="text-muted fw-semibold mb-1">Voting Opens</div>
                  <div class="fw-bold text-dark">{{ $election->voting_opens_at?->format('d M Y, H:i') ?? '—' }}</div>
                </div>
                <div class="col-6 col-md-3">
                  <div class="text-muted fw-semibold mb-1">Voting Closes</div>
                  <div class="fw-bold text-dark">{{ $election->voting_closes_at?->format('d M Y, H:i') ?? '—' }}</div>
                </div>
                <div class="col-6 col-md-3">
                  <div class="text-muted fw-semibold mb-1">Results Released</div>
                  <div class="fw-bold {{ $election->resultsReleased() ? 'text-success' : 'text-muted' }}">
                    {{ $election->results_released_at?->format('d M Y, H:i') ?? 'Not yet' }}
                  </div>
                </div>
                <div class="col-6 col-md-3">
                  <div class="text-muted fw-semibold mb-1">Acceptance Deadline</div>
                  <div class="fw-bold text-dark">{{ $election->acceptance_deadline_at?->format('d M Y, H:i') ?? '—' }}</div>
                </div>
              </div>
            </div>

            {{-- Export buttons --}}
            <div class="card border-0 bg-light rounded-4 p-4">
              <h6 class="fw-bold mb-3" style="color:#091c3d;"><i class="bi bi-download me-2" style="color:#f5951b;"></i>Export Reports (CSV)</h6>
              <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('reports.export', ['type' => 'overall']) }}"
                   class="btn btn-sm fw-semibold rounded-3 text-white" style="background:#091c3d;">
                  <i class="bi bi-file-earmark-spreadsheet me-1"></i>Overall Report
                </a>
                <a href="{{ route('reports.export', ['type' => 'positions']) }}"
                   class="btn btn-sm fw-semibold rounded-3 text-white" style="background:#0d6efd;">
                  <i class="bi bi-list-task me-1"></i>By Position
                </a>
                <a href="{{ route('reports.export', ['type' => 'faculties']) }}"
                   class="btn btn-sm fw-semibold rounded-3 text-white" style="background:#198754;">
                  <i class="bi bi-building me-1"></i>By Faculty
                </a>
                <a href="{{ route('reports.export', ['type' => 'candidates']) }}"
                   class="btn btn-sm fw-semibold rounded-3 text-white" style="background:#fd7e14;">
                  <i class="bi bi-person-badge me-1"></i>All Candidates
                </a>
                <button onclick="window.print()" class="btn btn-sm fw-semibold rounded-3 btn-outline-secondary ml-auto">
                  <i class="bi bi-printer me-1"></i>Print Page
                </button>
              </div>
            </div>

          </div>{{-- /overview --}}


          {{-- ═══════════════════════════════════════════════════════════════ --}}
          {{-- TAB 2 — BY POSITION                                            --}}
          {{-- ═══════════════════════════════════════════════════════════════ --}}
          <div class="tab-pane fade" id="tab-positions" role="tabpanel">

            @php
              $posTypeBg = [
                'president'   => '#0d6efd',
                'faculty_rep' => '#198754',
                'senator'     => '#fd7e14',
                'class_rep'   => '#0dcaf0',
              ];
              $posTypeLabel = [
                'president'   => 'President',
                'faculty_rep' => 'Faculty Representative',
                'senator'     => 'Senator',
                'class_rep'   => 'Class Representative',
              ];
            @endphp

            @forelse($positions as $pos)
            <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
              <div class="card-header border-0 py-3 px-4 d-flex align-items-center justify-content-between"
                   style="background:#f8f9fa;">
                <div>
                  <h6 class="fw-bold mb-0" style="color:#091c3d;">{{ $pos['name'] }}</h6>
                  <div class="d-flex align-items-center gap-2 mt-1">
                    <span class="badge rounded-pill px-2" style="background:{{ $posTypeBg[$pos['type']] ?? '#6c757d' }};color:#fff;font-size:.72rem;">
                      {{ $posTypeLabel[$pos['type']] ?? $pos['type'] }}
                    </span>
                    @if($pos['faculty'])
                      <span class="small text-muted"><i class="bi bi-building me-1"></i>{{ $pos['faculty'] }}</span>
                    @elseif($pos['program'])
                      <span class="small text-muted"><i class="bi bi-journal-bookmark me-1"></i>{{ $pos['program'] }}</span>
                    @endif
                    <span class="small text-muted"><i class="bi bi-ballot me-1"></i>{{ number_format($pos['total_votes']) }} total votes</span>
                  </div>
                </div>
                @if($pos['candidates']->isNotEmpty() && $pos['candidates'][0]['is_winner'])
                  <div class="text-end">
                    <div class="small text-muted fw-semibold">Winner</div>
                    <div class="fw-bold" style="color:#198754;">{{ $pos['candidates'][0]['name'] }}</div>
                  </div>
                @endif
              </div>

              <div class="card-body px-4 py-3">
                @if($pos['candidates']->isEmpty())
                  <p class="text-muted small mb-0">No candidates for this position.</p>
                @else
                  @foreach($pos['candidates'] as $i => $c)
                  <div class="mb-3">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                      <div class="d-flex align-items-center gap-2">
                        @if($c['image'])
                          <img src="{{ asset('storage/'.$c['image']) }}" style="width:32px;height:32px;border-radius:50%;object-fit:cover;border:2px solid #e9ecef;" onerror="this.style.display='none'">
                        @else
                          <div style="width:32px;height:32px;border-radius:50%;background:#e9ecef;display:inline-flex;align-items:center;justify-content:center;"><i class="bi bi-person-fill text-secondary" style="font-size:.8rem;"></i></div>
                        @endif
                        <span class="fw-semibold small" style="color:#091c3d;">{{ $c['name'] }}</span>
                        @if($c['is_winner'])
                          <span class="badge rounded-pill px-2" style="background:#198754;color:#fff;font-size:.68rem;"><i class="bi bi-trophy-fill me-1"></i>Winner</span>
                        @elseif($i === 0 && !$c['is_winner'] && $pos['total_votes'] === 0)
                          {{-- no votes yet --}}
                        @else
                          <span class="badge rounded-pill px-2 bg-secondary text-white" style="font-size:.68rem;">Runner-up</span>
                        @endif
                      </div>
                      <div class="text-end">
                        <span class="fw-bold" style="color:#091c3d;">{{ number_format($c['votes']) }}</span>
                        <span class="text-muted small"> votes</span>
                        <span class="badge ml-2 rounded-pill" style="background:#091c3d;color:#fff;font-size:.72rem;min-width:46px;">{{ $c['percentage'] }}%</span>
                      </div>
                    </div>
                    <div class="progress rounded-pill" style="height:12px;">
                      <div class="progress-bar rounded-pill" role="progressbar"
                           style="width:{{ $c['percentage'] }}%;background:{{ $c['is_winner'] ? '#198754' : ($i === 1 ? '#0d6efd' : '#adb5bd') }};"
                           aria-valuenow="{{ $c['percentage'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </div>
                  @endforeach

                  {{-- Summary table --}}
                  <div class="table-responsive mt-3">
                    <table class="table table-sm align-middle border-0" style="font-size:.83rem;">
                      <thead class="table-light">
                        <tr>
                          <th class="py-2 px-3">Rank</th>
                          <th class="py-2 px-3">Candidate</th>
                          <th class="py-2 px-3 text-center">Votes</th>
                          <th class="py-2 px-3 text-center">Share</th>
                          <th class="py-2 px-3 text-center">Status</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($pos['candidates'] as $rank => $c)
                        <tr>
                          <td class="px-3 fw-bold text-muted">#{{ $rank + 1 }}</td>
                          <td class="px-3 fw-semibold" style="color:#091c3d;">{{ $c['name'] }}</td>
                          <td class="px-3 text-center fw-bold">{{ number_format($c['votes']) }}</td>
                          <td class="px-3 text-center">{{ $c['percentage'] }}%</td>
                          <td class="px-3 text-center">
                            @if($c['is_winner'])
                              <span class="badge rounded-pill px-2" style="background:#198754;color:#fff;"><i class="bi bi-trophy-fill me-1"></i>Winner</span>
                            @elseif($pos['total_votes'] === 0)
                              <span class="badge rounded-pill px-2 bg-light text-muted border">No votes</span>
                            @else
                              <span class="badge rounded-pill px-2 bg-secondary text-white">Runner-up</span>
                            @endif
                          </td>
                        </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                @endif
              </div>
            </div>
            @empty
              <div class="text-center text-muted py-5">
                <i class="bi bi-inbox fs-1 d-block mb-2 opacity-25"></i>No positions configured yet.
              </div>
            @endforelse

          </div>{{-- /tab-positions --}}


          {{-- ═══════════════════════════════════════════════════════════════ --}}
          {{-- TAB 3 — BY FACULTY                                             --}}
          {{-- ═══════════════════════════════════════════════════════════════ --}}
          <div class="tab-pane fade" id="tab-faculties" role="tabpanel">

            @forelse($faculties as $f)
            <div class="card border-0 shadow-sm rounded-4 mb-4">
              <div class="card-header bg-white border-bottom py-3 px-4">
                <div class="d-flex align-items-center justify-content-between">
                  <h6 class="fw-bold mb-0" style="color:#091c3d;"><i class="bi bi-building me-2" style="color:#f5951b;"></i>{{ $f['name'] }}</h6>
                  <span class="badge rounded-pill px-3 py-2"
                        style="background:{{ $f['participation'] >= 75 ? '#198754' : ($f['participation'] >= 50 ? '#f5951b' : ($f['total_voters'] > 0 ? '#dc3545' : '#6c757d')) }};color:#fff;">
                    {{ $f['participation'] }}% participation
                  </span>
                </div>
              </div>
              <div class="card-body px-4 py-3">

                {{-- Stats row --}}
                <div class="row g-3 mb-3">
                  <div class="col-4">
                    <div class="text-center p-2 rounded-3" style="background:#f8f9fa;">
                      <div class="small text-muted fw-semibold">Registered Voters</div>
                      <div class="fw-bold fs-5" style="color:#091c3d;">{{ number_format($f['total_voters']) }}</div>
                    </div>
                  </div>
                  <div class="col-4">
                    <div class="text-center p-2 rounded-3" style="background:#e8f5e9;">
                      <div class="small fw-semibold" style="color:#1b5e20;">Voted</div>
                      <div class="fw-bold fs-5" style="color:#198754;">{{ number_format($f['voted_count']) }}</div>
                    </div>
                  </div>
                  <div class="col-4">
                    <div class="text-center p-2 rounded-3" style="background:#fdecea;">
                      <div class="small fw-semibold" style="color:#7f0000;">Did Not Vote</div>
                      <div class="fw-bold fs-5" style="color:#dc3545;">{{ number_format($f['not_voted']) }}</div>
                    </div>
                  </div>
                </div>

                {{-- Participation bar --}}
                <div class="mb-3">
                  <div class="d-flex justify-content-between small text-muted mb-1">
                    <span>Voter turnout</span>
                    <span class="fw-semibold">{{ $f['voted_count'] }} of {{ $f['total_voters'] }}</span>
                  </div>
                  <div class="progress rounded-pill" style="height:14px;">
                    <div class="progress-bar rounded-pill"
                         style="width:{{ $f['participation'] }}%;background:{{ $f['participation'] >= 75 ? '#198754' : ($f['participation'] >= 50 ? '#f5951b' : '#dc3545') }};"
                         role="progressbar"
                         aria-valuenow="{{ $f['participation'] }}" aria-valuemin="0" aria-valuemax="100">
                    </div>
                  </div>
                </div>

                {{-- Faculty-level positions --}}
                @if($f['positions']->isNotEmpty())
                <div class="mt-3">
                  <div class="small text-muted fw-semibold mb-2"><i class="bi bi-list-task me-1"></i>Positions in this faculty</div>
                  @foreach($f['positions'] as $p)
                  <div class="mb-2 p-2 rounded-3" style="background:#f8f9fa;">
                    <div class="small fw-semibold mb-1" style="color:#091c3d;">{{ $p['name'] }}
                      <span class="badge ml-1 rounded-pill" style="background:{{ $posTypeBg[$p['type']] ?? '#6c757d' }};color:#fff;font-size:.65rem;">{{ $posTypeLabel[$p['type']] ?? $p['type'] }}</span>
                    </div>
                    @foreach($p['candidates'] as $ci => $c)
                    <div class="d-flex align-items-center small mb-1">
                      <span class="text-muted me-2" style="min-width:18px;">#{{ $ci + 1 }}</span>
                      <span class="fw-semibold me-2" style="color:#091c3d;">{{ $c['name'] }}</span>
                      <span class="badge rounded-pill" style="background:#091c3d;color:#fff;font-size:.68rem;">{{ number_format($c['votes']) }} votes</span>
                      @if($ci === 0 && $c['votes'] > 0)
                        <span class="badge rounded-pill ml-1" style="background:#198754;color:#fff;font-size:.68rem;"><i class="bi bi-trophy-fill"></i></span>
                      @endif
                    </div>
                    @endforeach
                  </div>
                  @endforeach
                </div>
                @else
                  <p class="small text-muted mb-0 mt-2"><i class="bi bi-info-circle me-1"></i>No faculty-level positions configured for this faculty.</p>
                @endif

              </div>
            </div>
            @empty
              <div class="text-center text-muted py-5">
                <i class="bi bi-building fs-1 d-block mb-2 opacity-25"></i>No faculties configured yet.
              </div>
            @endforelse

          </div>{{-- /tab-faculties --}}


          {{-- ═══════════════════════════════════════════════════════════════ --}}
          {{-- TAB 4 — ALL CANDIDATES                                         --}}
          {{-- ═══════════════════════════════════════════════════════════════ --}}
          <div class="tab-pane fade" id="tab-candidates" role="tabpanel">

            {{-- Filter / search --}}
            <div class="d-flex align-items-center justify-content-between mb-3">
              <div class="input-group" style="max-width:320px;">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" class="form-control border-start-0 ps-0" id="candidateSearch"
                       placeholder="Search candidate or position…" oninput="filterCandidates()">
              </div>
              <a href="{{ route('reports.export', ['type' => 'candidates']) }}"
                 class="btn btn-sm fw-semibold rounded-3 text-white" style="background:#fd7e14;">
                <i class="bi bi-download me-1"></i>Export CSV
              </a>
            </div>

            <div class="table-responsive">
              <table class="table table-hover align-middle border-0" id="candidatesTable">
                <thead class="table-light">
                  <tr>
                    <th class="py-3 px-3">#</th>
                    <th class="py-3 px-3">Candidate</th>
                    <th class="py-3 px-3">Position</th>
                    <th class="py-3 px-3">Faculty / Programme</th>
                    <th class="py-3 px-3 text-center">Votes</th>
                    <th class="py-3 px-3" style="min-width:160px;">Vote Share</th>
                    <th class="py-3 px-3 text-center">Status</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($candidates as $i => $c)
                  <tr data-search="{{ strtolower($c['name'] . ' ' . $c['position'] . ' ' . $c['affiliation']) }}">
                    <td class="px-3 fw-bold text-muted">#{{ $i + 1 }}</td>
                    <td class="px-3">
                      <div class="d-flex align-items-center gap-2">
                        @if($c['image'])
                          <img src="{{ asset('storage/'.$c['image']) }}" style="width:38px;height:38px;border-radius:50%;object-fit:cover;border:2px solid #e9ecef;" onerror="this.style.display='none'">
                        @else
                          <div style="width:38px;height:38px;border-radius:50%;background:#e9ecef;display:flex;align-items:center;justify-content:center;"><i class="bi bi-person-fill text-secondary"></i></div>
                        @endif
                        <span class="fw-semibold" style="color:#091c3d;">{{ $c['name'] }}</span>
                      </div>
                    </td>
                    <td class="px-3">
                      <div class="small fw-semibold" style="color:#091c3d;">{{ $c['position'] ?? '—' }}</div>
                      @if($c['position_type'])
                        <span class="badge rounded-pill px-2" style="background:{{ $posTypeBg[$c['position_type']] ?? '#6c757d' }};color:#fff;font-size:.65rem;">
                          {{ $posTypeLabel[$c['position_type']] ?? $c['position_type'] }}
                        </span>
                      @endif
                    </td>
                    <td class="px-3 small text-muted">{{ $c['affiliation'] ?? '—' }}</td>
                    <td class="px-3 text-center">
                      <span class="fw-bold fs-5" style="color:#091c3d;">{{ number_format($c['votes']) }}</span>
                      <div class="small text-muted">of {{ number_format($c['position_total']) }}</div>
                    </td>
                    <td class="px-3">
                      <div class="d-flex align-items-center gap-2">
                        <div class="progress flex-grow-1 rounded-pill" style="height:8px;">
                          <div class="progress-bar rounded-pill"
                               style="width:{{ $c['percentage'] }}%;background:{{ isset($c['won']) && $c['won'] ? '#198754' : '#0d6efd' }};"
                               role="progressbar"></div>
                        </div>
                        <span class="small fw-semibold" style="min-width:36px;color:#091c3d;">{{ $c['percentage'] }}%</span>
                      </div>
                    </td>
                    <td class="px-3 text-center">
                      @if(!$c['results_out'])
                        <span class="badge rounded-pill px-2" style="background:#fff3cd;color:#856404;border:1px solid #ffc107;font-size:.75rem;">Pending</span>
                      @elseif($c['won'])
                        <span class="badge rounded-pill px-2" style="background:#198754;color:#fff;font-size:.75rem;"><i class="bi bi-trophy-fill me-1"></i>Winner</span>
                      @else
                        <span class="badge rounded-pill px-2 bg-secondary text-white" style="font-size:.75rem;">Runner-up</span>
                      @endif
                    </td>
                  </tr>
                  @empty
                    <tr>
                      <td colspan="7" class="text-center text-muted py-5">
                        <i class="bi bi-inbox fs-1 d-block mb-2 opacity-25"></i>No candidates registered yet.
                      </td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>

          </div>{{-- /tab-candidates --}}

        </div>{{-- /tab-content --}}
      </div>{{-- /card --}}

    </main>
  </div>
</div>

<style>
  body { font-family:'Inter',system-ui,sans-serif; background:#f8f9fa; }
  .active-link  { background:#f5951b !important; font-weight:600; }
  .hover-link:hover { background:rgba(255,255,255,.08); }
  .hover-logout:hover { background:rgba(220,53,69,.1); }
  .nav-tabs .nav-link { border-bottom: 3px solid transparent; border-top:0; border-left:0; border-right:0; padding:.75rem 1rem; }
  .nav-tabs .nav-link.active { border-bottom-color:#f5951b; background:transparent; }
  @media print {
    aside, .navbar, .nav-tabs, .btn, a.btn { display:none !important; }
    [style*="margin-left"] { margin-left:0 !important; }
    .tab-pane { display:block !important; opacity:1 !important; }
  }
</style>

<script>
// ── Charts ──────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {

  // Participation donut
  const pCtx = document.getElementById('participationChart');
  if (pCtx) {
    new Chart(pCtx, {
      type: 'doughnut',
      data: {
        labels: ['Voted', 'Did not vote'],
        datasets: [{
          data: [{{ $totalVoted }}, {{ $notVoted }}],
          backgroundColor: ['#198754', '#dee2e6'],
          borderWidth: 0,
          hoverOffset: 6,
        }]
      },
      options: {
        cutout: '68%',
        plugins: {
          legend: { display: false },
          tooltip: {
            callbacks: {
              label: ctx => ` ${ctx.label}: ${ctx.parsed.toLocaleString()} (${
                {{ $totalVoters }} > 0 ? ((ctx.parsed / {{ $totalVoters }}) * 100).toFixed(1) : 0
              }%)`
            }
          }
        }
      }
    });
  }

  // Vote timeline
  @if($votingActivity->isNotEmpty())
  const tCtx = document.getElementById('timelineChart');
  if (tCtx) {
    new Chart(tCtx, {
      type: 'line',
      data: {
        labels: {!! $votingActivity->pluck('hour')->map(fn($h) => "'" . substr($h, 0, 13) . ":00'")->join(',') !!},
        datasets: [{
          label: 'Votes cast',
          data: [{{ $votingActivity->pluck('count')->join(',') }}],
          borderColor: '#091c3d',
          backgroundColor: 'rgba(9,28,61,.08)',
          fill: true,
          tension: 0.3,
          pointBackgroundColor: '#f5951b',
          pointRadius: 4,
        }]
      },
      options: {
        scales: {
          x: { ticks: { maxRotation: 45, font: { size: 10 } } },
          y: { beginAtZero: true, ticks: { precision: 0 } }
        },
        plugins: { legend: { display: false } }
      }
    });
  }
  @endif

  // Program bar chart
  @if($votesByProgram->isNotEmpty())
  const pgCtx = document.getElementById('programChart');
  if (pgCtx) {
    const colors = ['#0d6efd','#198754','#fd7e14','#0dcaf0','#6f42c1','#d63384','#20c997','#ffc107'];
    new Chart(pgCtx, {
      type: 'bar',
      data: {
        labels: [
          @foreach($votesByProgram as $p)
            '{{ addslashes($p->program) }}',
          @endforeach
        ],
        datasets: [{
          label: 'Unique voters',
          data: [
            @foreach($votesByProgram as $p)
              {{ $p->voted_count }},
            @endforeach
          ],
          backgroundColor: colors,
          borderRadius: 6,
        }]
      },
      options: {
        indexAxis: 'y',
        scales: {
          x: { beginAtZero: true, ticks: { precision: 0 } },
          y: { ticks: { font: { size: 11 } } }
        },
        plugins: { legend: { display: false } }
      }
    });
  }
  @endif

});

// ── Candidate search filter ──────────────────────────────────────────────────
function filterCandidates() {
  const q = document.getElementById('candidateSearch').value.toLowerCase();
  document.querySelectorAll('#candidatesTable tbody tr[data-search]').forEach(tr => {
    tr.style.display = tr.dataset.search.includes(q) ? '' : 'none';
  });
}
</script>
@endsection
