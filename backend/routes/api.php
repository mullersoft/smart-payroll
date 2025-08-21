<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\EmploymentTypeController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\ChapaPaymentController;
use App\Http\Controllers\AllowanceController;


// Initialize a payment for a payroll (approved)
Route::middleware('auth:sanctum')->post('/payrolls/{payroll}/chapa/pay', [ChapaPaymentController::class, 'pay']);
// (Optional) public verify endpoint that your front-end can call
Route::middleware('auth:sanctum')->get('/payments/chapa/verify/{txRef}', [ChapaPaymentController::class, 'verify']);
// Webhook (must be publicly reachable WITHOUT auth)
Route::post('/webhooks/chapa', [ChapaPaymentController::class, 'webhook']);

Route::middleware('auth:sanctum')->get('/me', function (Request $request) {
    return response()->json(['user' => $request->user()]);
});

// --------------------
// 📌 Public Routes
// --------------------
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail']);
Route::post('/reset-password', [ResetPasswordController::class, 'reset']);
Route::post('/register', [AuthController::class, 'register']); // Can be admin-only depending on use case -->
Route::apiResource('positions', PositionController::class)->except(['show']);
Route::apiResource('employment-types', EmploymentTypeController::class)->except(['show']);

// Google OAuth
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);
Route::middleware('auth:sanctum')->get('/me', function (Request $request) {
    return response()->json(['user' => $request->user()]);
});
// Google OAuth
Route::get('/auth/google/redirect', [AuthController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);

// Set role after first Google signup
Route::middleware('auth:sanctum')->post('/auth/set-role', [AuthController::class, 'setRole']);

// --------------------
// 🔐 Protected Routes
// --------------------
Route::middleware(['auth:sanctum'])->group(function () {

    // --------------------
    // 👤 Authenticated User Routes (Any role)
    // Admin-Only Route to get all users (you already have this group)
    Route::middleware('role:admin')->group(function () {
        Route::get('/users', function () {
            return \App\Models\User::all(); // 👈 Or use a controller if preferred
        });
        Route::post('/users/{id}/toggle-status', [AuthController::class, 'toggleStatus']);
    });

    // --------------------
    Route::get('/user', fn(Request $request) => $request->user());
    Route::get('/profile', fn(Request $request) => $request->user());

    // ...
    // --------------------
    // 🛡️ Admin-Only Routes
    // --------------------
    Route::middleware('role:admin')->group(function () {
        Route::get('/users', function () {
            return \App\Models\User::all();
        });
        Route::post('/users/{id}/toggle-status', [AuthController::class, 'toggleStatus']);
        Route::put('/users/{id}', [AuthController::class, 'updateUser']); // 👈 add this
        Route::delete('/users/{id}', [AuthController::class, 'deleteUser']); // 👈 optional delete
    });


    // --------------------
    // 🧾 Approver-Only Routes
    // --------------------
    Route::middleware('role:approver,admin')->group(function () {
        Route::post('/payrolls/{payroll}/approve', [PayrollController::class, 'approve']);
        Route::get('/payrolls/pending', [PayrollController::class, 'pending']);
        Route::post('/payrolls/{payroll}/reject', [PayrollController::class, 'reject'])->middleware('role:approver,admin');
        Route::get('/reports/monthly/{month}', [ReportController::class, 'monthlyPayroll']);
        Route::get('/payrolls', [PayrollController::class, 'list']);
        Route::get('/payrolls/list', [PayrollController::class, 'list']);
    });

    // --------------------
    // 🏗️ Preparer-Only Routes 
    // --------------------
    Route::middleware('role:preparer')->group(function () {
        Route::apiResource('employees', EmployeeController::class);
        Route::apiResource('bank-accounts', BankAccountController::class);
        Route::apiResource('payrolls', PayrollController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
        Route::post('/payrolls/{payroll}/process', [PayrollController::class, 'processTransaction']); // Transaction execution
        Route::post('/employees/{id}/toggle-status', [EmployeeController::class, 'toggleStatus']);
        Route::post('/payrolls/bulk', [PayrollController::class, 'bulkStore']);
        Route::get('/reports/monthly/{month}', [ReportController::class, 'monthlyPayroll']);
        Route::get('/reports/monthly/{month}/export-excel', [ReportController::class, 'exportExcel']);
        Route::get('/reports/monthly/{month}/export-pdf', [ReportController::class, 'exportPdf']); // Add this

        Route::apiResource('allowances', AllowanceController::class);
    });

    // --------------------
    // 💳 Transactions: All roles can view (view-only access)
    // --------------------
    Route::apiResource('transactions', TransactionController::class)->only(['index', 'show']);
});
