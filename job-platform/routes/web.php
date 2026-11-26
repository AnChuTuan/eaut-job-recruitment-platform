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

/* ========================================================================= */
/* --- PHẦN PHỤ TRÁCH CỦA ĐOÀN (feature/doan)                                */
/* --- UC1: Search Job & View JD                                             */
/* --- UC2: Apply for Job                                                    */
/* --- UC9: Manage Candidate Profile & CVs                                   */
/* ========================================================================= */

// Tìm kiếm việc làm & Xem chi tiết JD (UC1)
Route::get('/jobs', [\App\Http\Controllers\JobController::class, 'index'])->name('jobs.index');
Route::get('/jobs/{job}', [\App\Http\Controllers\JobController::class, 'show'])->name('jobs.show');

// Nộp đơn ứng tuyển (UC2)
Route::post('/jobs/{job}/apply', [\App\Http\Controllers\ApplicationController::class, 'store'])
    ->middleware(['auth'])
    ->name('jobs.apply');

// Quản lý việc làm đã ứng tuyển (UC2)
Route::get('/my-applications', [\App\Http\Controllers\ApplicationController::class, 'index'])
    ->middleware(['auth'])
    ->name('candidate.applications.index');

// Quản lý Hồ sơ cá nhân ứng viên & CVs (UC9)
Route::middleware(['auth'])->prefix('candidate')->name('candidate.')->group(function () {
    Route::get('/profile', [\App\Http\Controllers\CandidateProfileController::class, 'edit'])->name('profile');
    Route::put('/profile', [\App\Http\Controllers\CandidateProfileController::class, 'update'])->name('profile.update');

    // Quản lý CVs (Tối đa 5 CV) (UC9)
    Route::get('/cvs', [\App\Http\Controllers\CvController::class, 'index'])->name('cvs.index');
    Route::post('/cvs', [\App\Http\Controllers\CvController::class, 'store'])->name('cvs.store');
    Route::post('/cvs/{cv}/default', [\App\Http\Controllers\CvController::class, 'setDefault'])->name('cvs.default');
    Route::delete('/cvs/{cv}', [\App\Http\Controllers\CvController::class, 'destroy'])->name('cvs.destroy');
    Route::get('/cvs/{cv}/download', [\App\Http\Controllers\CvController::class, 'download'])->name('cvs.download');
});

require __DIR__.'/auth.php';
