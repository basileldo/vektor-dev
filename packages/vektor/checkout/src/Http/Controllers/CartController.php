<?php

namespace Vektor\Checkout\Http\Controllers;

use Illuminate\Http\Request;
use Vektor\Api\Http\Controllers\ApiController;
use Vektor\Checkout\Formatter;
use Vektor\Checkout\Product;
use Vektor\Checkout\Utilities as CheckoutUtilities;

class CartController extends ApiController
{
    public function index(Request $request)
    {
        $products = CheckoutUtilities::products();
        return view('cart', ['products' => collect($products)]);
    }
}
