<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController,
    EmployeeController,
    PayrollController,
    TransactionController,
    ReportController,
    BankAccountController,
    ForgotPasswordController,
    ResetPasswordController,
    EmploymentTypeController,
    PositionController,
    ChapaPaymentController,
    AllowanceController
};

// --------------------
// 📌 Public Routes
// --------------------
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']); // registers with "pending" status
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail']);
Route::post('/reset-password', [ResetPasswordController::class, 'reset']);
Route::get('/profile', fn(Request $request) => $request->user());

// Google OAuth
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);



// --------------------
// 🔐 Protected Routes
// --------------------
Route::middleware(['auth:sanctum'])->group(function () {
Route::get('/me', fn(Request $request) => response()->json(['user' => $request->user()]));
Route::get('/my/profile', [EmployeeController::class, 'myProfile']);
Route::get('/my/payrolls', [PayrollController::class, 'myPayrolls']);
Route::get('/my/transactions', [TransactionController::class, 'myTransactions']);
Route::post('/webhooks/chapa', [ChapaPaymentController::class, 'webhook']);
    // ---- Employees (shared) ----
    // Preparer can only "index", Admin can do all
    Route::apiResource('employees', EmployeeController::class);

    // ---- Preparer ----
    Route::middleware('role:preparer')->group(function () {
        Route::apiResource('payrolls', PayrollController::class)
            ->only(['index', 'store', 'show', 'update', 'destroy']);
        Route::post('/payrolls/bulk', [PayrollController::class, 'bulkStore']);
        Route::post('/payrolls/{payroll}/process', [PayrollController::class, 'processTransaction']);

        Route::get('/reports/monthly/{month}', [ReportController::class, 'monthlyPayroll']);
        Route::get('/reports/monthly/{month}/export-excel', [ReportController::class, 'exportExcel']);
        Route::get('/reports/monthly/{month}/export-pdf', [ReportController::class, 'exportPdf']);

        Route::apiResource('transactions', TransactionController::class)->only(['index', 'show']);

        // Payments
        Route::post('/payrolls/{payroll}/chapa/pay', [ChapaPaymentController::class, 'pay']);
        Route::get('/payments/chapa/verify/{txRef}', [ChapaPaymentController::class, 'verify']);
    });

    // ---- Admin ----
    Route::middleware('role:admin,preparer')->group(function () {
        Route::post('/employees/{id}/activate', [EmployeeController::class, 'activate']);
        Route::post('/employees/{id}/toggle-status', [EmployeeController::class, 'toggleStatus']);
         Route::get('/users', [AuthController::class, 'index']); // all users
         Route::get('/user', [AuthController::class, 'me']);     // current logged-in user
        // Route::get('/users', fn() => \App\Models\User::all());
        // Route::get('/user', fn(Request $request) => $request->user());
        Route::post('/users/{id}/toggle-status', [AuthController::class, 'toggleStatus']);
        Route::put('/users/{id}', [AuthController::class, 'updateUser']);
        Route::delete('/users/{id}', [AuthController::class, 'deleteUser']);

        Route::apiResource('bank-accounts', BankAccountController::class);
        Route::apiResource('allowances', AllowanceController::class);
        Route::apiResource('positions', PositionController::class)->except(['show']);
        Route::apiResource('employment-types', EmploymentTypeController::class)->except(['show']);
    });

    // ---- Approver ----
    Route::middleware('role:approver,preparer')->group(function () {
        Route::post('/payrolls/{payroll}/approve', [PayrollController::class, 'approve']);
        Route::post('/payrolls/{payroll}/reject', [PayrollController::class, 'reject']);
        Route::get('/payrolls/pending', [PayrollController::class, 'pending']);
        Route::get('/payrolls', [PayrollController::class, 'list']);
        // Route::get('/payrolls/list', [PayrollController::class, 'list']);

        Route::get('/reports/monthly/{month}', [ReportController::class, 'monthlyPayroll']);
        Route::apiResource('transactions', TransactionController::class)->only(['index', 'show']);
    });
});
