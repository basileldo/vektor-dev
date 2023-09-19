<?php

namespace Vektor\Stripe;

use Exception;
use Illuminate\Http\Request;
use Stripe\Customer;
use Stripe\Exception\ApiConnectionException;
use Stripe\Exception\ApiErrorException;
use Stripe\Exception\AuthenticationException;
use Stripe\Exception\CardException;
use Stripe\Exception\InvalidRequestException;
use Stripe\Exception\RateLimitException;
use Stripe\PaymentIntent;
use Stripe\PaymentMethod;
use Stripe\SetupIntent;
use Stripe\Stripe;

class StripePayment
{
    private $customer_id = null;

    private $intent = null;

    private function handleCustomerParams(Request $request)
    {
        $params = [];

        $customer_description = implode(' ', array_values(array_filter([
            $request->input('first_name'),
            $request->input('last_name'),
        ])));

        if (! empty($customer_description)) {
            $params['description'] = $customer_description;
        }

        if ($request->input('email')) {
            $params['email'] = $request->input('email');
        }

        if ($request->input('phone')) {
            $params['phone'] = $request->input('phone');
        }

        $address_exists = (count(array_values(array_filter([
            $request->input('billing_address_line_1'),
            $request->input('billing_address_line_2'),
            $request->input('billing_city'),
            $request->input('billing_county'),
            $request->input('billing_postcode'),
            $request->input('billing_country'),
        ]))) > 0) ? true : false;

        if ($address_exists) {
            $params['address'] = [];

            if ($request->input('billing_address_line_1')) {
                $params['address']['line1'] = $request->input('billing_address_line_1');
            }

            if ($request->input('billing_address_line_2')) {
                $params['address']['line2'] = $request->input('billing_address_line_2');
            }

            if ($request->input('billing_city')) {
                $params['address']['city'] = $request->input('billing_city');
            }

            if ($request->input('billing_county')) {
                $params['address']['state'] = $request->input('billing_county');
            }

            if ($request->input('billing_postcode')) {
                $params['address']['postal_code'] = $request->input('billing_postcode');
            }

            if ($request->input('billing_country')) {
                $params['address']['country'] = $request->input('billing_country');
            }
        }

        return $params;
    }

    public function handleCustomer(Request $request)
    {
        $customer = null;

        if ($request->input('payment_method_id')) {
            $params = $this->handleCustomerParams($request);

            if ($request->input('customer_id')) {
                try {
                    $customer = Customer::retrieve($request->input('customer_id'));
                    $this->customer_id = $customer->id;

                    if (isset($customer->deleted) && $customer->deleted) {
                        $customer = Customer::create($params);
                        $this->customer_id = $customer->id;
                    } else {
                        if (! empty($params)) {
                            $customer = Customer::update($this->customer_id, $params);
                        }
                    }
                } catch (InvalidRequestException $e) {
                    $customer = Customer::create($params);
                    $this->customer_id = $customer->id;
                }
            } else {
                $customer = Customer::create($params);
                $this->customer_id = $customer->id;
            }
        }

        return $customer;
    }

    public function handleSetupIntent(Request $request)
    {
        try {
            if ($request->input('payment_method_id')) {
                $this->intent = SetupIntent::create([
                    'customer' => $this->customer_id,
                    'payment_method' => $request->input('payment_method_id'),
                    'usage' => 'off_session',
                    'confirm' => true,
                ]);
            }

            if ($this->intent && in_array($this->intent->status, ['requires_action', 'requires_source_action']) && $this->intent->next_action->type == 'use_stripe_sdk') {
                return [
                    'success' => true,
                    'data' => [
                        'requires_action' => true,
                        'intent_client_secret' => $this->intent->client_secret,
                        'customer' => $this->customer_id,
                        'payment_method' => $request->input('payment_method_id'),
                    ],
                ];
            } elseif ($this->intent && $this->intent->status == 'succeeded') {
                return [
                    'success' => true,
                    'success_message' => 'Your card was added successfully',
                    'data' => [
                        'status' => 'succeeded',
                        'customer' => $this->customer_id,
                        'payment_method' => $request->input('payment_method_id'),
                    ],
                ];
            } else {
                return [
                    'error' => true,
                    'error_message' => 'Invalid status',
                    'http_code' => 500,
                ];
            }
        } catch (CardException $e) {

            // The card has been declined
            $error_code = $e->getHttpStatus();
            if (strlen($error_code) != 3 || ! is_numeric($error_code)) {
                $error_code = 403;
            }

            return [
                'error' => true,
                'error_message' => $e->getError()->message,
                'http_code' => $error_code,
                'data' => [
                    'error_type' => $e->getError()->type,
                    'error_code' => $e->getError()->code,
                ],
            ];
        } catch (RateLimitException $e) {

            // The API request has been throttled
            return [
                'error' => true,
                'error_message' => $e->getMessage(),
                'http_code' => 429,
            ];
        } catch (InvalidRequestException $e) {

            // Invalid parameters were supplied to the API
            return [
                'error' => true,
                'error_message' => $e->getMessage(),
                'http_code' => 400,
            ];
        } catch (AuthenticationException $e) {

            // Authentication with the API has failed
            return [
                'error' => true,
                'error_message' => $e->getMessage(),
                'http_code' => 401,
            ];
        } catch (ApiConnectionException $e) {

            // Network communication with the API has failed
            return [
                'error' => true,
                'error_message' => $e->getMessage(),
                'http_code' => 402,
            ];
        } catch (ApiErrorException $e) {

            // Generic failure related to the API
            return [
                'error' => true,
                'error_message' => $e->getMessage(),
                'http_code' => 500,
            ];
        } catch (Exception $e) {

            // Generic failure unrelated to the API
            return [
                'error' => true,
                'error_message' => 'Your transaction was not processed successfully',
                'http_code' => 500,
            ];
        }
    }

