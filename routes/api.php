<?php

use App\Http\Controllers\Api\BillingController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\MerchantController;
use App\Http\Controllers\Api\PlanController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\UsageController;
use Illuminate\Support\Facades\Route;

// Requirement 2 & 6: Top-level POST /usage endpoint with rate-limiting
Route::post('/usage', [UsageController::class, 'ingestDirect'])->middleware('throttle:usage');

// Requirement 5: GET /merchants/{merchant}/dashboard
Route::get('/merchants/{merchant}/dashboard', [MerchantController::class, 'dashboard']);

Route::prefix('v1')->group(function () {
    // Top-level usage endpoint under v1
    Route::post('/usage', [UsageController::class, 'ingestDirect'])->middleware('throttle:usage');

    // Merchants (Tenants)
    Route::get('/merchants', [MerchantController::class, 'index']);
    Route::post('/merchants', [MerchantController::class, 'store']);
    Route::get('/merchants/{merchant}', [MerchantController::class, 'show']);
    Route::get('/merchants/{merchant}/dashboard', [MerchantController::class, 'dashboard']);

    Route::prefix('merchants/{merchant}')->group(function () {
        // Plans
        Route::get('/plans', [PlanController::class, 'index']);
        Route::post('/plans', [PlanController::class, 'store']);
        Route::get('/plans/{plan}', [PlanController::class, 'show']);

        // Customers
        Route::get('/customers', [CustomerController::class, 'index']);
        Route::post('/customers', [CustomerController::class, 'store']);
        Route::get('/customers/{customer}', [CustomerController::class, 'show']);
        Route::get('/customers/{customer}/usage', [UsageController::class, 'summary']);

        // Subscriptions
        Route::get('/subscriptions', [SubscriptionController::class, 'index']);
        Route::post('/subscriptions', [SubscriptionController::class, 'store']);
        Route::get('/subscriptions/{subscription}', [SubscriptionController::class, 'show']);
        Route::post('/subscriptions/{subscription}/cancel', [SubscriptionController::class, 'cancel']);

        // Requirement 8: Mid-Cycle Plan Change (Upgrade / Downgrade)
        Route::post('/subscriptions/{subscription}/change-plan', [SubscriptionController::class, 'changePlan']);

        // Usage Ingestion (High-Volume Support + Rate Limiting)
        Route::post('/usage', [UsageController::class, 'ingest'])->middleware('throttle:usage');
        Route::post('/usage/ingest', [UsageController::class, 'ingest'])->middleware('throttle:usage');
        Route::post('/usage/batch', [UsageController::class, 'batch'])->middleware('throttle:usage');

        // Invoicing & Billing
        Route::get('/subscriptions/{subscription}/preview-invoice', [BillingController::class, 'preview']);
        Route::post('/subscriptions/{subscription}/generate-invoice', [BillingController::class, 'generate']);
        Route::post('/billing/process-cycle', [BillingController::class, 'processCycle']);
        Route::get('/invoices', [BillingController::class, 'invoices']);
        Route::get('/invoices/{invoice}', [BillingController::class, 'invoiceDetails']);
    });
});
