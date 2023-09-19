<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Vektor\Checkout\Formatter;
use Vektor\Checkout\Product;
use Vektor\Checkout\Utilities as CheckoutUtilities;

class PageController extends Controller
{
    public function base(Request $request)
    {
        if (config('checkout.as_base') === true || config('checkout.only') === true) {
            $products = CheckoutUtilities::products();
            return view('shop', ['products' => collect($products)]);
        }
        return view('base');
    }

    public function about(Request $request)
    {
        if (config('checkout.as_base') === false || config('checkout.only') === true) {
            return redirect()->route('base');
        }
        return view('base');
    }

    public function tabs(Request $request)
    {
        return view('tabs');
    }

    public function article(Request $request)
    {
        return view('article');
    }

    public function map(Request $request)
    {
        return view('map');
    }

    public function contact(Request $request)
    {
        return view('contact');
    }

    public function terms(Request $request)
    {
        return view('terms');
    }

    public function policy(Request $request)
    {
        return view('policy');
    }
}
