<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Services\ChapaService;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ChapaPaymentController extends Controller
{
    // Kick off payment for an APPROVED payroll
    public function pay(Payroll $payroll, Request $request, ChapaService $chapa)
    {
        // You can protect this by role if needed
        if ($payroll->status !== 'approved') {
            return response()->json(['error' => 'Only approved payrolls can be paid.'], 400);
        }

        // Optional: prevent double payments
        if ($payroll->is_processed) {
            return response()->json(['error' => 'This payroll is already processed.'], 400);
        }

        $tx = $chapa->initializePayment($payroll);

        return response()->json([
            'message' => 'Payment initialized',
            'checkout_url' => $tx->checkout_url,
            'tx_ref' => $tx->tx_ref,
        ]);
    }

    // Front-end callback hits this endpoint (or just call /verify)
    public function verify(string $txRef, ChapaService $chapa)
    {
        $tx = Transaction::where('tx_ref', $txRef)->firstOrFail();

        $result = $chapa->verify($txRef);

        if ($result['ok'] && (($result['data']['status'] ?? '') === 'success')) {
            $tx->status = 'completed';
            $tx->verified_at = Carbon::now();
            $tx->provider_meta = $result['raw'];
            $tx->save();

            // mark payroll processed (salary leg only; taxes/pension if you keep BankService, mark here or separately)
            $payroll = $tx->payroll()->first();
            if ($payroll && !$payroll->is_processed) {
                $payroll->is_processed = true;
                $payroll->save();
            }

            return response()->json(['message' => 'Payment verified successfully', 'transaction' => $tx]);
        }

        $tx->status = 'failed';
        $tx->failure_reason = $result['raw']['message'] ?? 'Verification failed';
        $tx->provider_meta = $result['raw'] ?? null;
        $tx->save();

        return response()->json(['error' => 'Payment verification failed', 'details' => $result['raw'] ?? null], 400);
    }

    // Webhook for server-to-server confirmation
    public function webhook(Request $request, ChapaService $chapa)
    {
        // You can optionally verify a signature header if Chapa provides one.
        // Safer approach: read tx_ref & call verify()
        $payload = $request->all();
        Log::info('Chapa webhook received', $payload);

        $txRef = $payload['tx_ref'] ?? ($payload['data']['tx_ref'] ?? null);
        if (!$txRef) {
            return response()->json(['error' => 'tx_ref missing'], 422);
        }

        $tx = Transaction::where('tx_ref', $txRef)->first();
        if (!$tx) {
            return response()->json(['error' => 'Transaction not found'], 404);
        }

        // Double-check with verify
        $result = $chapa->verify($txRef);
        if ($result['ok'] && (($result['data']['status'] ?? '') === 'success')) {
            if ($tx->status !== 'completed') {
                $tx->status = 'completed';
                $tx->verified_at = Carbon::now();
                $tx->provider_meta = $result['raw'];
                $tx->save();

                $payroll = $tx->payroll()->first();
                if ($payroll && !$payroll->is_processed) {
                    $payroll->is_processed = true;
                    $payroll->save();
                }
            }
            return response()->json(['message' => 'Webhook processed']);
        }

        // Mark failed if verify says so
        $tx->status = 'failed';
        $tx->failure_reason = $result['raw']['message'] ?? 'Verification failed (webhook)';
        $tx->provider_meta = $result['raw'] ?? null;
        $tx->save();

        return response()->json(['message' => 'Webhook processed (failed)'], 200);
    }
}
