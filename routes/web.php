<?php

use App\Http\Controllers\Api\MerchantController;
use App\Http\Controllers\Api\UsageController;
use App\Http\Controllers\CustomerPortalController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// Merchant Admin Dashboard
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Requirement 2 & 6: Root POST /usage endpoint with rate limiting
Route::post('/usage', [UsageController::class, 'ingestDirect'])->middleware('throttle:usage');

// Requirement 5: Root GET /merchants/{merchant}/dashboard
Route::get('/merchants/{merchant}/dashboard', [MerchantController::class, 'dashboard']);

// Customer Web Portal
Route::prefix('portal')->name('portal.')->group(function () {
    Route::get('/login', [CustomerPortalController::class, 'showLogin'])->name('login');
    Route::post('/login', [CustomerPortalController::class, 'login'])->name('login.post');
    Route::post('/logout', [CustomerPortalController::class, 'logout'])->name('logout');
    Route::get('/', [CustomerPortalController::class, 'dashboard'])->name('dashboard');
    Route::post('/purchase', [CustomerPortalController::class, 'purchasePlan'])->name('purchase');
    Route::post('/change-plan', [CustomerPortalController::class, 'changePlan'])->name('change-plan');
    Route::post('/change_plan', [CustomerPortalController::class, 'changePlan'])->name('change_plan');
    Route::post('/change-password', [CustomerPortalController::class, 'updatePassword'])->name('password.update');
});

// Merchant Portal: Plan Definition & Management
Route::post('/merchants/{merchant}/plans', [\App\Http\Controllers\MerchantPlanController::class, 'store'])->name('merchants.plans.store');
Route::post('/merchants/{merchant}/plans/{plan}/update', [\App\Http\Controllers\MerchantPlanController::class, 'update'])->name('merchants.plans.update');
Route::post('/merchants/{merchant}/plans/{plan}/toggle', [\App\Http\Controllers\MerchantPlanController::class, 'toggle'])->name('merchants.plans.toggle');

// Dedicated Merchant Portal (Login, Dashboard & Plan Definition)
Route::prefix('merchant')->name('merchant.')->group(function () {
    Route::get('/login', [\App\Http\Controllers\MerchantPortalController::class, 'showLogin'])->name('login');
    Route::post('/login', [\App\Http\Controllers\MerchantPortalController::class, 'login'])->name('login.post');
    Route::post('/logout', [\App\Http\Controllers\MerchantPortalController::class, 'logout'])->name('logout');
    Route::get('/', [\App\Http\Controllers\MerchantPortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard', [\App\Http\Controllers\MerchantPortalController::class, 'dashboard'])->name('dashboard.alt');
    Route::post('/plans', [\App\Http\Controllers\MerchantPortalController::class, 'storePlan'])->name('plans.store');
    Route::post('/plans/{plan}/toggle', [\App\Http\Controllers\MerchantPortalController::class, 'togglePlan'])->name('plans.toggle');
    Route::post('/change-password', [\App\Http\Controllers\MerchantPortalController::class, 'updatePassword'])->name('password.update');
});


