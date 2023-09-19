@extends('layouts.default')
@section('title', 'Checkout')

@section('content')
    <div class="py-4 py-8:2 py-12:3">
        <div class="container:xl mb-4 mb-8:3 -mt-6:3">
            <ul class="breadcrumbs">
                <li><a href="{{ route('checkout.cart.index') }}">Back to Cart</a></li>
            </ul>
        </div>
        <div class="container:lg">
            <c-checkout>
                <template v-slot:default="checkoutScope">
                    <template v-if="checkoutScope.product_items.length > 0">
                        <c-message content="You have logged in successfully." class="message--positive message--top" :trigger="checkoutScope.logging_in" :autohide="true"></c-message>
                        <h1 class="text-gradient">Checkout</h1>

                        <div class="shadow-box p-5 mb-6" v-if="!checkoutScope.hide_pricing">
                            <table>
                                <thead>
                                    <tr class="text-sm">
                                        <th>Name</th>
                                        <th>Price</th>
                                        <th>Qty</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="text-sm" v-for="(item, item_index) in checkoutScope.product_items">
                                        <td><strong>@{{ item.name }}</strong><template v-if="checkoutScope.formatOptions(item.options)"> - <span class="text-xs" v-html="checkoutScope.formatOptions(item.options)"></span></template></td>
                                        <td>@{{ checkoutScope.formatPrice(item.display_price) }}</td>
                                        <td>@{{ item.qty }}</td>
                                        <td>@{{ checkoutScope.formatPrice(item.display_price * item.qty) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <c-form
                            :name="checkoutScope.ref"
                            :ref="checkoutScope.ref"
                            method="post"
                            :action="checkoutScope.action"
                            :field_values="checkoutScope.field_values"
                            :field_storage="checkoutScope.field_storage" :field_validation_rules="checkoutScope.validation_rules" :field_validation_messages="checkoutScope.validation_messages"
                        >
                            <template v-slot:fields="form">
                                <div class="mb-6">
                                    <div class="mb-4">
                                        <h3>Personal details</h3>
                                    </div>
                                    <div class="grid:3 grid-cols-2:3 gap-x-4:3">
                                        <c-input name="first_name" label="First Name" v-model="form.field_values.first_name" :validationrule="form.validation_rules.first_name" :validationmsg="form.validation_messages.first_name" autocomplete="given-name"></c-input>
                                        <c-input name="last_name" v-model="form.field_values.last_name" :validationrule="form.validation_rules.last_name" :validationmsg="form.validation_messages.last_name" label="Last Name" autocomplete="family-name"></c-input>
                                        <c-input name="email" v-model="form.field_values.email" :validationrule="form.validation_rules.email" :validationmsg="form.validation_messages.email" label="Email" autocomplete="email" type="email"></c-input>
                                        <c-input name="phone" type="tel" v-model="form.field_values.phone" :validationrule="form.validation_rules.phone" :validationmsg="form.validation_messages.phone" label="Phone Number" autocomplete="tel"></c-input>

                                        <template v-if="checkoutScope.exists === false">
                                            <div class="col-span-2:3 mb-4">
                                                <c-message class="message--warning" content="Please enter a password below to create an account." :trigger="true" :required="true"></c-message>
                                            </div>
                                            <c-input class="mb-0" name="password" type="password" label="Password" placeholder="Enter password" v-model="form.field_values.password" :validationrule="form.validation_rules.password" :validationmsg="form.validation_messages.password" autocomplete="new-password"></c-input>
                                            <c-input class="mb-0" name="password_confirmation" type="password" label="Confirmation Password" placeholder="Confirmation password" v-model="form.field_values.password_confirmation" :validationrule="form.validation_rules.password_confirmation" :validationmsg="form.validation_messages.password_confirmation" autocomplete="new-password"></c-input>
                                        </template>
                                        <template v-if="checkoutScope.exists === true">
                                            <div class="col-span-2:3 mb-4">
                                                <c-message class="message--warning" content="Please enter your existing password below to be logged in." :trigger="true" :required="true"></c-message>
                                                <c-input class="mb-0" name="password" type="password" label="Password" placeholder="Enter password" v-model="form.field_values.password" :validationrule="form.validation_rules.password" :validationmsg="form.validation_messages.password"></c-input>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                                <div class="mb-6">
                                    <div class="mb-4">
                                        <h3>Shipping address</h3>
                                    </div>
                                    <div class="grid:3 grid-cols-2:3 gap-x-4:3">
                                        <c-input name="shipping_address_line_1" v-model="form.field_values.shipping_address_line_1" :validationrule="form.validation_rules.shipping_address_line_1" :validationmsg="form.validation_messages.shipping_address_line_1" label="Address Line 1" autocomplete="shipping address-line1"></c-input>
                                        <c-input name="shipping_address_line_2" v-model="form.field_values.shipping_address_line_2" :validationrule="form.validation_rules.shipping_address_line_2" :validationmsg="form.validation_messages.shipping_address_line_2" label="Address Line 2" autocomplete="shipping address-line2"></c-input>
                                        <c-input name="shipping_city" v-model="form.field_values.shipping_city" :validationrule="form.validation_rules.shipping_city" :validationmsg="form.validation_messages.shipping_city" label="Town/City" autocomplete="shipping address-level2"></c-input>
                                        <c-input name="shipping_county" v-model="form.field_values.shipping_county" :validationrule="form.validation_rules.shipping_county" :validationmsg="form.validation_messages.shipping_county" label="County" autocomplete="shipping address-level1"></c-input>
                                        <c-input name="shipping_postcode" v-model="form.field_values.shipping_postcode" :validationrule="form.validation_rules.shipping_postcode" :validationmsg="form.validation_messages.shipping_postcode" label="Postcode" autocomplete="shipping postal-code"></c-input>
                                        <c-input name="shipping_country" v-model="form.field_values.shipping_country" :validationrule="form.validation_rules.shipping_country" :validationmsg="form.validation_messages.shipping_country" label="Country" autocomplete="shipping country-name" type="select" :options="checkoutScope.countries"></c-input>
                                    </div>
                                </div>
                                <div class="mb-6 -mt-6:3" v-if="checkoutScope.billing_required">
                                    <c-input name="same_as_shipping" type="checkbox" valuelabel="My billing and shipping address are the same" v-model="form.field_values.same_as_shipping" :validationrule="form.validation_rules.same_as_shipping" :validationmsg="form.validation_messages.same_as_shipping"></c-input>
                                </div>
                                <div class="mb-6" v-if="checkoutScope.billing_required" v-show="!form.field_values.same_as_shipping">
                                    <div class="mb-4">
                                        <h3>Billing address</h3>
                                    </div>
                                    <div class="grid:3 grid-cols-2:3 gap-x-4:3">
                                        <c-input name="billing_address_line_1" v-model="form.field_values.billing_address_line_1" :validationrule="form.validation_rules.billing_address_line_1" :validationmsg="form.validation_messages.billing_address_line_1" label="Address Line 1" autocomplete="billing address-line1"></c-input>
                                        <c-input name="billing_address_line_2" v-model="form.field_values.billing_address_line_2" :validationrule="form.validation_rules.billing_address_line_2" :validationmsg="form.validation_messages.billing_address_line_2" label="Address Line 2" autocomplete="billing address-line2"></c-input>
                                        <c-input name="billing_city" v-model="form.field_values.billing_city" :validationrule="form.validation_rules.billing_city" :validationmsg="form.validation_messages.billing_city" label="Town/City" autocomplete="billing address-level2"></c-input>
                                        <c-input name="billing_county" v-model="form.field_values.billing_county" :validationrule="form.validation_rules.billing_county" :validationmsg="form.validation_messages.billing_county" label="County" autocomplete="billing address-level1"></c-input>
                                        <c-input name="billing_postcode" v-model="form.field_values.billing_postcode" :validationrule="form.validation_rules.billing_postcode" :validationmsg="form.validation_messages.billing_postcode" label="Postcode" autocomplete="billing postal-code"></c-input>
                                        <c-input name="billing_country" v-model="form.field_values.billing_country" :validationrule="form.validation_rules.billing_country" :validationmsg="form.validation_messages.billing_country" label="Country" autocomplete="billing country-name" type="select" :options="checkoutScope.countries"></c-input>
                                    </div>
                                </div>
                                <div class="mb-6">
                                    <h3>Shipping methods</h3>
                                    <div class="shipping_methods">
                                        <div class="shipping_method" v-for="shipping_method in checkoutScope.available_shipping_methods" :class="{ 'is_selected': form.field_values.shipping_method == shipping_method.code }" @click="checkoutScope.changeShippingMethod(form, shipping_method)" :disabled="shipping_method.is_disabled">
                                            <div class="method_content">
                                                <header>@{{ shipping_method.name }}</header>
                                                <span @click.stop v-if="shipping_method.description" v-html="shipping_method.description"></span>
                                            </div>
                                            <div class="method_price"><span>@{{ shipping_method.formatted.display_price }}</span></div>
                                        </div>
                                    </div>
                                    <span class="field__message--error" v-if="form.validation_rules.shipping_method.$invalid && form.validation_rules.shipping_method.$dirty">Please select a shipping method</span>
                                </div>
                                <div class="mb-6" v-if="!checkoutScope.hide_pricing">
                                    <h3>Order totals</h3>
                                    <div class="shadow-box p-5 mb-8">
                                        <table>
                                            <tbody>
                                                <tr class="text-sm"><th>Subtotal</th><td>@{{ checkoutScope.formatted.product_subtotal }}</td></tr>
                                                <tr class="text-sm" v-if="checkoutScope.shipping_items.length > 0 && !form.validation_rules.shipping_method.$invalid"><th>Shipping</th><td>@{{ checkoutScope.formatted.shipping }}</td></tr>
                                                <tr class="text-sm"><th>Tax</th><td>@{{ checkoutScope.formatted.tax }}</td></tr>
                                                <tr class="text-sm"><th>Total</th><td>@{{ checkoutScope.formatted.total }}</td></tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="mb-6 -mt-4" v-if="checkoutScope.agree_terms">
                                    <c-input label="Terms & Conditions Agreement" name="agree_terms" type="switch" valuelabel="I agree to the <a target='_blank' href='{{ config('app.terms') ? config('app.terms') : route('terms') }}' class='text-primary'>Terms & Conditions</a>" v-model="form.field_values.agree_terms" :validationrule="form.validation_rules.agree_terms" :validationmsg="form.validation_messages.agree_terms"></c-input>
                                </div>

                                <h3>Payment details</h3>
                                <c-message :content="form.response.error_message" class="message--negative" :trigger="form.response.error"></c-message>
                                <c-message :content="form.response.success_message" class="message--positive" :trigger="form.response.success"></c-message>

                                @if (config('account.enabled') === true)
                                    <c-payment_account
                                        @validate="form.validate"
                                        @load="form.load"
                                        @unload="form.unload"
                                        @success="form.successResponse"
                                        @fail="form.failResponse"

                                        :additional_data="form.field_values"
                                        :is_valid="!form.validation_rules.$invalid"
                                        :amount="checkoutScope.total"
                                    ></c-payment_account>
                                @endif

                                @if (config('cash.enabled') === true)
                                    <c-payment_cash
                                        @validate="form.validate"
                                        @load="form.load"
                                        @unload="form.unload"
                                        @success="form.successResponse"
                                        @fail="form.failResponse"

                                        :additional_data="form.field_values"
                                        :is_valid="!form.validation_rules.$invalid"
                                        :amount="checkoutScope.total"
                                    ></c-payment_cash>
                                @endif

                                @if (config('paypal.enabled') === true)
                                    <c-payment_paypal
                                        @validate="form.validate"
                                        @load="form.load"
                                        @unload="form.unload"
                                        @success="form.successResponse"
                                        @fail="form.failResponse"

                                        :additional_data="form.field_values"
                                        :is_valid="!form.validation_rules.$invalid"
                                        :amount="checkoutScope.total"
                                    ></c-payment_paypal>
                                @endif

                                @if (config('stripe.request.enabled') === true)
                                    <c-payment_stripe_request
                                        @validate="form.validate"
                                        @load="form.load"
                                        @unload="form.unload"
                                        @success="form.successResponse"
                                        @fail="form.failResponse"

                                        :additional_data="form.field_values"
                                        :is_valid="!form.validation_rules.$invalid"
                                        :amount="checkoutScope.total"
                                        :customer_id="form.field_values.stripe_customer_id"
                                    ></c-payment_stripe_request>
                                @endif

                                @if (config('stripe.enabled') === true)
                                    <c-payment_stripe
                                        @validate="form.validate"
                                        @load="form.load"
                                        @unload="form.unload"
                                        @success="form.successResponse"
                                        @fail="form.failResponse"

                                        :additional_data="form.field_values"
                                        :is_valid="!form.validation_rules.$invalid"
                                        :amount="checkoutScope.total"
                                        :customer_id="form.field_values.stripe_customer_id"
                                        :billing_address_city="form.field_values.billing_city"
                                        :billing_address_country="form.field_values.billing_country"
                                        :billing_address_line1="form.field_values.billing_address_line_1"
                                        :billing_address_line2="form.field_values.billing_address_line_2"
                                        :billing_address_postal_code="form.field_values.billing_postcode"
                                        :billing_address_state="form.field_values.billing_county"
                                        :billing_email="form.field_values.email"
                                        :billing_name="checkoutScope.full_name"
                                        :billing_phone="form.field_values.phone"
                                        :can_save_cards="true"
                                    ></c-payment_stripe>
                                @endif
                            </template>
                        </c-form>
                    </template>

                    <template v-if="checkoutScope.product_items.length == 0 && checkoutScope.cart_fetched">
                        <h1 class="text-gradient">Shopping cart</h1>
                        <p>You have no items in your shopping cart.</p>
                        <p>
                            <a href="{{ route('checkout.product.index') }}" class="btn bg-primary border-primary text-primary_contrasting">Back to Shop</a>
                        </p>
                    </template>
                </template>
            </c-checkout>
        </div>
    </div>
@endsection

@section('config')
'payments.stripe.stripe_customer_id': '{!! $stripe_customer_id !!}',
@endsection