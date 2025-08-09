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


// --------------------
// 📌 Public Routes
// --------------------
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail']);
Route::post('/reset-password', [ResetPasswordController::class, 'reset']);
Route::post('/register', [AuthController::class, 'register']); // Can be admin-only depending on use case -->

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
    Route::post('/logout', [AuthController::class, 'logout']);

    // --------------------
    // 🛡️ Admin-Only Routes
    // --------------------
    Route::middleware('role:admin')->group(function () {
    // Route::post('/register', [AuthController::class, 'register']);
        // You can add user management routes here
    });

    // --------------------
    // 🧾 Approver-Only Routes
    // --------------------
        Route::middleware('role:approver,admin')->group(function () {
        Route::post('/payrolls/{payroll}/approve', [PayrollController::class, 'approve']);
        Route::get('/payrolls/pending', [PayrollController::class, 'pending']);
        Route::post('/payrolls/{payroll}/reject', [PayrollController::class, 'reject'])->middleware('role:approver,admin');

    });

    // --------------------
    // 🏗️ Preparer-Only Routes 
    // --------------------
        Route::middleware('role:preparer')->group(function () {
        Route::apiResource('employees', EmployeeController::class);
        Route::apiResource('bank-accounts', BankAccountController::class)->only(['index', 'store']);
        Route::apiResource('payrolls', PayrollController::class)->only(['index', 'store', 'show']);
        Route::post('/payrolls/{payroll}/process', [PayrollController::class, 'processTransaction']); // Transaction execution
        Route::get('/reports/monthly/{month}', [ReportController::class, 'monthlyPayroll']);
        Route::post('/employees/{id}/toggle-status', [EmployeeController::class, 'toggleStatus']);
        Route::post('/payrolls/bulk', [PayrollController::class, 'bulkStore']);

    });

    // --------------------
    // 💳 Transactions: All roles can view (view-only access)
    // --------------------
    Route::apiResource('transactions', TransactionController::class)->only(['index', 'show']);
});

