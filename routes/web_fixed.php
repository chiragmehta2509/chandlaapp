<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\ContactController as AdminContactController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Client\AuthController as ClientAuthController;
use App\Http\Controllers\Client\DashboardController as ClientDashboardController;
use App\Http\Controllers\Client\EventController as ClientEventController;
use App\Http\Controllers\Client\ContactController as ClientContactController;

Route::get('/', function () {
    return response()->json([
        'name' => 'Chandla Book API',
        'version' => '1.0.0',
        'status' => 'running'
    ]);
});

Route::view('/docs', 'swagger');

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Admin Auth Routes
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    // Admin Protected Routes
    Route::middleware(['admin.auth'])->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        
        // Users Management
        Route::resource('users', AdminUserController::class);
        Route::post('/users/{id}/toggle-status', [AdminUserController::class, 'toggleStatus'])->name('users.toggle-status');
        
        // Events Management
        Route::resource('events', AdminEventController::class)->only(['index', 'show', 'destroy']);
        
        // Contacts Management
        Route::resource('contacts', AdminContactController::class)->only(['index', 'show']);
        
        // Payments Management
        Route::resource('payments', AdminPaymentController::class)->only(['index', 'show']);
    });
});

// Client Portal Routes
Route::prefix('client')->name('client.')->group(function () {
    // Client Auth Routes
    Route::get('/login', [ClientAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [ClientAuthController::class, 'login']);
    Route::get('/register', [ClientAuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [ClientAuthController::class, 'register']);
    Route::post('/logout', [ClientAuthController::class, 'logout'])->name('logout');

    // Client Protected Routes
    Route::middleware(['auth:web'])->group(function () {
        Route::get('/dashboard', [ClientDashboardController::class, 'index'])->name('dashboard');
        // More client routes will be added by Client controllers
    });
});
