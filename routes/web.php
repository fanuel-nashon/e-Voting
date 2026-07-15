<?php

use App\Http\Controllers\AcceptanceController;
use App\Http\Controllers\Admin\CandidateController;
use App\Http\Controllers\Admin\FacultyController;
use App\Http\Controllers\Admin\ProgramController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ElectionAdminController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\VoterController;
use App\Http\Controllers\VoterRegistrationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Guest routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::view('/', 'auth.login')->name('login');
    Route::post('/login.submit', [LoginController::class, 'login'])->name('login.submit')->middleware('throttle:6,1');

    Route::view('/password/reset', 'auth.reset-password')->name('password.reset');
    Route::post('/reset-password', [LoginController::class, 'resetPassword'])->name('reset.password')->middleware('throttle:3,1');

    Route::view('/auth-token', 'auth.token')->name('token');
    Route::view('/enter-token', 'auth.enter-token')->name('enterToken');
    Route::post('/change-password', [LoginController::class, 'changePassword'])->name('change.password')->middleware('throttle:6,1');

    // Voter self-registration
    Route::get('/register/voter', [VoterRegistrationController::class, 'showForm'])->name('voter.register');
    Route::post('/register/voter', [VoterRegistrationController::class, 'submit'])->name('voter.register.submit');

    // OTP step (session-gated, not auth-gated)
    Route::get('/voter/otp', [LoginController::class, 'otpForm'])->name('voter.otp');
    Route::post('/voter/otp', [LoginController::class, 'verifyOtp'])->name('voter.otp.verify')->middleware('throttle:6,1');
});

/*
|--------------------------------------------------------------------------
| Candidate acceptance — public token-gated
|--------------------------------------------------------------------------
*/
Route::get('/acceptance/{token}', [AcceptanceController::class, 'form'])->name('acceptance.form');
Route::post('/acceptance/{token}', [AcceptanceController::class, 'submit'])->name('acceptance.submit');

/*
|--------------------------------------------------------------------------
| Authenticated — any logged-in user
|--------------------------------------------------------------------------
*/
Route::middleware('check.permission')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

/*
|--------------------------------------------------------------------------
| Voter routes (vote permission)
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
| Election management (manage_election permission)
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
    Route::get('/election/email-logs', [ElectionAdminController::class, 'pollEmailLogs'])->name('election.email-logs');
    Route::get('/election/stats', [ElectionAdminController::class, 'pollStats'])->name('election.stats');
    Route::post('/election/release', [ElectionAdminController::class, 'releaseResults'])->name('election.release');
    Route::post('/election/acceptances/{acceptance}/verify', [ElectionAdminController::class, 'verifyAcceptance'])->name('election.verify');
    Route::post('/election/publish', [ElectionAdminController::class, 'publishResults'])->name('election.publish');

    // Voter registration approvals
    Route::get('/voter-registrations/pending', [VoterRegistrationController::class, 'pendingList'])->name('voter.registrations.pending');
    Route::post('/voter-registrations/{registration}/approve', [VoterRegistrationController::class, 'approve'])->name('voter.registrations.approve');
    Route::post('/voter-registrations/{registration}/reject', [VoterRegistrationController::class, 'reject'])->name('voter.registrations.reject');

    // Voting reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [ReportController::class, 'exportCsv'])->name('reports.export');
});

/*
|--------------------------------------------------------------------------
| User management (manage_users permission)
|--------------------------------------------------------------------------
*/
Route::middleware('check.permission:manage_users')->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::patch('/users/{user}/faculty', [UserController::class, 'assignFaculty'])->name('users.assign-faculty');
    Route::post('/users/{user}/student', [UserController::class, 'attachStudent'])->name('users.attach-student');
});
