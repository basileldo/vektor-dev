<?php

namespace Vektor\Stripe\Http\Controllers\Api;

use Auth;
use Illuminate\Http\Request;
use Vektor\Api\Http\Controllers\ApiController;
use Vektor\Checkout\Events\CheckoutComplete;
use Vektor\Checkout\Events\PaymentSuccess;
use Vektor\Stripe\StripePayment;

class StripePaymentController extends ApiController
{
    public function setupIntent(Request $request)
    {
        $payment = new StripePayment;

        $payment_response = $payment->setupIntent(
            $request,
            config('stripe.secret_key')
        );

        return $this->response($payment_response);
    }

    public function paymentIntent(Request $request)
    {
        $payment = new StripePayment;

        $payment_response = $payment->paymentIntent(
            $request,
            config('stripe.secret_key')
        );

        if ($this->isSuccess($payment_response) && isset($payment_response['data']) && isset($payment_response['data']['status']) && $payment_response['data']['status'] == 'succeeded') {
            if (isset($payment_response['data']['customer']) && !empty($payment_response['data']['customer'])) {
                $request->merge(['stripe_customer_id' => $payment_response['data']['customer']]);
                $this->customerCreate($request);
            }

            if (isset($payment_response['data']['payment_intent_id']) && !empty($payment_response['data']['payment_intent_id'])) {
                $request->merge(['payment_intent_id' => $payment_response['data']['payment_intent_id']]);
            }
            if (isset($payment_response['data']['latest_charge']) && !empty($payment_response['data']['latest_charge'])) {
                $request->merge(['latest_charge' => $payment_response['data']['latest_charge']]);
            }
            $request->merge(['payment_method' => 'stripe']);
            PaymentSuccess::dispatch($request);

            $payment_response['data'] = $request->all();
            $payment_response['data']['redirect_url'] = url('success');

            CheckoutComplete::dispatch($request);
        }

        return $this->response($payment_response);
    }

    public function getCustomerCards(Request $request)
    {
        $payment = new StripePayment;

        $payment_response = $payment->getCustomerCards(
            $request,
            config('stripe.secret_key')
        );

        return $this->response($payment_response);
    }

    public function deleteCustomerCards(Request $request)
    {
        $payment = new StripePayment;

        $payment_response = $payment->deleteCustomerCards(
            $request,
            config('stripe.secret_key')
        );

        return $this->response($payment_response);
    }

    public function customerCreate(Request $request)
    {
        $user = Auth::user();

        if ($user && $request->input('stripe_customer_id')) {
            $user_configuration = $user->configuration;
            $user_configuration['stripe_customer_id'] = $request->input('stripe_customer_id');
            $user->configuration = $user_configuration;
            $user->save();

            return $this->response([
                'success' => true,
            ]);
        }

        return $this->response([
            'error' => true,
            'http_code' => 404,
        ]);
    }
}
