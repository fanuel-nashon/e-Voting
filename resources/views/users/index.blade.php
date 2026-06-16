@extends('layouts.app')

@section('title', 'Users & Voters - e-Voting Admin')

@section('content')

<div class="container-fluid p-0 d-flex min-vh-100 flex-column flex-md-row">

    <!-- Sidebar -->
    <aside class="text-white d-flex flex-column justify-content-between p-3 position-fixed top-0 start-0 h-100 shadow"
           style="width: 260px; background-color: #091c3d; z-index: 1030;">
        <div class="w-100">
            <div class="d-flex align-items-center mb-4 px-2 pt-2">
                <i class="bi bi-shield-check fs-3 me-2" style="color: #f5951b;"></i>
                <span class="fs-5 fw-bold">e-Voting Admin</span>
            </div>

            <hr class="opacity-25 mb-4">

            <ul class="nav flex-column gap-2 w-100">
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link text-white d-flex align-items-center px-3 py-2 rounded-3 hover-link">
                        <i class="bi bi-speedometer2 me-3 fs-5"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('users.index') }}" class="nav-link text-white d-flex align-items-center px-3 py-2 rounded-3 active-link">
                        <i class="bi bi-people me-3 fs-5"></i> Users / Voters
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}?section=faculties"
                       class="nav-link text-white d-flex align-items-center px-3 py-2 rounded-3 hover-link">
                        <i class="bi bi-building me-3 fs-5"></i> Faculties
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}?section=programs"
                       class="nav-link text-white d-flex align-items-center px-3 py-2 rounded-3 hover-link">
                        <i class="bi bi-journal-bookmark me-3 fs-5"></i> Programs
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('candidates.index') }}"
                       class="nav-link text-white d-flex align-items-center px-3 py-2 rounded-3 hover-link">
                        <i class="bi bi-person-badge me-3 fs-5"></i> Candidates
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link text-white d-flex align-items-center px-3 py-2 rounded-3 hover-link">
                        <i class="bi bi-journal-text me-3 fs-5"></i> Audit Logs
                    </a>
                </li>
            </ul>
        </div>

        <div class="w-100 pb-2">
            <hr class="opacity-25">
            <form action="{{ route('logout') }}" method="POST" class="d-inline w-100">
                @csrf
                <button type="submit" class="btn btn-link text-white text-decoration-none d-flex align-items-center px-3 py-2 w-100 rounded-3 hover-logout">
                    <i class="bi bi-box-arrow-left me-3 fs-5 text-danger"></i> Sign Out
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-grow-1 min-vh-100 d-flex flex-column" style="margin-left: 260px; background-color: #f8f9fa;">

        <!-- Top Nav -->
        <nav class="navbar navbar-expand-lg navbar-white bg-white border-bottom px-4 py-3 sticky-top shadow-sm">
            <div class="container-fluid p-0">
                <div class="d-flex align-items-center gap-3">
                    <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-secondary rounded-3 d-flex align-items-center gap-2">
                        <i class="bi bi-arrow-left"></i> Back to Dashboard
                    </a>
                    <span class="fw-bold" style="color: #091c3d;">Users &amp; Voters</span>
                </div>

                <div class="d-flex align-items-center ms-auto">
                    <div class="d-flex align-items-center me-3">
                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 38px; height: 38px;">
                            <i class="bi bi-person text-secondary"></i>
                        </div>
                        <div>
                            <div class="fw-semibold small text-dark">{{ Auth::user()->name ?? 'Admin' }}</div>
                            <span class="text-muted" style="font-size: 0.75rem;">System Administrator</span>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <main class="p-4 p-md-5 flex-grow-1">

            <!-- Stats Row -->
            <div class="row g-4 mb-5">
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
                        <div class="mb-3 text-primary"><i class="bi bi-people fs-4"></i></div>
                        <h6 class="text-muted fw-semibold mb-1 small">Total Users</h6>
                        <h3 class="fw-bold m-0" style="color: #091c3d;" id="statTotal">{{ count($users) }}</h3>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
                        <div class="mb-3" style="color:#198754;"><i class="bi bi-person-check fs-4"></i></div>
                        <h6 class="text-muted fw-semibold mb-1 small">Voters</h6>
                        <h3 class="fw-bold m-0" style="color: #091c3d;" id="statVoters">{{ collect($users)->where('role','voter')->count() }}</h3>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
                        <div class="mb-3 text-warning"><i class="bi bi-person-gear fs-4"></i></div>
                        <h6 class="text-muted fw-semibold mb-1 small">Election Admins</h6>
                        <h3 class="fw-bold m-0" style="color: #091c3d;" id="statElectionAdmins">{{ collect($users)->where('role','election_admin')->count() }}</h3>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
                        <div class="mb-3 text-danger"><i class="bi bi-shield-lock fs-4"></i></div>
                        <h6 class="text-muted fw-semibold mb-1 small">Admins</h6>
                        <h3 class="fw-bold m-0" style="color: #091c3d;" id="statAdmins">{{ collect($users)->where('role','admin')->count() }}</h3>
                    </div>
                </div>
            </div>

            <!-- Users Table Card -->
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="fw-bold m-0" style="color: #091c3d;">All Users</h4>
                    <button class="btn text-white rounded-3 px-4" style="background-color: #091c3d;" data-toggle="modal" data-target="#createUserModal">
                        <i class="bi bi-plus-lg me-2"></i>Add User
                    </button>
                </div>

                <!-- Role filter tabs -->
                <div class="mb-3 d-flex gap-2 flex-wrap">
                    <button class="btn btn-sm rounded-pill filter-btn active-filter" onclick="filterTable('all', this)">All</button>
                    <button class="btn btn-sm rounded-pill filter-btn" onclick="filterTable('voter', this)">Voters</button>
                    <button class="btn btn-sm rounded-pill filter-btn" onclick="filterTable('election_admin', this)">Election Admins</button>
                    <button class="btn btn-sm rounded-pill filter-btn" onclick="filterTable('admin', this)">Admins</button>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle border-0">
                        <thead class="table-light">
                            <tr>
                                <th class="py-3 px-4" style="width: 60px;">S/No</th>
                                <th class="py-3 px-4">Name</th>
                                <th class="py-3 px-4">Email</th>
                                <th class="py-3 px-4">Role</th>
                                <th class="py-3 px-4">Student Profile</th>
                                <th class="py-3 px-4" style="width: 160px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="usersTbody">
                            @forelse($users as $index => $user)
                            <tr data-id="{{ $user['id'] }}"
                                data-role="{{ $user['role'] }}"
                                data-name="{{ $user['name'] }}"
                                data-student-id="{{ $user['student_id'] }}"
                                data-reg-no="{{ $user['student_reg_no'] }}"
                                data-student-name="{{ $user['student_name'] }}"
                                data-faculty-id="{{ $user['student_faculty_id'] }}"
                                data-program-id="{{ $user['student_program_id'] }}">
                                <td class="px-4 fw-semibold text-secondary">{{ $index + 1 }}</td>
                                <td class="px-4 fw-medium text-dark">{{ $user['name'] }}</td>
                                <td class="px-4 text-muted small">{{ $user['email'] }}</td>
                                <td class="px-4">
                                    <span class="badge rounded-pill px-3 py-1 role-badge-{{ $user['role'] }}">
                                        {{ match($user['role']) {
                                            'admin'          => 'Admin',
                                            'election_admin' => 'Election Admin',
                                            'voter'          => 'Voter',
                                            default          => ucfirst($user['role']),
                                        } }}
                                    </span>
                                </td>
                                <td class="px-4 student-profile-cell">
                                    @if($user['role'] === 'voter')
                                        @if($user['student_id'])
                                            <div class="small fw-semibold" style="color:#091c3d;">{{ $user['student_reg_no'] }}</div>
                                            <div class="small text-muted">{{ $user['student_faculty'] }}</div>
                                            <div class="small text-muted">{{ $user['student_program'] }}</div>
                                        @else
                                            <span class="badge rounded-pill px-2" style="background:#fff3cd;color:#856404;border:1px solid #ffc107;font-size:.75rem;">No profile</span>
                                        @endif
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                <td class="px-4">
                                    <div class="d-flex gap-1 flex-wrap">
                                        @if($user['role'] === 'voter')
                                        <button class="btn btn-sm rounded-3 fw-semibold"
                                                style="background:#091c3d;color:#fff;font-size:.78rem;"
                                                onclick="openProfileModal(this.closest('tr'))">
                                            <i class="bi bi-person-badge me-1"></i>{{ $user['student_id'] ? 'Edit Profile' : 'Set Profile' }}
                                        </button>
                                        @endif
                                        @if($user['id'] !== Auth::id())
                                        <button class="btn btn-sm btn-outline-danger rounded-3"
                                                onclick="deleteUser({{ $user['id'] }}, this.closest('tr'))">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                        @else
                                        <span class="text-muted small">You</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr id="emptyRow">
                                <td colspan="6" class="text-center text-muted py-4">No users found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>
