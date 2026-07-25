<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Vendor\VendorWebController;

// Public self-registration (no login required for MVP)
Route::get('/client/vendors/register', [VendorWebController::class, 'registerForm'])->name('client.vendors.register');
Route::post('/client/vendors/register', [VendorWebController::class, 'registerSubmit'])->name('client.vendors.register.submit');

// Client Portal (Requires auth)
Route::middleware(['auth:web'])->prefix('client/vendors')->name('client.vendors.')->group(function () {
    Route::get('/', [VendorWebController::class, 'index'])->name('index');
    Route::get('/{vendor}', [VendorWebController::class, 'show'])->name('show')->whereNumber('vendor');
    Route::post('/{vendor}/lead', [VendorWebController::class, 'submitLead'])->name('lead.submit')->whereNumber('vendor');
});

// Admin Panel (Requires admin auth)
Route::middleware(['admin.auth'])->prefix('admin/vendors')->name('admin.vendors.')->group(function () {
    Route::get('/', [VendorWebController::class, 'adminIndex'])->name('index');
    Route::post('/{vendor}/approve', [VendorWebController::class, 'adminApprove'])->name('approve')->whereNumber('vendor');
    Route::post('/{vendor}/reject', [VendorWebController::class, 'adminReject'])->name('reject')->whereNumber('vendor');
});
