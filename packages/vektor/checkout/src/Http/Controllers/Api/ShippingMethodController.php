<?php

namespace Vektor\Checkout\Http\Controllers\Api;

use Illuminate\Http\Request;
use Vektor\Api\Http\Controllers\ApiController;
use Vektor\Checkout\ShippingMethod;
use Vektor\Checkout\Utilities as CheckoutUtilities;
use Vektor\Utilities\Formatter;

class ShippingMethodController extends ApiController
{
    public function index(Request $request)
    {
        $shipping_methods = [];

        $_shipping_methods = ShippingMethod::all()->toArray();

        if (!empty($_shipping_methods) > 0) {
            foreach($_shipping_methods as $_shipping_method) {
                $shipping_methods[] = array_merge($_shipping_method, [
                    'is_hidden' => !$_shipping_method['is_active'],
                    'is_disabled' => false,
                    'display_price' => CheckoutUtilities::addPercentage($_shipping_method['price'], 20),
                    'formatted' => [
                        'price' => Formatter::currency($_shipping_method['price']),
                        'display_price' => Formatter::currency(CheckoutUtilities::addPercentage($_shipping_method['price'], 20)),
                    ]
                ]);
            }
        }

        return $this->response([
            'success' => true,
            'data' => [
                'shipping_methods' => $shipping_methods,
            ],
        ]);
    }
}
