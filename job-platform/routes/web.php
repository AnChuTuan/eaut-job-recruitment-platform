<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ==========================================
// ROUTES DÀNH CHO ADMIN (Thành viên: Nguyên)
// Quản lý người dùng (UC6) & Dashboard thống kê
// ==========================================
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Middleware\AdminMiddleware;

Route::middleware(['auth', AdminMiddleware::class])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');

    // A1. Moderate Account (Warn, Suspend, Ban, Reactivate)
    Route::post('/users/{user}/moderate', [UserController::class, 'moderate'])->name('users.moderate');

    // A2. Assist Account Recovery: Send Password Reset Link
    Route::post('/users/{user}/password-reset-link', [UserController::class, 'sendPasswordResetLink'])->name('users.password-reset-link');

    // A3. Assist Account Recovery: Manual Verification
    Route::post('/users/{user}/manual-verify', [UserController::class, 'manualVerify'])->name('users.manual-verify');

    // A4. Assist Account Recovery: Update Login Email
    Route::patch('/users/{user}/update-email', [UserController::class, 'updateLoginEmail'])->name('users.update-email');
});

require __DIR__.'/auth.php';
