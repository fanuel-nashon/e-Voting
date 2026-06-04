<?php

use App\Http\Controllers\Admin\CandidateController;
use App\Http\Controllers\Admin\FacultyController;
use App\Http\Controllers\Admin\ProgramController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AcceptanceController;
use App\Http\Controllers\ElectionAdminController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\VoterController;
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
| Candidate acceptance — public (token-gated, no auth required)
|--------------------------------------------------------------------------
*/
Route::get('/acceptance/{token}', [AcceptanceController::class, 'form'])->name('acceptance.form');
Route::post('/acceptance/{token}', [AcceptanceController::class, 'submit'])->name('acceptance.submit');

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
| Voter routes — voters only (have vote permission)
|--------------------------------------------------------------------------
*/
Route::middleware('check.permission:vote')->group(function () {
    Route::get('/voter', [VoterController::class, 'dashboard'])->name('voter.dashboard');
    Route::post('/voter/review', [VoterController::class, 'review'])->name('voter.review');
    Route::post('/voter/confirm', [VoterController::class, 'confirm'])->name('voter.confirm');
    Route::get('/voter/done', [VoterController::class, 'done'])->name('voter.done');
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

    // Election control centre
    Route::get('/election', [ElectionAdminController::class, 'dashboard'])->name('election.dashboard');
    Route::post('/election/timeline', [ElectionAdminController::class, 'saveTimeline'])->name('election.timeline');
    Route::get('/election/logs', [ElectionAdminController::class, 'pollLogs'])->name('election.logs');
    Route::get('/election/stats', [ElectionAdminController::class, 'pollStats'])->name('election.stats');
    Route::post('/election/release', [ElectionAdminController::class, 'releaseResults'])->name('election.release');
    Route::post('/election/acceptances/{acceptance}/verify', [ElectionAdminController::class, 'verifyAcceptance'])->name('election.verify');
    Route::post('/election/publish', [ElectionAdminController::class, 'publishResults'])->name('election.publish');
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
