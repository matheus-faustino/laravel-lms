<?php

use App\Http\Controllers\Api\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Api\Admin\EnrollmentController as AdminEnrollmentController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\Auth\AuthenticationController;
use App\Http\Controllers\Api\Student\CourseController as StudentCourseController;
use App\Http\Controllers\Api\Student\EnrollmentController as StudentEnrollmentController;
use App\Http\Controllers\Api\Student\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthenticationController::class, 'login']);
    Route::post('register', [AuthenticationController::class, 'register']);
    Route::post('forgot-password', [AuthenticationController::class, 'forgotPassword']);
    Route::post('reset-password', [AuthenticationController::class, 'resetPassword'])->name('password.reset');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthenticationController::class, 'logout']);
        Route::get('me', [AuthenticationController::class, 'me']);
    });
});

Route::prefix('admin')->middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::apiResource('users', UserController::class);
    Route::apiResource('courses', AdminCourseController::class);
    Route::put('courses/{id}/toggle-status', [AdminCourseController::class, 'toggleStatus']);

    Route::get('enrollments/stats', [AdminEnrollmentController::class, 'stats']);
    Route::apiResource('enrollments', AdminEnrollmentController::class)->except(['update']);
});

Route::prefix('student')->middleware(['auth:sanctum', 'role:student'])->group(function () {
    Route::get('profile', [ProfileController::class, 'show']);
    Route::put('profile', [ProfileController::class, 'update']);
    Route::put('change-password', [ProfileController::class, 'changePassword']);
    Route::get('courses', [StudentCourseController::class, 'index']);
    Route::get('courses/{id}', [StudentCourseController::class, 'show']);
    Route::apiResource('enrollments', StudentEnrollmentController::class)->except(['update']);
});
