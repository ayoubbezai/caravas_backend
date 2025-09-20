<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Chargily\ChargilyPay\ChargilyPay;
use Chargily\ChargilyPay\Auth\Credentials;
use Chargily\ChargilyPay\Elements\CheckoutElement;

class CharjilyPaymentController extends Controller
{
    protected function chargilyPayInstance(): ChargilyPay
    {
        return new ChargilyPay(new Credentials([
            'mode' => env('CHARGILY_MODE', 'live'), // sandbox or live
            'public' => env('CHARGILY_PUBLIC_KEY'),
            'secret' => env('CHARGILY_SECRET_KEY'),
        ]));
    }

    public function redirect(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:100',
        ]);

        $userId = Auth::id() ?? $request->user_id;
        if (!$userId) {
            return response()->json(['error' => 'User not found'], 400);
        }

        $amount = $request->amount;
        $currency = $request->currency ?? 'dzd';

        // Create payment record
        $paymentId = DB::table('chargily_payments')->insertGetId([
            'user_id' => $userId,
            'amount' => $amount,
            'currency' => $currency,
            'status' => 'pending',
            'type' => 'charge',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $checkout = $this->chargilyPayInstance()->checkouts()->create([
            'metadata' => ['payment_id' => $paymentId],
            'locale' => 'ar',
            'amount' => $amount,
            'currency' => $currency,
            'description' => "Payment #$paymentId",
            'success_url' => $request->success_url ?? 'https://yourdomain.com/payment-success?payment_id=' . $paymentId,
            'failure_url' => $request->failure_url ?? 'https://yourdomain.com/payment-failure?payment_id=' . $paymentId,
            'webhook_endpoint' => env('CHARGILY_WEBHOOK_ENDPOINT'),
        ]);

        // Redirect directly to Chargily checkout page
        return redirect($checkout->getUrl());
    }

    public function webhook(Request $request)
    {
        $webhook = $this->chargilyPayInstance()->webhook()->get();

        if (!$webhook) {
            return response()->json(['status' => false], 403);
        }

        $checkout = $webhook->getData();
        if (!$checkout instanceof CheckoutElement) {
            return response()->json(['status' => false], 403);
        }

        $metadata = $checkout->getMetadata();
        $payment = DB::table('chargily_payments')->where('id', $metadata['payment_id'])->first();

        if ($payment) {
            DB::beginTransaction();
            $status = $checkout->getStatus();

            DB::table('chargily_payments')->where('id', $payment->id)->update([
                'status' => in_array($status, ['paid', 'failed', 'canceled']) ? $status : 'failed',
                'updated_at' => now(),
            ]);

            if ($status === 'paid') {
                DB::table('users')->where('id', $payment->user_id)->increment('wallet', $payment->amount);
            }

            DB::commit();
        }

        return response()->json(['status' => true]);
    }
}
