<?php

use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'auth.login')->name('login');

Route::view('dashboard', 'pages.dashboard')->name('dashboard');

Route::post('login.submit',[LoginController::class, 'login'])->name('login.submit');

Route::view('/password/reset', 'auth.reset-password')->name('password.reset');
