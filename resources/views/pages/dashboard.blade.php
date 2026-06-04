@extends('layouts.app')

@section('title', 'Admin Dashboard - e-Voting')

@section('content')

<div class="container-fluid p-0 d-flex min-vh-100 flex-column flex-md-row">

    <!-- Sidebar -->
    <aside class="text-white d-flex flex-column justify-content-between p-3 position-fixed top-0 start-0 h-100 shadow"
           style="width: 260px; background-color: #091c3d; z-index: 1030;">
        <div class="w-100">
            <div class="d-flex align-items-center mb-4 px-2 pt-2">
                <i class="bi bi-shield-check fs-3 me-2" style="color: #f5951b;"></i>
                <span class="fs-5 fw-bold tracking-wider">e-Voting Admin</span>
            </div>

            <hr class="opacity-25 mb-4">

            <ul class="nav flex-column gap-2 w-100">
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" id="dashboardLink"
                       class="nav-link text-white d-flex align-items-center px-3 py-2.5 rounded-3 active-link">
                        <i class="bi bi-speedometer2 me-3 fs-5"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link text-white d-flex align-items-center px-3 py-2.5 rounded-3 hover-link">
                        <i class="bi bi-people me-3 fs-5"></i> Users / Voters
                    </a>
                </li>
                <li class="nav-item">
                    <a href="javascript:void(0)" id="facultySectionLink"
                       class="nav-link text-white d-flex align-items-center px-3 py-2.5 rounded-3 hover-link"
                       onclick="facultySectionDisplay()">
                        <i class="bi bi-building me-3 fs-5"></i> Faculties
                    </a>
                </li>
                <li class="nav-item">
                    <a href="javascript:void(0)" id="candidatesSectionLink"
                       class="nav-link text-white d-flex align-items-center px-3 py-2.5 rounded-3 hover-link"
                       onclick="candidateSectionDisplay()">
                        <i class="bi bi-person-badge me-3 fs-5"></i> Candidates
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link text-white d-flex align-items-center px-3 py-2.5 rounded-3 hover-link">
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

    <!-- Main Content Area -->
    <div class="flex-grow-1 min-vh-100 d-flex flex-column" style="margin-left: 260px; background-color: #f8f9fa;">

        <nav class="navbar navbar-expand-lg navbar-white bg-white border-bottom px-4 py-3 sticky-top shadow-sm">
            <div class="container-fluid p-0">
                <span class="navbar-brand fw-bold m-0" style="color: #091c3d;">Management Console</span>

                <div class="d-flex align-items-center ms-auto">
                    <div class="d-flex align-items-center me-3">
                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 38px; height: 38px;">
                            <i class="bi bi-person text-secondary"></i>
                        </div>
                        <div>
                            <div class="fw-semibold small text-dark leading-tight">{{ Auth::user()->name ?? 'Admin' }}</div>
                            <span class="text-muted" style="font-size: 0.75rem;">System Administrator</span>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <main class="p-4 p-md-5 flex-grow-1">
            <div class="row g-4">

                <!-- Stats Cards -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
                        <div class="mb-3 text-primary"><i class="bi bi-people fs-4"></i></div>
                        <h6 class="text-muted fw-semibold mb-1 small">Total Registered Voters</h6>
                        <h3 class="fw-bold m-0" style="color: #091c3d;">0</h3>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
                        <div class="mb-3 text-success"><i class="bi bi-check2-circle fs-4"></i></div>
                        <h6 class="text-muted fw-semibold mb-1 small">Votes Casted</h6>
                        <h3 class="fw-bold m-0" style="color: #091c3d;">0</h3>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
                        <div class="mb-3 text-warning"><i class="bi bi-person-video2 fs-4"></i></div>
                        <h6 class="text-muted fw-semibold mb-1 small">Total Candidates</h6>
                        <h3 class="fw-bold m-0" style="color: #091c3d;">0</h3>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
                        <div class="mb-3 text-danger"><i class="bi bi-clock-history fs-4"></i></div>
                        <h6 class="text-muted fw-semibold mb-1 small">System Status</h6>
                        <h3 class="fw-bold m-0 fs-5 text-success">Active</h3>
                    </div>
                </div>

                {{-- @can('manage_election') --}}
                <!-- Faculty Management Section -->
                <div class="col-12 mt-5 d-none" id="facultySection">
                    <div class="card border-0 shadow-sm rounded-4 p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="fw-bold m-0" style="color: #091c3d;">Registered Faculties</h4>
                            <button class="btn text-white rounded-3 px-4" style="background-color: #091c3d;" id="toggleFacultyBtn">
                                <i class="bi bi-plus-lg me-2"></i>Create New Faculty
                            </button>
                        </div>

                        <!-- Add Faculty Form -->
                        <form class="d-none border p-4 rounded-3 mb-4 bg-light shadow-sm" id="createFacultyForm" onsubmit="createFaculty(event)">
                            @csrf
                            <div class="mb-3">
                                <label for="name" class="form-label fw-semibold text-dark">Faculty Name</label>
                                <input type="text" class="form-control" id="name" name="name" required placeholder="e.g., Faculty of Engineering">
                            </div>
                            <p class="text text-danger mb-3 d-none" id="createFacultyErr"></p>
                            <button type="submit" class="btn text-white px-4" style="background-color: #f5951b;">Save Faculty</button>
                        </form>

                        <!-- Dynamic Table -->
                        <div class="table-responsive">
                            <table class="table table-hover align-middle border-0">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col" class="py-3 px-4" style="width: 100px;">S/No</th>
                                        <th scope="col" class="py-3 px-4">Faculty Name</th>
                                        <th scope="col" class="py-3 px-4" style="width: 160px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="facultyTbody">
                                    <!-- Rendered dynamically -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                {{-- @endcan --}}

                <!-- Candidates Overview (read-only, hidden until sidebar link clicked) -->
                <div class="col-12 mt-5 d-none" id="candidatesSection">
                    <div class="card border-0 shadow-sm rounded-4 p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="fw-bold m-0" style="color: #091c3d;">Candidates Overview</h4>
                            <a href="{{ route('candidates.index') }}" class="btn text-white rounded-3 px-4"
                               style="background-color: #091c3d;">
                                <i class="bi bi-pencil-square me-2"></i>Manage Candidates
                            </a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle border-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="py-3 px-4" style="width: 60px;">#</th>
                                        <th class="py-3 px-4" style="width: 65px;">Photo</th>
                                        <th class="py-3 px-4">Name</th>
                                        <th class="py-3 px-4">Position</th>
                                        <th class="py-3 px-4">Faculty / Program</th>
                                    </tr>
                                </thead>
                                <tbody id="dashboardCandidatesTbody">
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="bi bi-arrow-repeat me-1"></i>Loading...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Edit Faculty Modal -->
                <div class="modal fade" id="editFacultyModal" tabindex="-1" aria-labelledby="editFacultyModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content rounded-4 border-0 shadow">
                            <div class="modal-header border-0 pb-0">
                                <h5 class="modal-title fw-bold" id="editFacultyModalLabel" style="color: #091c3d;">Edit Faculty</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            </div>
                            <div class="modal-body pt-2">
                                <form id="editFacultyForm" onsubmit="updateFaculty(event)">
                                    <input type="hidden" id="editFacultyId">
                                    <div class="mb-3">
                                        <label for="editFacultyName" class="form-label fw-semibold text-dark">Faculty Name</label>
                                        <input type="text" class="form-control" id="editFacultyName" required>
                                    </div>
                                    <p class="text text-danger mb-3 d-none" id="editFacultyErr"></p>
                                    <div class="d-flex justify-content-end" style="gap: 0.5rem;">
                                        <button type="button" class="btn btn-secondary rounded-3" data-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn text-white px-4 rounded-3" style="background-color: #f5951b;">Save Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </main>

    </div>
