<?php

namespace App\Http\Controllers;

use App\Models\Transaction;

class TransactionController extends Controller
{
    public function index()
    {
        return response()->json(Transaction::with('payroll')->get());
    }

    public function show($id)
    {
        return response()->json(Transaction::with('payroll')->findOrFail($id));
    }
}
