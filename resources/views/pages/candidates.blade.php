@extends('layouts.app')

@section('title', 'Candidates - e-Voting Admin')

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
                    <a href="#" class="nav-link text-white d-flex align-items-center px-3 py-2 rounded-3 hover-link">
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
                    <a href="{{ route('candidates.index') }}" class="nav-link text-white d-flex align-items-center px-3 py-2 rounded-3 active-link">
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
    <div class="flex-grow-1 d-flex flex-column" style="margin-left: 260px; background-color: #f8f9fa;">

        <!-- Top Navbar -->
        <nav class="navbar navbar-expand-lg navbar-white bg-white border-bottom px-4 py-3 sticky-top shadow-sm">
            <div class="container-fluid p-0">
                <span class="navbar-brand fw-bold m-0" style="color: #091c3d;">Candidate Management</span>
                <div class="d-flex align-items-center ms-auto">
                    <div class="d-flex align-items-center">
                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-2"
                             style="width: 38px; height: 38px;">
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

        <!-- Page Content -->
        <div class="p-4">

            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold m-0" style="color: #091c3d;">Registered Candidates</h4>
                    <p class="text-muted small m-0 mt-1">Manage presidential, faculty, senate, and class representatives</p>
                </div>
                <button class="btn text-white rounded-3 px-4 py-2" style="background-color: #091c3d;"
                        onclick="openCreateModal()">
                    <i class="bi bi-plus-lg me-2"></i>Add Candidate
                </button>
            </div>

            <!-- Candidates Table -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="py-3 px-4" style="width: 60px;">#</th>
                                    <th class="py-3 px-4" style="width: 70px;">Photo</th>
                                    <th class="py-3 px-4">Name</th>
                                    <th class="py-3 px-4">Position</th>
                                    <th class="py-3 px-4">Faculty / Program</th>
                                    <th class="py-3 px-4" style="width: 160px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="candidatesTbody">
                                <tr id="loadingRow">
                                    <td colspan="6" class="text-center text-muted py-5">
                                        <i class="bi bi-arrow-repeat me-2"></i>Loading candidates...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- ===================== Create Modal ===================== -->
