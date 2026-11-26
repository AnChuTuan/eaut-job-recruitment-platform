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

// === TÀI: UC7 - Post Jobs (Employer) ===
Route::middleware('auth')->prefix('employer/jobs')->name('employer.jobs.')->group(function () {
    Route::get('/', [\App\Http\Controllers\EmployerJobController::class, 'index'])->name('index');
    Route::get('/create', [\App\Http\Controllers\EmployerJobController::class, 'create'])->name('create');
    Route::post('/', [\App\Http\Controllers\EmployerJobController::class, 'store'])->name('store');
    Route::get('/{job}/edit', [\App\Http\Controllers\EmployerJobController::class, 'edit'])->name('edit');
    Route::put('/{job}', [\App\Http\Controllers\EmployerJobController::class, 'update'])->name('update');
    Route::delete('/{job}', [\App\Http\Controllers\EmployerJobController::class, 'destroy'])->name('destroy');
    Route::get('/{job}/preview', [\App\Http\Controllers\EmployerJobController::class, 'preview'])->name('preview');
    Route::patch('/{job}/submit', [\App\Http\Controllers\EmployerJobController::class, 'submit'])->name('submit');
});

// === TÀI: UC8 - Approve Job Posts (Admin) ===
Route::middleware('auth')->prefix('admin/moderation')->name('admin.moderation.')->group(function () {
    Route::get('/', [\App\Http\Controllers\AdminModerationController::class, 'index'])->name('index');
    Route::get('/{job}', [\App\Http\Controllers\AdminModerationController::class, 'show'])->name('show');
    Route::patch('/{job}/approve', [\App\Http\Controllers\AdminModerationController::class, 'approve'])->name('approve');
    Route::patch('/{job}/reject', [\App\Http\Controllers\AdminModerationController::class, 'reject'])->name('reject');
    Route::patch('/{job}/request-revision', [\App\Http\Controllers\AdminModerationController::class, 'requestRevision'])->name('request-revision');
    Route::patch('/{job}/ban', [\App\Http\Controllers\AdminModerationController::class, 'ban'])->name('ban');
});
// === END TÀI ===

require __DIR__.'/auth.php';
