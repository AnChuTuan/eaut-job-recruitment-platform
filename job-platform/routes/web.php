<?php

use App\Http\Controllers\CompanyVerificationController;
use App\Http\Controllers\Employer\ApplicantController;
use App\Http\Controllers\Employer\CompanyProfileController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:employer'])->prefix('employer')->name('employer.')->group(function () {
    Route::get('/company', [CompanyProfileController::class, 'edit'])->name('company.edit');
    Route::put('/company', [CompanyProfileController::class, 'update'])->name('company.update');
    Route::get('/company/verification', [CompanyVerificationController::class, 'create'])->name('verification.create');
    Route::post('/company/verification', [CompanyVerificationController::class, 'submit'])->name('verification.submit');

    Route::get('/applicants', [ApplicantController::class, 'index'])->name('applicants.index');
    Route::get('/applicants/{application}', [ApplicantController::class, 'show'])->name('applicants.show');
    Route::patch('/applicants/{application}/status', [ApplicantController::class, 'updateStatus'])->name('applicants.status');
    Route::get('/applicants/{application}/cv', [ApplicantController::class, 'downloadCv'])->name('applicants.cv');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/verifications', [CompanyVerificationController::class, 'adminIndex'])->name('verifications.index');
    Route::get('/verifications/{verification}', [CompanyVerificationController::class, 'adminShow'])->name('verifications.show');
    Route::get('/verifications/{verification}/document', [CompanyVerificationController::class, 'downloadDocument'])->name('verifications.document');
    Route::patch('/verifications/{verification}/approve', [CompanyVerificationController::class, 'approve'])->name('verifications.approve');
    Route::patch('/verifications/{verification}/reject', [CompanyVerificationController::class, 'reject'])->name('verifications.reject');
});

require __DIR__.'/auth.php';
