<?php

namespace Vektor\Checkout\Http\Controllers\Api;

use Cart;
use Illuminate\Http\Request;
use Vektor\Api\Http\Controllers\ApiController;
use Vektor\Checkout\Formatter as CheckoutFormatter;
use Vektor\Checkout\Product;
use Vektor\Checkout\Utilities as CheckoutUtilities;
use Vektor\Utilities\Formatter;

class CartController extends ApiController
{
    public function index(Request $request)
    {
        $cart = [];
        $product_count = 0;
        $product_subtotal = 0;
        $products = [];
        $shipping_subtotal = 0;
        $shipping = [];

        $cart_content = Cart::content();
        if ($cart_content->count() > 0) {
            foreach ($cart_content as $_cart_item) {
                if ($_cart_item->model) {
                    $_product = $_cart_item->model->toArray();
                    $_product = CheckoutFormatter::product($_product);
                    $cart_item = $_cart_item->toArray();
                    $cart_item['display_price'] = $_product['display_price'];
                    $cart_item = array_merge($cart_item, ['product' => $_product]);
                } else {
                    $cart_item = $_cart_item->toArray();
                }
                $cart[] = $cart_item;
                if (CheckoutUtilities::detectShippingLines($cart_item)) {
                    $shipping_subtotal += $cart_item['subtotal'];
                    $shipping[] = $cart_item;
                } else {
                    $product_count += $cart_item['qty'];
                    $product_subtotal += $cart_item['subtotal'];
                    $products[] = $cart_item;
                }
            }
        }

        return $this->response([
            'success' => true,
            'data' => [
                'items' => $cart,
                'product_items' => $products,
                'shipping_items' => $shipping,
                'count' => $product_count,
                'subtotal' => floatval(Cart::subtotal()),
                'product_subtotal' => $product_subtotal,
                'shipping' => $shipping_subtotal,
                'tax' => floatval(Cart::tax()),
                'total' => floatval(Cart::total()),
                'formatted' => [
                    'subtotal' => Formatter::currency(Cart::subtotal()),
                    'product_subtotal' => Formatter::currency($product_subtotal),
                    'shipping' => Formatter::currency($shipping_subtotal),
                    'tax' => Formatter::currency(Cart::tax()),
                    'total' => Formatter::currency(Cart::total()),
                ]
            ],
        ]);
    }

    public function store(Request $request)
    {
        $can_add_to_cart = true;
        $error_message = null;
        $product_id = $request->input('id');
        $product_name = $request->input('name');
        $product_qty = $request->input('qty');
        $product_options = $request->input('options', []);
        $parent_id = $product_options['parent_id'] ?? $product_id;
        $qty_per_order = $request->input('qty_per_order');
        $qty_per_order_grouping = $request->input('qty_per_order_grouping', 'id');
        if (! $qty_per_order_grouping || $qty_per_order_grouping == 'sku') {
            $qty_per_order_grouping = 'id';
        }

        if ($qty_per_order) {
            $cart = Cart::content()->groupBy('options.parent_id')->toArray();

            foreach ($cart as $cart_item_group_id => $cart_item_group) {
                if ($cart_item_group_id == $parent_id) {
                    $cumulative_qty = 0;
                    foreach ($cart_item_group as $cart_item) {
                        if ($qty_per_order_grouping == 'id') {
                            $cumulative_qty = $cart_item['qty'] + $cumulative_qty;
                        } elseif ($qty_per_order_grouping == 'all') {
                            if (empty(array_diff($cart_item['options'], $product_options))) {
                                $cumulative_qty = $cart_item['qty'] + $cumulative_qty;
                            }
                        } else {
                            if (
                                isset($cart_item['options'][$qty_per_order_grouping])
                                && isset($product_options[$qty_per_order_grouping])
                                && $cart_item['options'][$qty_per_order_grouping] == $product_options[$qty_per_order_grouping]
                            ) {
                                $cumulative_qty = $cart_item['qty'] + $cumulative_qty;
                            }
                        }
                    }
                    if ($cumulative_qty >= $qty_per_order) {
                        $can_add_to_cart = false;
                        $error_message = "You can't add more than {$qty_per_order} per order";
                    }
                }
            }

            if ($can_add_to_cart == true && $product_qty > $qty_per_order) {
                $can_add_to_cart = false;
                $error_message = "You can't add more than {$qty_per_order} per order";
            }
        }

        if ($can_add_to_cart == true) {
            // if (isset($product_options['id'])) {
            //     $product_id = $product_options['id'];
            //     unset($product_options['id']);
            // }
            $product_options['parent_id'] = $parent_id;

            $product_model = Product::find($product_id);

            $item = Cart::add([
                'id' => $product_id,
                'name' => $product_name,
                'qty' => $product_qty,
                'price' => $product_model ? $product_model->price : $request->input('price'),
                'weight' => $request->input('weight'),
                'options' => $product_options,
            ])->associate(Product::class);

            if ($product_model) {
                if (isset($product_model->configuration['tax_percentage'])) {
                    Cart::setTax($item->rowId, $product_model->configuration['tax_percentage']);
                }
            }

            return $this->response([
                'success' => true,
                'success_message' => "You added {$product_name} to your shopping cart",
                'http_code' => 201,
                'data' => [
                    'item' => $item->toArray(),
                ],
            ]);
        } else {
            return $this->response([
                'error' => true,
                'error_message' => $error_message,
                'http_code' => 403,
            ]);
        }
    }