<div class="modal fade" id="createCandidateModal" tabindex="-1" aria-labelledby="createCandidateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" style="color: #091c3d;">Add New Candidate</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body pt-2">
                <form id="createCandidateForm" onsubmit="createCandidate(event)" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Position Type <span class="text-danger">*</span></label>
                                <select class="form-control" name="position_type" id="createPositionType" required
                                        onchange="handlePositionTypeChange(this, 'create')">
                                    <option value="">-- Select Position --</option>
                                    <option value="president">President</option>
                                    <option value="faculty_rep">Faculty Representative</option>
                                    <option value="senator">Senator</option>
                                    <option value="class_rep">Class Representative</option>
                                </select>
                            </div>
                            <div class="mb-3 d-none" id="createFacultyGroup">
                                <label class="form-label fw-semibold">Faculty <span class="text-danger">*</span></label>
                                <select class="form-control" name="faculty_id" id="createFacultyId">
                                    <option value="">-- Select Faculty --</option>
                                </select>
                            </div>
                            <div class="mb-3 d-none" id="createProgramGroup">
                                <label class="form-label fw-semibold">Program <span class="text-danger">*</span></label>
                                <select class="form-control" name="program_id" id="createProgramId">
                                    <option value="">-- Select Program --</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Candidate Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" required
                                       placeholder="e.g., John Doe">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Photo <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" name="image" id="createImageInput"
                                       accept="image/jpeg,image/png,image/webp" required
                                       onchange="previewImage(this, 'createImagePreview')">
                                <small class="text-muted">JPG, PNG or WEBP — max 2 MB</small>
                            </div>
                            <div class="text-center mt-2">
                                <img id="createImagePreview" src="" alt="Preview"
                                     class="d-none rounded-3 border"
                                     style="max-height: 160px; max-width: 100%; object-fit: cover;">
                            </div>
                        </div>
                    </div>
                    <p class="text-danger mb-3 d-none small" id="createCandidateErr"></p>
                    <div class="d-flex justify-content-end" style="gap: 0.5rem;">
                        <button type="button" class="btn btn-secondary rounded-3" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn text-white px-4 rounded-3" style="background-color: #f5951b;">
                            <i class="bi bi-check-lg me-1"></i>Save Candidate
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ===================== Edit Modal ===================== -->
<div class="modal fade" id="editCandidateModal" tabindex="-1" aria-labelledby="editCandidateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" style="color: #091c3d;">Edit Candidate</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body pt-2">
                <form id="editCandidateForm" onsubmit="updateCandidate(event)" enctype="multipart/form-data">
                    <input type="hidden" id="editCandidateId">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Position Type <span class="text-danger">*</span></label>
                                <select class="form-control" name="position_type" id="editPositionType" required
                                        onchange="handlePositionTypeChange(this, 'edit')">
                                    <option value="">-- Select Position --</option>
                                    <option value="president">President</option>
                                    <option value="faculty_rep">Faculty Representative</option>
                                    <option value="senator">Senator</option>
                                    <option value="class_rep">Class Representative</option>
                                </select>
                            </div>
                            <div class="mb-3 d-none" id="editFacultyGroup">
                                <label class="form-label fw-semibold">Faculty <span class="text-danger">*</span></label>
                                <select class="form-control" name="faculty_id" id="editFacultyId">
                                    <option value="">-- Select Faculty --</option>
                                </select>
                            </div>
                            <div class="mb-3 d-none" id="editProgramGroup">
                                <label class="form-label fw-semibold">Program <span class="text-danger">*</span></label>
                                <select class="form-control" name="program_id" id="editProgramId">
                                    <option value="">-- Select Program --</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Candidate Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" id="editCandidateName" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Photo <small class="text-muted">(leave blank to keep current)</small></label>
                                <input type="file" class="form-control" name="image" id="editImageInput"
                                       accept="image/jpeg,image/png,image/webp"
                                       onchange="previewImage(this, 'editImagePreview')">
                                <small class="text-muted">JPG, PNG or WEBP — max 2 MB</small>
                            </div>
                            <div class="text-center mt-2">
                                <img id="editImagePreview" src="" alt="Current Photo"
                                     class="rounded-3 border"
                                     style="max-height: 160px; max-width: 100%; object-fit: cover;">
                            </div>
                        </div>
                    </div>
                    <p class="text-danger mb-3 d-none small" id="editCandidateErr"></p>
                    <div class="d-flex justify-content-end" style="gap: 0.5rem;">
                        <button type="button" class="btn btn-secondary rounded-3" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn text-white px-4 rounded-3" style="background-color: #f5951b;">
                            <i class="bi bi-check-lg me-1"></i>Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    body { background-color: #f8f9fa; font-family: 'Inter', system-ui, -apple-system, sans-serif; }
    .active-link { background-color: #f5951b !important; color: #091c3d !important; font-weight: 600; }
    .hover-link:hover { background-color: rgba(255,255,255,0.08); transition: background 0.2s; }
    .hover-logout:hover { background-color: rgba(220,53,69,0.12); transition: background 0.2s; }
    .badge-president  { background-color: #0d6efd; color: #fff; }
    .badge-faculty_rep { background-color: #198754; color: #fff; }
    .badge-senator    { background-color: #fd7e14; color: #fff; }
    .badge-class_rep  { background-color: #0dcaf0; color: #fff; }
    .candidate-avatar {
        width: 46px; height: 46px; border-radius: 50%; object-fit: cover;
        border: 2px solid #e9ecef;
    }
    .avatar-placeholder {
        width: 46px; height: 46px; border-radius: 50%;
        background: #e9ecef; display: inline-flex;
        align-items: center; justify-content: center;
    }
</style>

<script>
    let candidates = @json($candidates);
    const faculties = @json($faculties);
    const programs  = @json($programs);
    const storageBase = '{{ asset("storage") }}';
    const csrfToken   = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    // ─── Dropdown population ────────────────────────────────────────────────
    function populateDropdowns() {
        ['create', 'edit'].forEach(prefix => {
            const fSel = document.getElementById(`${prefix}FacultyId`);
            const pSel = document.getElementById(`${prefix}ProgramId`);

            faculties.forEach(f => {
                const opt = new Option(f.name, f.id);
                fSel.appendChild(opt);
            });
            programs.forEach(p => {
                const label = p.name + (p.faculty ? ` (${p.faculty.name})` : '');
                const opt = new Option(label, p.id);
                pSel.appendChild(opt);
            });
        });
    }

    // ─── Position type toggle ────────────────────────────────────────────────
    function handlePositionTypeChange(sel, prefix) {
        const type = sel.value;
        const needsFaculty  = type === 'faculty_rep' || type === 'senator';
        const needsProgram  = type === 'class_rep';

        const fg = document.getElementById(`${prefix}FacultyGroup`);
        const pg = document.getElementById(`${prefix}ProgramGroup`);

        fg.classList.toggle('d-none', !needsFaculty);
        pg.classList.toggle('d-none', !needsProgram);

        document.getElementById(`${prefix}FacultyId`).required = needsFaculty;
        document.getElementById(`${prefix}ProgramId`).required = needsProgram;
    }

    // ─── Image preview ───────────────────────────────────────────────────────
    function previewImage(input, previewId) {
        const preview = document.getElementById(previewId);
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => {
                preview.src = e.target.result;
                preview.classList.remove('d-none');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    // ─── Render helpers ──────────────────────────────────────────────────────
    function escapeHtml(str) {
        if (!str) return '';
        const d = document.createElement('div');
        d.appendChild(document.createTextNode(str));
        return d.innerHTML;
    }

    const POSITION_LABELS = {
        president:   'President',
        faculty_rep: 'Faculty Representative',
        senator:     'Senator',
        class_rep:   'Class Representative',
    };

    function getAffiliation(c) {
        if (!c.position) return '—';
        if (c.position.faculty) return escapeHtml(c.position.faculty.name);
        if (c.position.program) {
            let label = escapeHtml(c.position.program.name);
            if (c.position.program.faculty) label += ` <span class="text-muted">(${escapeHtml(c.position.program.faculty.name)})</span>`;
            return label;
        }
        return '—';
    }

    function buildAvatarHtml(c) {
        if (!c.image) return `<div class="avatar-placeholder"><i class="bi bi-person-fill text-secondary"></i></div>`;
        return `<img src="${storageBase}/${escapeHtml(c.image)}" alt="${escapeHtml(c.name)}"
                     class="candidate-avatar"
                     onerror="this.outerHTML='<div class=\'avatar-placeholder\'><i class=\'bi bi-person-fill text-secondary\'></i></div>'">`;
    }

    function renderCandidates() {
        const tbody = document.getElementById('candidatesTbody');
        tbody.innerHTML = '';

        if (candidates.length === 0) {
            tbody.innerHTML = `<tr><td colspan="6" class="text-center text-muted py-5">
                <i class="bi bi-person-x fs-3 d-block mb-2"></i>No candidates registered yet.</td></tr>`;
            return;
        }

        candidates.forEach((c, i) => {
            const type  = c.position?.type || '';
            const label = POSITION_LABELS[type] || type;
            const row   = document.createElement('tr');
            row.setAttribute('data-id', c.id);
            row.innerHTML = `
                <td class="px-4 fw-semibold text-secondary">${i + 1}</td>
                <td class="px-4">${buildAvatarHtml(c)}</td>
                <td class="px-4 fw-medium text-dark">${escapeHtml(c.name)}</td>
                <td class="px-4">
                    <span class="badge badge-${type} px-3 py-1 rounded-pill" style="font-size:0.78rem;">${label}</span>
                </td>
                <td class="px-4 text-muted small">${getAffiliation(c)}</td>
                <td class="px-4">
                    <button class="btn btn-sm btn-outline-primary rounded-3 me-1" onclick="openEditModal(${c.id})">
                        <i class="bi bi-pencil-fill"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger rounded-3" onclick="deleteCandidate(${c.id}, this.closest('tr'))">
                        <i class="bi bi-trash-fill"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(row);
        });
    }

    // ─── Create ─────────────────────────────────────────────────────────────
    function openCreateModal() {
        document.getElementById('createCandidateForm').reset();
        document.getElementById('createImagePreview').classList.add('d-none');
        document.getElementById('createFacultyGroup').classList.add('d-none');
        document.getElementById('createProgramGroup').classList.add('d-none');
        document.getElementById('createFacultyId').required = false;
        document.getElementById('createProgramId').required = false;
        document.getElementById('createCandidateErr').classList.add('d-none');
        $('#createCandidateModal').modal('show');
    }

    function createCandidate(event) {
        event.preventDefault();
        const errElem = document.getElementById('createCandidateErr');
        errElem.classList.add('d-none');

        const formData = new FormData(document.getElementById('createCandidateForm'));

        fetch('{{ route("candidates.store") }}', {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken },
            body: formData,
        })
        .then(async res => {
            const data = await res.json();
            if (!res.ok) {
                const msg = data.errors ? Object.values(data.errors).flat()[0] : (data.message || `Error (${res.status})`);
                throw new Error(msg);
            }
            return data;
        })
        .then(data => {
            if (data.success) {
                candidates.push(data.candidate);
                renderCandidates();
                $('#createCandidateModal').modal('hide');
            }
        })
        .catch(err => { errElem.textContent = err.message; errElem.classList.remove('d-none'); });
    }

    // ─── Edit ────────────────────────────────────────────────────────────────
    function openEditModal(id) {
        const c = candidates.find(c => c.id == id);
        if (!c) return;

        document.getElementById('editCandidateId').value    = c.id;
        document.getElementById('editCandidateName').value  = c.name;
        document.getElementById('editImageInput').value     = '';

        const typeSel = document.getElementById('editPositionType');
        typeSel.value = c.position?.type || '';
        handlePositionTypeChange(typeSel, 'edit');

        if (c.position?.faculty_id) document.getElementById('editFacultyId').value = c.position.faculty_id;
        if (c.position?.program_id) document.getElementById('editProgramId').value = c.position.program_id;

        const preview = document.getElementById('editImagePreview');
        preview.src   = c.image ? `${storageBase}/${c.image}` : '';

        document.getElementById('editCandidateErr').classList.add('d-none');
        $('#editCandidateModal').modal('show');
    }

    function updateCandidate(event) {
        event.preventDefault();
        const id      = document.getElementById('editCandidateId').value;
        const errElem = document.getElementById('editCandidateErr');
        errElem.classList.add('d-none');

        const formData = new FormData(document.getElementById('editCandidateForm'));
        formData.append('_method', 'PUT');

        fetch(`/candidates/${id}`, {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken },
            body: formData,
        })
        .then(async res => {
            const data = await res.json();
            if (!res.ok) {
                const msg = data.errors ? Object.values(data.errors).flat()[0] : (data.message || `Error (${res.status})`);
                throw new Error(msg);
            }
            return data;
        })
        .then(data => {
            if (data.success) {
                const idx = candidates.findIndex(c => c.id == id);
                if (idx !== -1) candidates[idx] = data.candidate;
                renderCandidates();
                $('#editCandidateModal').modal('hide');
            }
        })
        .catch(err => { errElem.textContent = err.message; errElem.classList.remove('d-none'); });
    }

    // ─── Delete ──────────────────────────────────────────────────────────────
    function deleteCandidate(id, row) {
        if (!confirm('Delete this candidate? This action cannot be undone.')) return;

        fetch(`/candidates/${id}`, {
            method: 'DELETE',
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken },
        })
        .then(async res => {
            const data = await res.json();
            if (!res.ok) throw new Error(data.message || `Error (${res.status})`);
            return data;
        })
        .then(data => {
            if (data.success) {
                candidates = candidates.filter(c => c.id != id);
                renderCandidates();
            }
        })
        .catch(err => alert('Delete failed: ' + err.message));
    }

    // ─── Init ────────────────────────────────────────────────────────────────
    populateDropdowns();
    renderCandidates();
</script>

@endsection
