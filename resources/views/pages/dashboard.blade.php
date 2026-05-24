@extends('layouts.app')

@section('title', 'Admin Dashboard - e-Voting')

@section('content')

<div class="container-fluid p-0 d-flex min-vh-100 flex-column flex-md-row">

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
                    <a href="#" class="nav-link text-white d-flex align-items-center px-3 py-2.5 rounded-3 active-link">
                        <i class="bi bi-speedometer2 me-3 fs-5"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link text-white d-flex align-items-center px-3 py-2.5 rounded-3 hover-link">
                        <i class="bi bi-people me-3 fs-5"></i> Users / Voters
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link text-white d-flex align-items-center px-3 py-2.5 rounded-3 hover-link">
                        <i class="bi bi-building me-3 fs-5"></i> Faculties
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link text-white d-flex align-items-center px-3 py-2.5 rounded-3 hover-link">
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
            {{-- <form action="{{ route('logout') }}" method="POST" class="d-inline w-100"> --}}
                @csrf
                <button type="submit" class="btn btn-link text-white text-decoration-none d-flex align-items-center px-3 py-2 w-100 rounded-3 hover-logout">
                    <i class="bi bi-box-arrow-left me-3 fs-5 text-danger"></i> Sign Out
                </button>
            </form>
        </div>
    </aside>

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
                            <div class="fw-semibold small text-dark leading-tight">{{ Auth::user()->name }}</div>
                            <span class="text-muted" style="font-size: 0.75rem;">System Administrator</span>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <main class="p-4 p-md-5 flex-grow-1">
            <div class="row g-4">

                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="rounded-3 p-3 bg-primary-subtle text-primary">
                                <i class="bi bi-people fs-4"></i>
                            </div>
                        </div>
                        <h6 class="text-muted fw-semibold mb-1 small">Total Registered Voters</h6>
                        <h3 class="fw-bold m-0" style="color: #091c3d;">0</h3>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="rounded-3 p-3 bg-success-subtle text-success">
                                <i class="bi bi-check2-circle fs-4"></i>
                            </div>
                        </div>
                        <h6 class="text-muted fw-semibold mb-1 small">Votes Casted</h6>
                        <h3 class="fw-bold m-0" style="color: #091c3d;">0</h3>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="rounded-3 p-3 bg-warning-subtle text-warning">
                                <i class="bi bi-person-video2 fs-4"></i>
                            </div>
                        </div>
                        <h6 class="text-muted fw-semibold mb-1 small">Total Candidates</h6>
                        <h3 class="fw-bold m-0" style="color: #091c3d;">0</h3>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="rounded-3 p-3 bg-danger-subtle text-danger">
                                <i class="bi bi-clock-history fs-4"></i>
                            </div>
                        </div>
                        <h6 class="text-muted fw-semibold mb-1 small">System Status</h6>
                        <h3 class="fw-bold m-0 fs-5 text-success">Active</h3>
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
</aside>
@endsection
