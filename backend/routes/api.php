<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\BankAccountController;

Route::apiResource('bank-accounts', BankAccountController::class)->only(['index', 'store']);
Route::apiResource('employees', EmployeeController::class);
Route::apiResource('payrolls', PayrollController::class)->only(['index', 'store', 'show']);
Route::apiResource('transactions', TransactionController::class)->only(['index', 'show']);
Route::post('/payrolls/{payroll}/process', [PayrollController::class, 'processTransaction']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/reports/monthly/{month}', [ReportController::class, 'monthlyPayroll']);


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
