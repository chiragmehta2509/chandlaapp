<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Vendor\VendorApiController;

// Public API endpoints (Note: prefix 'api' is already applied by RouteServiceProvider)
// So Route::get('vendor-categories') becomes /api/vendor-categories
Route::get('vendor-categories', [VendorApiController::class, 'indexCategory']);
Route::get('vendors', [VendorApiController::class, 'index']);
Route::get('vendors/{id}', [VendorApiController::class, 'show'])->whereNumber('id');
Route::post('vendors', [VendorApiController::class, 'store']);

// Authenticated client API endpoints
Route::middleware('auth:sanctum')->group(function () {
    Route::post('vendors/{id}/leads', [VendorApiController::class, 'storeLead'])->whereNumber('id');
});

// Authenticated admin API endpoints
Route::middleware('auth:sanctum')->group(function () {
    Route::get('admin/vendors/pending', [VendorApiController::class, 'adminPending']);
    Route::post('admin/vendors/{id}/approve', [VendorApiController::class, 'adminApprove'])->whereNumber('id');
    Route::post('admin/vendors/{id}/reject', [VendorApiController::class, 'adminReject'])->whereNumber('id');
});

// Register duplicate v1 prefixed routes to match the v1 prefix in api.php
Route::prefix('v1')->group(function () {
    Route::get('vendor-categories', [VendorApiController::class, 'indexCategory']);
    Route::get('vendors', [VendorApiController::class, 'index']);
    Route::get('vendors/{id}', [VendorApiController::class, 'show'])->whereNumber('id');
    Route::post('vendors', [VendorApiController::class, 'store']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('vendors/{id}/leads', [VendorApiController::class, 'storeLead'])->whereNumber('id');
        Route::get('admin/vendors/pending', [VendorApiController::class, 'adminPending']);
        Route::post('admin/vendors/{id}/approve', [VendorApiController::class, 'adminApprove'])->whereNumber('id');
        Route::post('admin/vendors/{id}/reject', [VendorApiController::class, 'adminReject'])->whereNumber('id');
    });
});
