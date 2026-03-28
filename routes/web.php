<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('welcome'))->name('landing');
Route::get('/home', fn () => view('home'))->name('home')->middleware('auth');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

Route::get('/login', [LoginController::class, 'form'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit')->middleware('guest');

Route::get('/register', [RegisterController::class, 'form'])->name('register')->middleware('guest');
Route::post('/register', [RegisterController::class, 'register'])->name('register.submit')->middleware('guest');

Route::get('/forgot-password', [ResetPasswordController::class, 'forgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [ResetPasswordController::class, 'sendPasswordResetMail']);
Route::get('/reset-password', [ResetPasswordController::class, 'resetPasswordForm'])->name('password.reset');
Route::post('/reset-password', [ResetPasswordController::class, 'resetPassword']);
