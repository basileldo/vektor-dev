<?php

namespace Vektor\Checkout\Http\Controllers;

use Auth;
use Cart;
use Illuminate\Http\Request;
use Vektor\Api\Http\Controllers\ApiController;
use Vektor\Utilities\Utilities;

class CheckoutController extends ApiController
{
    public function index(Request $request)
    {
        if (Cart::count() == 0) {
            return redirect()->route('checkout.cart.index');
        }

        $user = Auth::user();
        $stripe_customer_id = null;

        if ($user) {
            $user_array = $user->toArray();
            $stripe_customer_id = Utilities::getNestedFlattenedValue($user_array, 'configuration.stripe_customer_id');
        }

        return view('checkout', ['stripe_customer_id' => $stripe_customer_id]);
    }
}
