<?php

namespace Vektor\Checkout\Http\Controllers;

use Illuminate\Http\Request;
use Vektor\Api\Http\Controllers\ApiController;
use Vektor\Checkout\Formatter;
use Vektor\Checkout\Product;
use Vektor\Checkout\Utilities as CheckoutUtilities;

class ProductController extends ApiController
{
    public function index(Request $request)
    {
        if (config('checkout.as_base') === true || config('checkout.only') === true) {
            return redirect()->route('base');
        }

        $products = CheckoutUtilities::products();
        return view('shop', ['products' => collect($products)]);
    }

    public function show(Request $request, $slug)
    {
        $_product = Product::with(['products' => function($query) {
            $query->orderBy('sort_order');
        }])->whereNull('parent_id')->orderBy('sort_order')->where('slug', $slug)->first();

        if ($_product) {
            $_product = $_product->toArray();
            $product = Formatter::product($_product);
            return view('product', ['product' => collect($product)]);
        } 
        // else {
        //     $_product = Product::with(['products' => function($query) {
        //         $query->orderBy('sort_order');
        //     }, 'parent'])->orderBy('sort_order')->where('slug', $slug)->first();
        //     return redirect()->route('checkout.product.show', ['slug' => $_product->parent->slug]);
        // }

        return redirect()->route('checkout.product.index');
    }
}
