<?php

use App\Http\Controllers\Admin\CandidateController;
use App\Http\Controllers\Admin\FacultyController;
use App\Http\Controllers\Admin\ProgramController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Guest routes — accessible only when NOT logged in
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::view('/', 'auth.login')->name('login');
    Route::post('/login.submit', [LoginController::class, 'login'])->name('login.submit');

    Route::view('/password/reset', 'auth.reset-password')->name('password.reset');
    Route::post('/reset-password', [LoginController::class, 'resetPassword'])->name('reset.password');

    Route::view('/auth-token', 'auth.token')->name('token');
    Route::view('/enter-token', 'auth.enter-token')->name('enterToken');
    Route::post('/change-password', [LoginController::class, 'changePassword'])->name('change.password');
});

/*
|--------------------------------------------------------------------------
| Authenticated routes — any logged-in user
|--------------------------------------------------------------------------
*/
Route::middleware('check.permission')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

/*
|--------------------------------------------------------------------------
| Election management — admin + election_admin (have manage_election)
|--------------------------------------------------------------------------
*/
Route::middleware('check.permission:manage_election')->group(function () {
    Route::view('/dashboard', 'pages.dashboard')->name('dashboard');

    Route::resource('faculties', FacultyController::class);
    Route::resource('programs', ProgramController::class)->except(['create', 'edit', 'show']);
    Route::resource('candidates', CandidateController::class)->except(['create', 'edit', 'show']);
});

/*
|--------------------------------------------------------------------------
| User management — admin only (has manage_users)
|--------------------------------------------------------------------------
*/
Route::middleware('check.permission:manage_users')->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});

/*
|--------------------------------------------------------------------------
| Voting — voters only (have vote)
|--------------------------------------------------------------------------
*/
Route::middleware('check.permission:vote')->group(function () {
    // voting routes go here
});
