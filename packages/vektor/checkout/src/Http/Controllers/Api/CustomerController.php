<?php

namespace Vektor\Checkout\Http\Controllers\Api;

use Illuminate\Http\Request;
use Vektor\Api\Http\Controllers\ApiController;
use Vektor\Checkout\Customer;

class CustomerController extends ApiController
{
    public function showByEmail(Request $request)
    {
        $customer = null;

        if (config('checkout.customer_unique')) {
            $customer = Customer::where('email', $request->input('email'))->first();
        }

        return $this->response([
            'success' => ($customer != null),
        ]);
    }
}
