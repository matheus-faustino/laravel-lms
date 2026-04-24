<?php

use App\Enums\RoleEnum;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $role = Auth::user()->role->value;

    switch ($role) {
        case RoleEnum::ADMIN->value:
            return redirect()->route('admin.dashboard.index');
            break;

        case RoleEnum::USER->value:
            abort(501, __('shared/errors.not_implemented'));
            break;

        default:
            abort(403);
            break;
    }
})->middleware('auth');

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

Route::get('/login', [LoginController::class, 'form'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit')->middleware('guest');

Route::get('/register', [RegisterController::class, 'form'])->name('register')->middleware('guest');
Route::post('/register', [RegisterController::class, 'register'])->name('register.submit')->middleware('guest');

Route::get('/forgot-password', [ResetPasswordController::class, 'forgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [ResetPasswordController::class, 'sendPasswordResetMail']);
Route::get('/reset-password/{token}', [ResetPasswordController::class, 'resetPasswordForm'])->name('password.reset');
Route::post('/reset-password', [ResetPasswordController::class, 'resetPassword']);

Route::middleware('auth', 'role:admin')->prefix('/admin')->as('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');

    Route::controller(UserController::class)->prefix('/users')->as('users.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/create', 'store')->name('store');
        Route::prefix('{userId}')->group(function () {
            Route::get('/edit', 'edit')->name('edit');
            Route::put('/edit', 'update')->name('update');
            Route::delete('/delete', 'delete')->name('delete');
        });
    });

    Route::controller(CategoryController::class)->prefix('/categories')->as('categories.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/create', 'store')->name('store');
        Route::prefix('{categoryId}')->group(function () {
            Route::get('/edit', 'edit')->name('edit');
            Route::put('/edit', 'update')->name('update');
            Route::delete('/delete', 'delete')->name('delete');
        });
    });

    Route::controller(CourseController::class)->prefix('/courses')->as('courses.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/create', 'store')->name('store');
        Route::prefix('{courseId}')->group(function () {
            Route::get('/edit', 'edit')->name('edit');
            Route::put('/edit', 'update')->name('update');
            Route::delete('/delete', 'delete')->name('delete');
            Route::patch('/publish', 'publish')->name('publish');
        });
    });
});
