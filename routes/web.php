<?php

use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'auth.login')->name('login');

Route::view('dashboard', 'pages.dashboard')->name('dashboard');

Route::post('login.submit',[LoginController::class, 'login'])->name('login.submit');

Route::view('/password/reset', 'auth.reset-password')->name('password.reset');

Route::post('/reset-password', [LoginController::class, 'resetPassword'])->name('reset.password');

Route::view('/auth-token', 'auth.token')->name('token');

Route::view('/enter-token', 'auth.enter-token')->name('enterToken');

Route::post('/change-password', [LoginController::class, 'changePassword'])->name('change.password');