    public function update(Request $request, $rowId)
    {
        $can_add_to_cart = true;
        $error_message = null;
        $product_id = null;
        $product_name = null;
        $product_qty = $request->input('qty');
        $product_options = [];
        $qty_per_order = $request->input('qty_per_order');
        $qty_per_order_grouping = $request->input('qty_per_order_grouping', 'id');
        if (! $qty_per_order_grouping || $qty_per_order_grouping == 'sku') {
            $qty_per_order_grouping = 'id';
        }

        if ($qty_per_order) {
            $ungrouped_cart = Cart::content()->toArray();
            if (isset($ungrouped_cart[$rowId])) {
                $product_id = $ungrouped_cart[$rowId]['id'];
                $product_name = $ungrouped_cart[$rowId]['name'];
                $product_options = $ungrouped_cart[$rowId]['options'];
            }

            $cart = Cart::content()->groupBy('id')->toArray();

            foreach ($cart as $cart_item_group_id => $cart_item_group) {
                if ($cart_item_group_id == $product_id) {
                    $cumulative_qty = 0;
                    foreach ($cart_item_group as $cart_item) {
                        if ($cart_item['rowId'] == $rowId) {
                            $cart_item['qty'] = $product_qty;
                        }
                        if ($qty_per_order_grouping == 'id') {
                            $cumulative_qty = $cart_item['qty'] + $cumulative_qty;
                        } elseif ($qty_per_order_grouping == 'all') {
                            if (empty(array_diff($cart_item['options'], $product_options))) {
                                $cumulative_qty = $cart_item['qty'] + $cumulative_qty;
                            }
                        } else {
                            if (
                                isset($cart_item['options'][$qty_per_order_grouping])
                                && isset($product_options[$qty_per_order_grouping])
                                && $cart_item['options'][$qty_per_order_grouping] == $product_options[$qty_per_order_grouping]
                            ) {
                                $cumulative_qty = $cart_item['qty'] + $cumulative_qty;
                            }
                        }
                    }
                    if ($cumulative_qty > $qty_per_order) {
                        $can_add_to_cart = false;
                        $error_message = "You can't add more than {$qty_per_order} per order";
                    }
                }
            }
        }

        if ($can_add_to_cart == true) {
            $item = Cart::update($rowId, $request->input('qty'));

            return $this->response([
                'success' => true,
                'success_message' => "You updated {$product_name} in your shopping cart",
                'data' => [
                    'item' => $item->toArray(),
                ],
            ]);
        } else {
            return $this->response([
                'error' => true,
                'error_message' => $error_message,
                'http_code' => 403,
            ]);
        }
    }

    public function remove(Request $request, $rowId)
    {
        if ($rowId == 'shipping') {
            $shipping_lines = Cart::search(function ($cartItem, $rowId) {
                return $cartItem->id === 'shipping';
            });
            if ($shipping_lines->count() > 0) {
                foreach ($shipping_lines as $shipping_line) {
                    $item = Cart::remove($shipping_line->rowId);
                }
            }
        } else {
            $item = Cart::remove($rowId);
        }

        return $this->response([
            'success' => true,
        ]);
    }

    public function destroy(Request $request)
    {
        Cart::destroy();

        return $this->response([
            'success' => true,
        ]);
    }
}