    public function handlePaymentIntent(Request $request)
    {
        try {
            if ($request->input('payment_method_id')) {
                $payment_intent_payload = [
                    'customer' => $this->customer_id,
                    'payment_method' => $request->input('payment_method_id'),
                    'amount' => $request->input('amount'),
                    'currency' => 'gbp',
                    'confirmation_method' => 'manual',
                    'confirm' => true,
                    'off_session' => false,
                ];

                if ($request->input('off_session')) {
                    $payment_intent_payload['off_session'] = true;
                }

                if ($request->input('save_card')) {
                    $payment_intent_payload['setup_future_usage'] = 'off_session';
                }

                $this->intent = PaymentIntent::create($payment_intent_payload);
            }

            if ($request->input('payment_intent_id')) {
                $this->intent = PaymentIntent::retrieve($request->input('payment_intent_id'));
                $this->intent->confirm();
            }

            if ($this->intent && isset($this->intent->status) && in_array($this->intent->status, ['requires_action', 'requires_source_action']) && isset($this->intent->next_action) && isset($this->intent->next_action->type) && $this->intent->next_action->type == 'use_stripe_sdk') {
                return [
                    'success' => true,
                    'data' => [
                        'requires_action' => true,
                        'intent_client_secret' => $this->intent->client_secret,
                        'customer' => $this->customer_id,
                    ],
                ];
            } elseif ($this->intent && $this->intent->status == 'succeeded') {
                return [
                    'success' => true,
                    'success_message' => 'Your transaction was processed successfully',
                    'data' => [
                        'status' => 'succeeded',
                        'customer' => $this->customer_id,
                        'payment_method' => $this->intent->payment_method,
                        'payment_intent_id' => $this->intent->id,
                        'latest_charge' => $this->intent->latest_charge,
                    ],
                ];
            } else {
                return [
                    'error' => true,
                    'error_message' => 'Invalid status',
                    'http_code' => 500,
                ];
            }
        } catch (CardException $e) {

            // The card has been declined
            $error_code = $e->getHttpStatus();
            if (strlen($error_code) != 3 || ! is_numeric($error_code)) {
                $error_code = 403;
            }

            return [
                'error' => true,
                'error_message' => $e->getError()->message,
                'http_code' => $error_code,
                'data' => [
                    'error_type' => $e->getError()->type,
                    'error_code' => $e->getError()->code,
                ],
            ];
        } catch (RateLimitException $e) {

            // The API request has been throttled
            return [
                'error' => true,
                'error_message' => $e->getMessage(),
                'http_code' => 429,
            ];
        } catch (InvalidRequestException $e) {

            // Invalid parameters were supplied to the API
            return [
                'error' => true,
                'error_message' => $e->getMessage(),
                'http_code' => 400,
            ];
        } catch (AuthenticationException $e) {

            // Authentication with the API has failed
            return [
                'error' => true,
                'error_message' => $e->getMessage(),
                'http_code' => 401,
            ];
        } catch (ApiConnectionException $e) {

            // Network communication with the API has failed
            return [
                'error' => true,
                'error_message' => $e->getMessage(),
                'http_code' => 402,
            ];
        } catch (ApiErrorException $e) {

            // Generic failure related to the API
            return [
                'error' => true,
                'error_message' => $e->getMessage(),
                'http_code' => 500,
            ];
        } catch (Exception $e) {

            // Generic failure unrelated to the API
            return [
                'error' => true,
                'error_message' => 'Your transaction was not processed successfully',
                'http_code' => 500,
            ];
        }
    }

    public function setupIntent(Request $request, $secret_key)
    {
        Stripe::setApiKey($secret_key);

        $this->handleCustomer($request);
        $payment_intent_response = $this->handleSetupIntent($request);

        return $payment_intent_response;
    }

    public function paymentIntent(Request $request, $secret_key)
    {
        Stripe::setApiKey($secret_key);

        $this->handleCustomer($request);
        $payment_intent_response = $this->handlePaymentIntent($request);

        return $payment_intent_response;
    }

    public function getCustomerCards(Request $request, $secret_key)
    {
        Stripe::setApiKey($secret_key);

        $customer_cards = [];

        if ($request->input('customer_id')) {
            try {
                $payment_method_response = PaymentMethod::all([
                    'customer' => $request->input('customer_id'),
                    'type' => 'card',
                    'limit' => 100,
                ]);

                if (isset($payment_method_response->data) && ! empty($payment_method_response->data)) {
                    foreach ($payment_method_response->data as $customer_card) {
                        $customer_cards[] = [
                            'id' => $customer_card->id,
                            'brand' => $customer_card->card->brand,
                            'exp_month' => $customer_card->card->exp_month,
                            'exp_year' => $customer_card->card->exp_year,
                            'expiry' => str_pad($customer_card->card->exp_month, 2, '0', STR_PAD_LEFT).'/'.substr($customer_card->card->exp_year, 2),
                            'last4' => $customer_card->card->last4,
                            'number' => '**** **** **** '.$customer_card->card->last4,
                        ];
                    }
                }
            } catch (Exception $e) {
            }
        }

        return [
            'success' => true,
            'data' => [
                'customer_cards' => $customer_cards,
            ],
        ];
    }

    public function deleteCustomerCards(Request $request, $secret_key)
    {
        Stripe::setApiKey($secret_key);

        try {
            $payment_method_response = PaymentMethod::retrieve($request->input('id'));
            $payment_method_response->detach();
        } catch (InvalidRequestException $e) {
            return [
                'error' => true,
            ];
        }

        return [
            'success' => true,
        ];
    }
}
