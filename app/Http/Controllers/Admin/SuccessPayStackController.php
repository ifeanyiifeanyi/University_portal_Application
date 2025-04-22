<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class SuccessPayStackController extends Controller
{
    protected $paystackSecretKey;

    public function __construct()
    {
        $this->paystackSecretKey = env('PAYSTACK_SECRET_KEY');
    }


    public function index(Request $request)
    {
        try {
            $from = $request->input('from', now()->subDays(7)->format('Y-m-d'));
            $to = $request->input('to', now()->format('Y-m-d'));

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->paystackSecretKey,
                'Content-Type' => 'application/json',
            ])->get('https://api.paystack.co/transaction', [
                'status' => 'success',
                'perPage' => 1000,
                'from' => $from,
                'to' => $to
            ]);

            if (!$response->successful()) {
                throw new \Exception('Failed to fetch Paystack transactions: ' . ($response->json()['message'] ?? 'Unknown error'));
            }

            $transactions = collect($response->json()['data']);
            // dd($transactions->count());

            return view('admin.payments.paystack-transactions', compact('transactions', 'from', 'to'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
