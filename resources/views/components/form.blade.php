@extends('layouts.app')

@section('content')

    <div class="container">
        <div class="d-flex justify-content-center align-items-center">
            <div class="card border-0 shadow-sm rounded-4 p-4" style="width: 400px;">
                <div class="card-body">
                    <h5 class="card-title mb-4">Create Student Account</h5>
                    <form>
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Create Account</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
