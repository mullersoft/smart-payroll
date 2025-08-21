<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Str;
use App\Models\Payroll;
use App\Models\Transaction;
use Carbon\Carbon;

class ChapaService
{
    protected Client $http;
    protected string $baseUrl;
    protected string $secret;
    protected string $currency;
    protected string $webhookUrl;
    protected string $returnUrl;

    public function __construct()
    {
        $this->baseUrl    = rtrim(config('services.chapa.base_url', env('CHAPA_BASE_URL', 'https://api.chapa.co')), '/');
        $this->secret     = config('services.chapa.secret', env('CHAPA_SECRET_KEY'));
        $this->currency   = config('services.chapa.currency', env('CHAPA_CURRENCY', 'ETB'));
        $this->webhookUrl = config('services.chapa.webhook', env('CHAPA_WEBHOOK_URL'));
        $this->returnUrl  = config('services.chapa.return', env('CHAPA_RETURN_URL'));

        $this->http = new Client([
            'base_uri' => $this->baseUrl,
            'headers'  => [
                'Authorization' => 'Bearer ' . $this->secret,
                'Accept'        => 'application/json',
            ],
            'http_errors' => false,
            'timeout'     => 20,
        ]);
    }

    public function initializePayment(Payroll $payroll): Transaction
    {
        $payroll->load('employee');

        // Generate a unique tx_ref (idempotent per payroll-month ideally)
        $txRef = 'payroll_' . $payroll->id . '_' . Str::uuid();

        // Create a pending transaction record first
        $tx = Transaction::create([
            'payroll_id'       => $payroll->id,
            'transaction_type' => 'salary',
            'amount'           => $payroll->net_payment,
            'from_account'     => 'CHAPA',
            'to_account'       => $payroll->employee?->bankAccount?->account_number ?? 'N/A',
            'transaction_date' => Carbon::now(),
            'processed_by'     => auth()->user()->name ?? 'System',
            'status'           => 'pending',
            'provider'         => 'chapa',
            'tx_ref'           => $txRef,
        ]);


        $names = explode(' ', $payroll->employee->full_name, 2);
        $firstName = $names[0] ?? 'Employee';
        $lastName  = $names[1] ?? 'Payroll';


        // Prepare Chapa payload
        $payload = [
            'amount'       => number_format($payroll->net_payment, 2, '.', ''), // string money
            'currency'     => $this->currency,
            'email'        => $payroll->employee->email ?? 'payroll@example.com',
            'first_name'   => $firstName, //$payroll->employee->full_name ?? 'Employee',
            'last_name'    => $lastName, // 'Payroll',
            'tx_ref'       => $txRef,
            'callback_url' => $this->returnUrl,   // where user is redirected to (front-end)
            'return_url'   => $this->returnUrl,   // (Chapa supports both; keep same)
            'customization' => [
                'title' => 'Payroll Payment',
                'description' => "Salary for {$payroll->pay_month}",
            ],
            'subaccount'   => null, // if you use split payments later
        ];

        $res = $this->http->post('/v1/transaction/initialize', ['json' => $payload]);
        $body = json_decode((string) $res->getBody(), true);

        if (($res->getStatusCode() !== 200 && $res->getStatusCode() !== 201) || !($body['status'] ?? false)) {
            $message = $body['message'] ?? 'Unable to initialize payment with Chapa';

            // Ensure $message is always a string
            if (is_array($message)) {
                $message = json_encode($message);
            }

            $tx->status = 'failed';
            $tx->failure_reason = $message;
            $tx->provider_meta = $body ?? null;
            $tx->save();

            throw new \Exception($message);
        }

        // if (($res->getStatusCode() !== 200 && $res->getStatusCode() !== 201) || !($body['status'] ?? false)) {
        //     // $message = $body['message'] ?? 'Unable to initialize payment with Chapa';
        //     // $tx->status = 'failed';
        //     // $tx->failure_reason = $message;
        //     // $tx->provider_meta = $body ?? null;
        //     // $tx->save();
        //     // throw new \Exception($message);
        // }

        $checkout = $body['data']['checkout_url'] ?? null;

        $tx->checkout_url  = $checkout;
        $tx->provider_meta = $body;
        $tx->save();

        return $tx;
    }

    public function verify(string $txRef): array
    {
        $res = $this->http->get("/v1/transaction/verify/{$txRef}");
        $body = json_decode((string) $res->getBody(), true);

        return [
            'ok'     => ($body['status'] ?? false) === 'success',
            'data'   => $body['data'] ?? [],
            'raw'    => $body,
            'status' => $body['status'] ?? null,
        ];
    }
}
