<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'completed'); // default is completed

        return response()->json(
            Transaction::with(['payroll.employee', 'fromBankAccount', 'toBankAccount'])
                ->when($status, function ($query, $status) {
                    $query->where('status', $status);
                })
                ->orderBy('created_at', 'desc')
                ->get()
        );
    }

    public function show($id)
    {
        return response()->json(Transaction::with('payroll')->findOrFail($id));
    }
}