</div>

<!-- Create User Modal -->
<div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="createUserModalLabel" style="color: #091c3d;">Add New User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body pt-3">
                <form id="createUserForm" onsubmit="createUser(event)">
                    <div class="mb-3">
                        <label for="userName" class="form-label fw-semibold text-dark">Full Name</label>
                        <input type="text" class="form-control" id="userName" required placeholder="e.g., John Doe">
                    </div>
                    <div class="mb-3">
                        <label for="userEmail" class="form-label fw-semibold text-dark">Email Address</label>
                        <input type="email" class="form-control" id="userEmail" required placeholder="e.g., john@example.com">
                    </div>
                    <div class="mb-3">
                        <label for="userRole" class="form-label fw-semibold text-dark">Role</label>
                        <select class="form-control" id="userRole" required>
                            <option value="">— Select role —</option>
                            <option value="voter">Voter</option>
                            <option value="election_admin">Election Admin</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="userPassword" class="form-label fw-semibold text-dark">Password</label>
                        <input type="password" class="form-control" id="userPassword" required minlength="6" placeholder="Min. 6 characters">
                    </div>
                    <div class="mb-3">
                        <label for="userPasswordConfirm" class="form-label fw-semibold text-dark">Confirm Password</label>
                        <input type="password" class="form-control" id="userPasswordConfirm" required placeholder="Repeat password">
                    </div>
                    <p class="text-danger small mb-3 d-none" id="createUserErr"></p>
                    <div class="d-flex justify-content-end" style="gap: 0.5rem;">
                        <button type="button" class="btn btn-secondary rounded-3" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn text-white px-4 rounded-3" style="background-color: #f5951b;">
                            <span id="createUserBtnText">Create User</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Set Student Profile Modal -->