</div>

<style>
    body {
        background-color: #f8f9fa;
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        overflow-x: hidden;
    }
    .active-link {
        background-color: #f5951b !important;
        color: #091c3d !important;
        font-weight: 600;
    }
    .hover-link:hover {
        background-color: rgba(255, 255, 255, 0.08);
    }
    .hover-logout:hover {
        background-color: rgba(220, 53, 69, 0.1);
    }
</style>

<script>
    function facultySectionDisplay() {
        const section      = document.getElementById('facultySection');
        const facultyLink  = document.getElementById('facultySectionLink');
        const dashLink     = document.getElementById('dashboardLink');
        const isHidden     = section.classList.contains('d-none');

        section.classList.toggle('d-none', !isHidden);

        facultyLink.classList.toggle('active-link', isHidden);
        facultyLink.classList.toggle('hover-link', !isHidden);
        dashLink.classList.toggle('active-link', !isHidden);
        dashLink.classList.toggle('hover-link', isHidden);

        if (isHidden) loadFaculties();
    }

    function toggleFacultyForm() {
        document.getElementById('toggleFacultyBtn').addEventListener('click', function() {
            document.getElementById('createFacultyForm').classList.toggle('d-none');
        });
    }

    function appendFacultyRow(id, name, index) {
        const tbody = document.getElementById('facultyTbody');
        const newRow = document.createElement('tr');
        newRow.setAttribute('data-id', id);
        newRow.innerHTML = `
            <td class="px-4 fw-semibold text-secondary">${index}</td>
            <td class="px-4 text-dark fw-medium" data-name="${escapeAttr(name)}">${escapeHtml(name)}</td>
            <td class="px-4">
                <button class="btn btn-sm btn-outline-primary me-2 rounded-3" onclick="openEditModal(${id}, this.closest('tr'))">
                    <i class="bi bi-pencil-fill me-1"></i>Edit
                </button>
                <button class="btn btn-sm btn-outline-danger rounded-3" onclick="deleteFaculty(${id}, this.closest('tr'))">
                    <i class="bi bi-trash-fill me-1"></i>Delete
                </button>
            </td>
        `;
        tbody.appendChild(newRow);
    }

    function escapeHtml(str) {
        const d = document.createElement('div');
        d.appendChild(document.createTextNode(str));
        return d.innerHTML;
    }

    function escapeAttr(str) {
        return str.replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/'/g,'&#39;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }

    function openEditModal(id, row) {
        const name = row.querySelector('[data-name]').getAttribute('data-name');
        document.getElementById('editFacultyId').value = id;
        document.getElementById('editFacultyName').value = name;
        document.getElementById('editFacultyErr').classList.add('d-none');
        $('#editFacultyModal').modal('show');
    }

    function updateFaculty(event) {
        event.preventDefault();
        const id = document.getElementById('editFacultyId').value;
        const name = document.getElementById('editFacultyName').value;
        const errElem = document.getElementById('editFacultyErr');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        fetch(`/faculties/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ name })
        })
        .then(async response => {
            const isJson = response.headers.get('content-type')?.includes('application/json');
            const data = isJson ? await response.json() : null;
            if (!response.ok) {
                if (response.status === 422 && data?.errors?.name) throw new Error(data.errors.name[0]);
                throw new Error(data?.message || `Error (${response.status})`);
            }
            return data;
        })
        .then(data => {
            if (data && data.success) {
                const row = document.querySelector(`tr[data-id="${id}"]`);
                if (row) {
                    const nameCell = row.querySelector('[data-name]');
                    nameCell.setAttribute('data-name', escapeAttr(data.faculty.name));
                    nameCell.textContent = data.faculty.name;
                }
                $('#editFacultyModal').modal('hide');
            } else {
                errElem.textContent = 'An unexpected error occurred.';
                errElem.classList.remove('d-none');
            }
        })
        .catch(err => {
            errElem.textContent = err.message;
            errElem.classList.remove('d-none');
        });
    }

    function deleteFaculty(id, row) {
        if (!confirm('Are you sure you want to delete this faculty?')) return;
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        fetch(`/faculties/${id}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(async response => {
            const isJson = response.headers.get('content-type')?.includes('application/json');
            const data = isJson ? await response.json() : null;
            if (!response.ok) throw new Error(data?.message || `Error (${response.status})`);
            return data;
        })
        .then(data => {
            if (data && data.success) {
                row.remove();
                document.querySelectorAll('#facultyTbody tr').forEach((r, i) => {
                    r.cells[0].textContent = i + 1;
                });
            }
        })
        .catch(err => alert('Delete failed: ' + err.message));
    }

    function loadFaculties() {
        fetch("{{ route('faculties.index') }}", {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`Auth or Permission Error (Status: ${response.status})`);
            }
            return response.json();
        })
        .then(data => {
            if(data.success && data.faculties) {
                const tbody = document.getElementById('facultyTbody');
                tbody.innerHTML = '';
                data.faculties.forEach((faculty, idx) => {
                    appendFacultyRow(faculty.id, faculty.name, idx + 1);
                });
            }
        })
        .catch(err => {
            console.error('Error fetching data:', err);
        });
    }

    function createFaculty(event) {
        event.preventDefault();
        const nameInput = document.getElementById('name');
        const name = nameInput.value;
        const errElem = document.getElementById('createFacultyErr');
        errElem.classList.add('d-none');

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        fetch("{{ route('faculties.store') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ name })
        })
        .then(async response => {
            const isJson = response.headers.get('content-type')?.includes('application/json');
            const data = isJson ? await response.json() : null;

            if (!response.ok) {
                if (response.status === 422 && data?.errors?.name) {
                    throw new Error(data.errors.name[0]);
                }
                throw new Error(data?.message || `Permission or Server Error (${response.status})`);
            }
            return data;
        })
        .then(data => {
            if(data && data.success){
                const currentRowsCount = document.getElementById('facultyTbody').children.length;
                appendFacultyRow(data.faculty.id, data.faculty.name, currentRowsCount + 1);

                document.getElementById('createFacultyForm').reset();
                document.getElementById('createFacultyForm').classList.add('d-none');
            } else {
                errElem.textContent = 'An unexpected error occurred.';
                errElem.classList.remove('d-none');
            }
        })
        .catch(err => {
            errElem.textContent = err.message;
            errElem.classList.remove('d-none');
        });
    }

    toggleFacultyForm();
    loadFaculties();

    // ─── Candidates overview (read-only) ──────────────────────────────────
    const DASH_POSITION_LABELS = {
        president: 'President', faculty_rep: 'Faculty Representative',
        senator: 'Senator', class_rep: 'Class Representative',
    };
    const DASH_BADGE_STYLES = {
        president:   'background:#0d6efd;color:#fff;',
        faculty_rep: 'background:#198754;color:#fff;',
        senator:     'background:#fd7e14;color:#fff;',
        class_rep:   'background:#0dcaf0;color:#fff;',
    };

    function loadDashboardCandidates() {
        fetch("{{ route('candidates.index') }}", {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            const tbody = document.getElementById('dashboardCandidatesTbody');
            tbody.innerHTML = '';
            if (!data.success || !data.candidates.length) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-4">No candidates registered yet.</td></tr>';
                return;
            }
            const storageBase = '{{ asset("storage") }}';
            data.candidates.forEach((c, i) => {
                const type  = c.position?.type || '';
                const label = DASH_POSITION_LABELS[type] || type;
                const badge = DASH_BADGE_STYLES[type] || '';
                let affil = '—';
                if (c.position?.faculty)  affil = c.position.faculty.name;
                else if (c.position?.program) affil = c.position.program.name;

                const avatar = c.image
                    ? `<img src="${storageBase}/${c.image}" style="width:40px;height:40px;border-radius:50%;object-fit:cover;border:2px solid #e9ecef;" onerror="this.style.display='none'">`
                    : `<div style="width:40px;height:40px;border-radius:50%;background:#e9ecef;display:inline-flex;align-items:center;justify-content:center;"><i class="bi bi-person-fill text-secondary"></i></div>`;

                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="px-4 fw-semibold text-secondary">${i + 1}</td>
                    <td class="px-4">${avatar}</td>
                    <td class="px-4 fw-medium text-dark">${c.name}</td>
                    <td class="px-4"><span class="badge rounded-pill px-3 py-1" style="${badge}font-size:0.78rem;">${label}</span></td>
                    <td class="px-4 text-muted small">${affil}</td>
                `;
                tbody.appendChild(tr);
            });
        })
        .catch(() => {
            document.getElementById('dashboardCandidatesTbody').innerHTML =
                '<tr><td colspan="5" class="text-center text-muted py-4">Could not load candidates.</td></tr>';
        });
    }

    function candidateSectionDisplay() {
        const section       = document.getElementById('candidatesSection');
        const candidateLink = document.getElementById('candidatesSectionLink');
        const dashLink      = document.getElementById('dashboardLink');
        const isHidden      = section.classList.contains('d-none');

        section.classList.toggle('d-none', !isHidden);

        candidateLink.classList.toggle('active-link', isHidden);
        candidateLink.classList.toggle('hover-link', !isHidden);
        dashLink.classList.toggle('active-link', !isHidden);
        dashLink.classList.toggle('hover-link', isHidden);

        if (isHidden) loadDashboardCandidates();
    }

    // Auto-open a section when arriving from another page via ?section= query param
    (function () {
        const section = new URLSearchParams(window.location.search).get('section');
        if (section === 'faculties')   facultySectionDisplay();
        if (section === 'candidates')  candidateSectionDisplay();
    })();
</script>
@endsection
