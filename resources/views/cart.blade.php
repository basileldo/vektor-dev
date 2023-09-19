@extends('layouts.default')
@section('title', 'Cart')

@section('content')
    <div class="py-4 py-8:2 py-12:3">
        <div class="container:xl mb-4 mb-8:3 -mt-6:3">
            <ul class="breadcrumbs">
                <li><a href="{{ route('checkout.product.index') }}">Back to Shop</a></li>
            </ul>
        </div>
        <div class="container:xl">
            <h1 class="text-gradient">Shopping cart</h1>
            <c-message :content="success_message" class="message--positive message--top" :trigger="is_success_message_shown" :autohide="true"></c-message>
            <c-message :content="error_message" class="message--negative message--top" :trigger="is_error_message_shown" :autohide="true"></c-message>
            <c-cart
            @update:cart="updateCart"
            @message:hide="hideMessage"
            @message:success="successMessage"
            @message:error="errorMessage">
                <template v-slot:default="cartScope">
                    <div class="spinner__wrapper" :class="{ is_loading: cartScope.is_loading == true }">
                        <div class="spinner"></div>
                    </div>
                    <template v-if="cartScope.product_items.length > 0 && cartScope.cart_fetched == true">
                        <div class="grid grid-cols-4:4 gap-6 mb-6">
                            <div :class="[{'col-span-3:4': !cartScope.hide_pricing}, {'col-span-4:4': cartScope.hide_pricing}]">
                                <div class="cart-items">
                                    <div class="cart-item" v-for="(item, item_index) in cartScope.product_items">
                                        <div class="item-image">
                                            <a :href="'{{ url('product') }}/' + item.slug">
                                                <img width="800" height="800" :src="item.image" :alt="item.name">
                                            </a>
                                        </div>
                                        <div class="item-content">
                                            <div>
                                                <div class="h6 mb-2"><a :href="'{{ url('product') }}/' + item.slug">@{{ item.name }}</a></div>
                                                <div class="text-sm" v-html="cartScope.formatOptions(item.options)"></div>
                                            </div>
                                            <div>
                                                <div class="flex items-center" :class="((item.qty_per_order != 1 && (item.qty_per_order_grouping != 'id' && item.qty_per_order_grouping != 'sku')) || (!cartScope.hide_pricing)) ? 'justify-between' : 'justify-end'">
                                                    <div class="flex items-center justify-between" v-if="!cartScope.hide_pricing">
                                                        <span class="mr-4">@{{ cartScope.formatPrice(item.display_price) }}</span> <c-input :name="'qty[' + item_index + ']'" v-model="item.qty" type="number:buttons" class="sm" @update:model-value="cartScope.updateCartItem(item)" v-if="item.qty_per_order != 1 && (item.qty_per_order_grouping != 'id' && item.qty_per_order_grouping != 'sku')"></c-input> <span class="ml-4" v-if="item.qty_per_order != 1 && (item.qty_per_order_grouping != 'id' && item.qty_per_order_grouping != 'sku')">@{{ cartScope.formatPrice(item.display_price * item.qty) }}</span>
                                                    </div>
                                                    <div class="flex items-center justify-between" v-if="cartScope.hide_pricing && item.qty_per_order != 1 && (item.qty_per_order_grouping != 'id' && item.qty_per_order_grouping != 'sku')">
                                                        <c-input :name="'qty[' + item_index + ']'" v-model="item.qty" type="number:buttons" class="sm" @update:model-value="cartScope.updateCartItem(item)"></c-input>
                                                    </div>
                                                    <a @click="cartScope.deleteCartItem(item)" title="Delete" class="item-delete text-xs text-rose-600 ml-5 no-underline whitespace-nowrap"><span><svg class="inline" xmlns="http://www.w3.org/2000/svg"width="10.9px" height="14px" viewBox="0 0 10.9 14"><path class="fill-current" d="M0.8,12.4c0,0.9,0.7,1.6,1.6,1.6h6.2c0.9,0,1.6-0.7,1.6-1.6V3.1H0.8V12.4z M8.2,0.8L7.4,0H3.5L2.7,0.8H0v1.6 h10.9V0.8H8.2z"/></svg></span><span class="ml-1 underline">Remove</span></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-if="!cartScope.hide_pricing">
                                <div style="position: sticky; top: 110px;">
                                    <div class="shadow-box p-5 p-6:3">
                                        <table>
                                            <tbody>
                                                <tr><td>Subtotal</td><td>@{{ cartScope.formatted.product_subtotal }}</td></tr>
                                                <tr v-if="cartScope.shipping_items.length > 0"><td>Shipping</td><td>@{{ cartScope.formatted.shipping }}</td></tr>
                                                <tr><td>Tax</td><td>@{{ cartScope.formatted.tax }}</td></tr>
                                                <tr><td>Total</td><td><strong>@{{ cartScope.formatted.total }}</strong></td></tr>
                                            </tbody>
                                        </table>
                                        <div class="text-center">
                                            <a href="{{ route('checkout.checkout.index') }}" class="btn block bg-primary border-primary text-primary_contrasting">Checkout</a>
                                            <a class="btn block border-transparent text-sm underline" @click="cartScope.clearCart">Clear Cart</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="ml-auto:1e flex:2 items-center justify-between">
                            <div class="mb-2:1e"><a href="{{ route('checkout.product.index') }}" class="btn:sm">Continue shopping</a></div>
                            <div v-if="cartScope.hide_pricing">
                                <a class="btn border-transparent text-sm underline" @click="cartScope.clearCart">Clear cart</a>
                                <a href="{{ route('checkout.checkout.index') }}" class="btn bg-primary border-primary text-primary_contrasting">Checkout</a>
                            </div>
                        </div>
                    </template>

                    <template v-if="cartScope.product_items.length == 0 && cartScope.cart_fetched == true">
                        <p>You have no items in your shopping cart.</p>
                        <p>
                            <a href="{{ route('checkout.product.index') }}" class="btn bg-primary border-primary text-primary_contrasting">Back to Shop</a>
                        </p>
                    </template>
                </template>
            </c-cart>
        </div>
    </div>
@endsection

@section('config')
'checkout.products.index': {!! $products !!},
@endsection