<div class="modal fade" id="studentProfileModal" tabindex="-1" aria-labelledby="studentProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <div>
                    <h5 class="modal-title fw-bold mb-0" id="studentProfileModalLabel" style="color:#091c3d;">Student Profile</h5>
                    <p class="text-muted small mb-0" id="profileModalSubtitle"></p>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body pt-3">
                <form id="studentProfileForm" onsubmit="saveStudentProfile(event)">
                    <input type="hidden" id="profileUserId">

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small">Full Name</label>
                        <input type="text" class="form-control" id="profileName" required placeholder="Student's full name">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small">Registration Number</label>
                        <input type="text" class="form-control" id="profileRegNo" required placeholder="e.g. MZ/ICT/2022/001">
                        <div class="form-text">Must be unique. Include the enrolment year (e.g. 2022).</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small">Faculty</label>
                        <select class="form-control" id="profileFacultyId" required onchange="filterProgramsByFaculty()">
                            <option value="">— Select faculty —</option>
                            @foreach($faculties as $f)
                                <option value="{{ $f->id }}">{{ $f->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small">Programme</label>
                        <select class="form-control" id="profileProgramId" required>
                            <option value="">— Select faculty first —</option>
                        </select>
                    </div>

                    <p class="text-danger small mb-3 d-none" id="profileErr"></p>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary rounded-3" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn text-white px-4 rounded-3" style="background:#f5951b;">
                            <span id="profileBtnText"><i class="bi bi-person-badge me-1"></i>Save Profile</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    body { background-color: #f8f9fa; font-family: 'Inter', system-ui, -apple-system, sans-serif; overflow-x: hidden; }
    .active-link  { background-color: #f5951b !important; color: #091c3d !important; font-weight: 600; }
    .hover-link:hover { background-color: rgba(255,255,255,0.08); }
    .hover-logout:hover { background-color: rgba(220,53,69,0.1); }

    .role-badge-voter          { background: #0d6efd; color: #fff; }
    .role-badge-election_admin { background: #fd7e14; color: #fff; }
    .role-badge-admin          { background: #dc3545; color: #fff; }
    .role-badge-none           { background: #6c757d; color: #fff; }

    .filter-btn        { border: 1px solid #dee2e6; color: #495057; background: #fff; }
    .filter-btn:hover  { background: #f0f0f0; }
    .active-filter     { background-color: #091c3d !important; color: #fff !important; border-color: #091c3d !important; }
</style>

<script>
    const CSRF = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    function escapeHtml(str) {
        const d = document.createElement('div');
        d.appendChild(document.createTextNode(str));
        return d.innerHTML;
    }

    // ─── Filter by role ───────────────────────────────────────────────────────
    function filterTable(role, btn) {
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active-filter'));
        btn.classList.add('active-filter');

        document.querySelectorAll('#usersTbody tr[data-id]').forEach(tr => {
            tr.style.display = (role === 'all' || tr.dataset.role === role) ? '' : 'none';
        });
    }

    // ─── Row counter helper ───────────────────────────────────────────────────
    function reindex() {
        let i = 1;
        document.querySelectorAll('#usersTbody tr[data-id]').forEach(tr => {
            tr.cells[0].textContent = i++;
        });
    }

    // ─── Update stat cards ────────────────────────────────────────────────────
    function updateStats() {
        const rows = [...document.querySelectorAll('#usersTbody tr[data-id]')];
        document.getElementById('statTotal').textContent         = rows.length;
        document.getElementById('statVoters').textContent        = rows.filter(r => r.dataset.role === 'voter').length;
        document.getElementById('statElectionAdmins').textContent = rows.filter(r => r.dataset.role === 'election_admin').length;
        document.getElementById('statAdmins').textContent        = rows.filter(r => r.dataset.role === 'admin').length;
    }

    // ─── Append a new row ─────────────────────────────────────────────────────
    function appendUserRow(user) {
        const emptyRow = document.getElementById('emptyRow');
        if (emptyRow) emptyRow.remove();

        const roleLabels = { voter: 'Voter', election_admin: 'Election Admin', admin: 'Admin' };
        const tbody = document.getElementById('usersTbody');
        const count = tbody.querySelectorAll('tr[data-id]').length + 1;

        const tr = document.createElement('tr');
        tr.setAttribute('data-id', user.id);
        tr.setAttribute('data-role', user.role);
        tr.setAttribute('data-name', user.name);
        tr.setAttribute('data-student-id', '');
        tr.setAttribute('data-reg-no', '');
        tr.setAttribute('data-student-name', '');
        tr.setAttribute('data-faculty-id', '');
        tr.setAttribute('data-program-id', '');
        tr.innerHTML = `
            <td class="px-4 fw-semibold text-secondary">${count}</td>
            <td class="px-4 fw-medium text-dark">${escapeHtml(user.name)}</td>
            <td class="px-4 text-muted small">${escapeHtml(user.email)}</td>
            <td class="px-4">
                <span class="badge rounded-pill px-3 py-1 role-badge-${user.role}">
                    ${roleLabels[user.role] || user.role}
                </span>
            </td>
            <td class="px-4 student-profile-cell">
                ${user.role === 'voter'
                    ? '<span class="badge rounded-pill px-2" style="background:#fff3cd;color:#856404;border:1px solid #ffc107;font-size:.75rem;">No profile</span>'
                    : '<span class="text-muted small">—</span>'}
            </td>
            <td class="px-4">
                <div class="d-flex gap-1 flex-wrap">
                    ${user.role === 'voter'
                        ? `<button class="btn btn-sm rounded-3 fw-semibold" style="background:#091c3d;color:#fff;font-size:.78rem;" onclick="openProfileModal(this.closest('tr'))"><i class="bi bi-person-badge me-1"></i>Set Profile</button>`
                        : ''}
                    <button class="btn btn-sm btn-outline-danger rounded-3"
                            onclick="deleteUser(${user.id}, this.closest('tr'))">
                        <i class="bi bi-trash-fill"></i>
                    </button>
                </div>
            </td>
        `;
        tbody.appendChild(tr);
        updateStats();
    }

    // ─── Create user ──────────────────────────────────────────────────────────
    function createUser(event) {
        event.preventDefault();
        const errElem = document.getElementById('createUserErr');
        errElem.classList.add('d-none');

        const name              = document.getElementById('userName').value;
        const email             = document.getElementById('userEmail').value;
        const role              = document.getElementById('userRole').value;
        const password          = document.getElementById('userPassword').value;
        const password_confirmation = document.getElementById('userPasswordConfirm').value;

        if (password !== password_confirmation) {
            errElem.textContent = 'Passwords do not match.';
            errElem.classList.remove('d-none');
            return;
        }

        const btn = document.getElementById('createUserBtnText');
        btn.textContent = 'Creating…';

        fetch("{{ route('users.store') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': CSRF,
            },
            body: JSON.stringify({ name, email, role, password, password_confirmation }),
        })
        .then(async response => {
            const data = await response.json();
            if (!response.ok) {
                if (response.status === 422 && data?.errors) {
                    const first = Object.values(data.errors)[0];
                    throw new Error(Array.isArray(first) ? first[0] : first);
                }
                throw new Error(data?.message || `Error (${response.status})`);
            }
            return data;
        })
        .then(data => {
            if (data.success) {
                appendUserRow(data.user);
                document.getElementById('createUserForm').reset();
                $('#createUserModal').modal('hide');
            }
        })
        .catch(err => {
            errElem.textContent = err.message;
            errElem.classList.remove('d-none');
        })
        .finally(() => { btn.textContent = 'Create User'; });
    }

    // ─── Student profile modal ────────────────────────────────────────────────
    const ALL_PROGRAMS = @json($programs);   // [{id, name, faculty_id}, ...]

    function openProfileModal(row) {
        const userId      = row.dataset.id;
        const userName    = row.dataset.name;
        const studentName = row.dataset.studentName || userName;
        const regNo       = row.dataset.regNo       || '';
        const facultyId   = row.dataset.facultyId   || '';
        const programId   = row.dataset.programId   || '';

        document.getElementById('profileUserId').value         = userId;
        document.getElementById('profileName').value           = studentName;
        document.getElementById('profileRegNo').value          = regNo;
        document.getElementById('profileFacultyId').value      = facultyId;
        document.getElementById('profileErr').classList.add('d-none');
        document.getElementById('profileBtnText').innerHTML    = '<i class="bi bi-person-badge me-1"></i>Save Profile';
        document.getElementById('profileModalSubtitle').textContent = userName;

        filterProgramsByFaculty(programId);
        $('#studentProfileModal').modal('show');
    }

    function filterProgramsByFaculty(preselect = null) {
        const facultyId = document.getElementById('profileFacultyId').value;
        const sel       = document.getElementById('profileProgramId');
        sel.innerHTML   = '<option value="">— Select programme —</option>';

        const filtered = ALL_PROGRAMS.filter(p => String(p.faculty_id) === String(facultyId));
        filtered.forEach(p => {
            const opt = document.createElement('option');
            opt.value       = p.id;
            opt.textContent = p.name;
            if (preselect && String(p.id) === String(preselect)) opt.selected = true;
            sel.appendChild(opt);
        });

        if (!facultyId) {
            sel.innerHTML = '<option value="">— Select faculty first —</option>';
        } else if (filtered.length === 0) {
            sel.innerHTML = '<option value="">No programmes for this faculty</option>';
        }
    }

    function saveStudentProfile(event) {
        event.preventDefault();
        const errEl = document.getElementById('profileErr');
        errEl.classList.add('d-none');

        const userId    = document.getElementById('profileUserId').value;
        const name      = document.getElementById('profileName').value;
        const regNo     = document.getElementById('profileRegNo').value;
        const facultyId = document.getElementById('profileFacultyId').value;
        const programId = document.getElementById('profileProgramId').value;
        const btn       = document.getElementById('profileBtnText');

        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Saving…';

        fetch(`/users/${userId}/student`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': CSRF,
            },
            body: JSON.stringify({ name, reg_no: regNo, faculty_id: facultyId, program_id: programId }),
        })
        .then(async response => {
            const data = await response.json();
            if (!response.ok) {
                if (response.status === 422 && data?.errors) {
                    const first = Object.values(data.errors)[0];
                    throw new Error(Array.isArray(first) ? first[0] : first);
                }
                throw new Error(data?.message || `Error (${response.status})`);
            }
            return data;
        })
        .then(data => {
            if (data.success) {
                // Update the row's data attributes
                const row = document.querySelector(`tr[data-id="${userId}"]`);
                row.dataset.studentId   = data.student_id;
                row.dataset.regNo       = data.student_reg_no;
                row.dataset.studentName = data.student_name;
                row.dataset.facultyId   = data.student_faculty_id;
                row.dataset.programId   = data.student_program_id;

                // Update the Student Profile cell
                row.querySelector('.student-profile-cell').innerHTML = `
                    <div class="small fw-semibold" style="color:#091c3d;">${escapeHtml(data.student_reg_no)}</div>
                    <div class="small text-muted">${escapeHtml(data.student_faculty || '')}</div>
                    <div class="small text-muted">${escapeHtml(data.student_program || '')}</div>
                `;

                // Update the Set Profile button label to "Edit Profile"
                const profileBtn = row.querySelector('button[onclick*="openProfileModal"]');
                if (profileBtn) {
                    profileBtn.innerHTML = '<i class="bi bi-person-badge me-1"></i>Edit Profile';
                }

                $('#studentProfileModal').modal('hide');
            }
        })
        .catch(err => {
            errEl.textContent = err.message;
            errEl.classList.remove('d-none');
        })
        .finally(() => {
            btn.innerHTML = '<i class="bi bi-person-badge me-1"></i>Save Profile';
        });
    }

    // ─── Delete user ──────────────────────────────────────────────────────────
    function deleteUser(id, row) {
        if (!confirm('Are you sure you want to delete this user?')) return;

        fetch(`/users/${id}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': CSRF,
            },
        })
        .then(async response => {
            const data = await response.json();
            if (!response.ok) throw new Error(data?.message || `Error (${response.status})`);
            return data;
        })
        .then(data => {
            if (data.success) {
                row.remove();
                reindex();
                updateStats();
            }
        })
        .catch(err => alert('Delete failed: ' + err.message));
    }
</script>

@endsection
