<?php

namespace Vektor\Checkout\Http\Controllers\Api;

use Illuminate\Http\Request;
use Vektor\Api\Http\Controllers\ApiController;
use Vektor\Checkout\Formatter;
use Vektor\Checkout\Product;
use Vektor\Checkout\Utilities as CheckoutUtilities;

class CheckoutController extends ApiController
{
    public function index(Request $request)
    {
        $products = [];

        if ($request->input('paginate', false)) {
            $_products = Product::with(['products' => function($query) {
                $query->orderBy('sort_order');
            }])->whereNull('parent_id')->orderBy('sort_order')->paginate($request->input('per_page', 1))->toArray();
            if (!empty($_products['data'])) {
                $_products['data'] = Formatter::products($_products['data']);
                $products = $_products;
            }
        } else {
            $products = CheckoutUtilities::products();
        }

        return $this->response([
            'success' => true,
            'data' => [
                'products' => collect($products),
            ],
        ]);
    }
}
