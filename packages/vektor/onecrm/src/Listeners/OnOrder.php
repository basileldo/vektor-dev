<?php

namespace Vektor\OneCRM\Listeners;

use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Hash;
use Vektor\Checkout\Customer;
use Vektor\Checkout\Events\PaymentSuccess;
use Vektor\Checkout\Events\PaymentSuccessNotify;
use Vektor\Checkout\Utilities as CheckoutUtilities;
use Vektor\OneCRM\Model\Account;
use Vektor\OneCRM\Model\Contact;
use Vektor\OneCRM\Model\SalesOrder;
use Vektor\OneCRM\Model\Task;
use Vektor\OneCRM\Model\WrapperInvoice;
use Vektor\OneCRM\Model\WrapperOrder;
use Vektor\OneCRM\Model\WrapperPayment;
use Vektor\OneCRM\Model\WrapperShipping;
use Vektor\Utilities\Countries;
use Vektor\Utilities\Formatter;
use Vektor\Utilities\Utilities;

class OnOrder implements ShouldQueue
{
    // use InteractsWithQueue, SerializesModels;
    /**
     * The number of times the queued listener may be attempted.
     *
     * @var int
     */
    public $tries = 2;

    /**
     * Handle the event.
     */
    public function handle(PaymentSuccess $event): void
    {
        if (config('onecrm.enabled')) {
            $data = $event->data;
            $cart = $event->cart;
            $count = $event->count;
            $subtotal = $event->subtotal;
            $tax = $event->tax;
            $total = $event->total;
            $current_user = $event->current_user;
            $event->notification_email = null;
            $event->order_number = null;

            $email = Formatter::email($data['email']);
            $event->notification_email = $email;

            if ($current_user) {
                $email = $current_user->email;
            }

            if (config('checkout.customer_unique')) {
                $existing_customer = Customer::where('email', $email)->first();
                if ($existing_customer != null) {
                    return;
                }
            }

            $user = User::where('email', $email)->first();
            if (
                $user == null
                && isset($data['password'])
                && !empty($data['password'])
                && isset($data['password_confirmation'])
                && !empty($data['password_confirmation'])
            ) {
                $user_data = [
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'email' => $email,
                    'password' => Hash::make($data['password']),
                ];

                if (isset($data['stripe_customer_id'])) {
                    $user_data['configuration'] = [
                        'stripe_customer_id' => $data['stripe_customer_id']
                    ];
                }

                $user = User::create($user_data);
            }

            $user_array = [];
            if ($user == null) {
                return;
            }
            $user_array = $user->toArray();

            $singular_account_id = config('onecrm.account_id') ? true : false;
            $account_id = config('onecrm.account_id') ? config('onecrm.account_id') : Utilities::getNestedFlattenedValue($user_array, 'configuration.onecrm_account_id');
            $contact_id = Utilities::getNestedFlattenedValue($user_array, 'configuration.onecrm_contact_id');
            $shipping_required = config('checkout.shipping_required');
            $billing_required = config('checkout.billing_required');

            $full_name = implode(' ', array_filter([
                $data['first_name'],
                $data['last_name'],
            ]));

            if (config('onecrm.on_order.create.accounts')) {
                $_account = new Account();
                if ($shipping_required === false) {
                    $_account->shipping_before_billing = false;
                }

                $account_data = [
                    'name' => "{$full_name} - {$email}",
                    'phone_office' => $data['phone'],
                    'email1' => $email,
                ];

                if ($singular_account_id && !empty($account_id)) {
                    $account_data['id'] = $account_id;
                }

                if ($shipping_required == true) {
                    $account_data = array_merge($account_data, [
                        'shipping_address_street' => [
                            $data['shipping_address_line_1'],
                            $data['shipping_address_line_2'],
                        ],
                        'shipping_address_city' => $data['shipping_city'],
                        'shipping_address_state' => $data['shipping_county'],
                        'shipping_address_postalcode' => $data['shipping_postcode'],
                        'shipping_address_countrycode' => $data['shipping_country'],
                        'shipping_address_country' => Countries::convert($data['shipping_country'], 'iso2', 'name'),
                    ]);
                }

                if (($shipping_required == true && $data['same_as_shipping'] == false && $billing_required == true) || $shipping_required == false) {
                    $account_data = array_merge($account_data, [
                        'billing_address_street' => [
                            $data['billing_address_line_1'],
                            $data['billing_address_line_2'],
                        ],
                        'billing_address_city' => $data['billing_city'],
                        'billing_address_state' => $data['billing_county'],
                        'billing_address_postalcode' => $data['billing_postcode'],
                        'billing_address_countrycode' => $data['billing_country'],
                        'billing_address_country' => Countries::convert($data['billing_country'], 'iso2', 'name'),
                    ]);
                }

                $_account->fill($account_data);
                $account_response = $_account->persist();
            } else {
                $account_response = null;
                if (!empty($account_id)) {
                    $account_response = ['id' => $account_id];
                }
            }
            if ($account_response && isset($account_response['id'])) {
                $user_configuration = $user->configuration;
                $user_configuration['onecrm_account_id'] = $account_response['id'];
                $user->configuration = $user_configuration;
                $user->save();
            }

            if ($account_response && isset($account_response['id'])) {
                if (config('onecrm.on_order.create.contacts')) {
                    $_contact = new Contact();
                    if ($shipping_required === false) {
                        $_contact->shipping_before_billing = false;
                    }

                    $contact_data = [
                        'primary_account_id' => $account_response['id'],
                        'first_name' => $data['first_name'],
                        'last_name' => $data['last_name'],
                        'phone_work' => $data['phone'],
                        'email1' => $email,
                        'email_opt_in' => true,
                    ];

                    if (!empty($contact_id)) {
                        $contact_data['id'] = $contact_id;
                    }

                    if ($singular_account_id) {
                        if ($shipping_required == true) {
                            $contact_data = array_merge($contact_data, [
                                'primary_address_street' => [
                                    $data['shipping_address_line_1'],
                                    $data['shipping_address_line_2'],
                                ],
                                'primary_address_city' => $data['shipping_city'],
                                'primary_address_state' => $data['shipping_county'],
                                'primary_address_postalcode' => $data['shipping_postcode'],
                                'primary_address_countrycode' => $data['shipping_country'],
                                'primary_address_country' => Countries::convert($data['shipping_country'], 'iso2', 'name'),
                            ]);
                        }

                        if (($shipping_required == true && $data['same_as_shipping'] == false && $billing_required == true) || $shipping_required == false) {
                            $contact_data = array_merge($contact_data, [
                                'alt_address_street' => [
                                    $data['billing_address_line_1'],
                                    $data['billing_address_line_2'],
                                ],
                                'alt_address_city' => $data['billing_city'],
                                'alt_address_state' => $data['billing_county'],
                                'alt_address_postalcode' => $data['billing_postcode'],
                                'alt_address_countrycode' => $data['billing_country'],
                                'alt_address_country' => Countries::convert($data['billing_country'], 'iso2', 'name'),
                            ]);
                        }
                    }

                    $_contact->fill($contact_data);
                    $contact_response = $_contact->persist();
                } else {
                    $contact_response = null;
                    if (!empty($contact_id)) {
                        $contact_response = ['id' => $contact_id];
                    }
                }
            }
            if ($contact_response && isset($contact_response['id'])) {
                $user_configuration = $user->configuration;
                $user_configuration['onecrm_contact_id'] = $contact_response['id'];
                $user->configuration = $user_configuration;
                $user->save();
            }

            $order_response = null;

            if ($account_response && isset($account_response['id']) && $contact_response && isset($contact_response['id'])) {
                if (config('onecrm.on_order.create.sales_orders')) {
                    $_order = new WrapperOrder();
                    if ($shipping_required === false) {
                        $_order->_sales_order->shipping_before_billing = false;
                    }

                    $order_data = [
                        'name' => "[" . config('app.company.name') . "] - {$full_name}",
                        'amount' => $total,
                        'subtotal' => $subtotal,
                        'pretax' => $subtotal,
                    ];

                    $shipping_rates = array_values(array_filter($cart, CheckoutUtilities::class . '::detectShippingLines'));

                    if (count($shipping_rates) > 0) {
                        $shipping_rate = current($shipping_rates);
                        if (isset($shipping_rate['options']) && isset($shipping_rate['options']['shipping_provider_id'])) {
                            $order_data['shipping_provider_id'] = $shipping_rate['options']['shipping_provider_id'];
                        }
                    }

                    if ($shipping_required == true) {
                        $order_data = array_merge($order_data, [
                            'shipping_account_id' => $account_response['id'],
                            'shipping_contact_id' => $contact_response['id'],
                            'shipping_address_street' => [
                                $data['shipping_address_line_1'],
                                $data['shipping_address_line_2'],
                            ],
                            'shipping_address_city' => $data['shipping_city'],
                            'shipping_address_state' => $data['shipping_county'],
                            'shipping_address_postalcode' => $data['shipping_postcode'],
                            'shipping_address_country' => Countries::convert($data['shipping_country'], 'iso2', 'name'),
                            'shipping_address_countrycode' => $data['shipping_country'],
                        ]);
                    } else {
                        $order_data = array_merge($order_data, [
                            'billing_account_id' => $account_response['id'],
                            'billing_contact_id' => $contact_response['id']
                        ]);
                    }

                    if (($shipping_required == true && $data['same_as_shipping'] == false && $billing_required == true) || $shipping_required == false) {
                        $order_data = array_merge($order_data, [
                            'billing_address_street' => [
                                $data['billing_address_line_1'],
                                $data['billing_address_line_2'],
                            ],
                            'billing_address_city' => $data['billing_city'],
                            'billing_address_state' => $data['billing_county'],
                            'billing_address_postalcode' => $data['billing_postcode'],
                            'billing_address_country' => Countries::convert($data['billing_country'], 'iso2', 'name'),
                            'billing_address_countrycode' => $data['billing_country'],
                        ]);
                    }

                    $_order_lines = $cart;
                    $order_lines = CheckoutUtilities::transformLines($_order_lines);

                    if (! empty($order_lines)) {
                        $order_data['lines'] = $order_lines;
                    }

                    $_order->fill($order_data);
                    $order_response = $_order->persist();

                    $_sales_order = new SalesOrder();
                    $sales_order_response = $_sales_order->show($order_response['id']);
                    if ($sales_order_response && isset($sales_order_response['id'])) {
                        $event->order_number = "{$sales_order_response['prefix']}{$sales_order_response['so_number']}";
                    }
                }

                if ($order_response && $shipping_required == true && config('onecrm.on_order.create.shipping')) {
                    $shipping_rates = array_values(array_filter($cart, CheckoutUtilities::class . '::detectShippingLines'));
                    $shipping_rate = null;

                    if (count($shipping_rates) > 0) {
                        $shipping_rate = current($shipping_rates);
                    }

                    $_shipping = new WrapperShipping();

                    $shipping_data = [
                        'so_id' => $order_response['id'],
                        'name' => "[" . config('app.company.name') . "] - {$full_name}",
                        'shipping_cost' => ($shipping_rate != null && isset($shipping_rate['price'])) ? $shipping_rate['price'] : 0,
                        'shipping_account_id' => $account_response['id'],
                        'shipping_contact_id' => $contact_response['id'],
                        'shipping_address_street' => [
                            $data['shipping_address_line_1'],
                            $data['shipping_address_line_2'],
                        ],
                        'shipping_address_city' => $data['shipping_city'],
                        'shipping_address_state' => $data['shipping_county'],
                        'shipping_address_postalcode' => $data['shipping_postcode'],
                        'shipping_address_country' => Countries::convert($data['shipping_country'], 'iso2', 'name'),
                        'shipping_address_countrycode' => $data['shipping_country'],
                    ];

                    $_shipping_lines = array_values(array_filter($cart, CheckoutUtilities::class . '::detectProductLines'));
                    $shipping_lines = CheckoutUtilities::transformLines($_shipping_lines);

                    if (! empty($shipping_lines)) {
                        $shipping_data['lines'] = $shipping_lines;
                    }

                    $_shipping->fill($shipping_data);
                    $shipping_response = $_shipping->persist();
                }

                if ($order_response && config('onecrm.on_order.create.invoices')) {
                    $_invoice = new WrapperInvoice;

                    $invoice_data = [
                        'from_so_id' => $order_response['id'],
                        'name' => "[" . config('app.company.name') . "] - {$full_name}",
                        'amount' => $total,
                        'subtotal' => $subtotal,
                        'pretax' => $subtotal,
                        'billing_account_id' => $account_response['id'],
                        'billing_contact_id' => $contact_response['id'],
                    ];

                    $_invoice_lines = $cart;
                    $invoice_lines = CheckoutUtilities::transformLines($_invoice_lines);

                    if (! empty($invoice_lines)) {
                        $invoice_data['lines'] = $invoice_lines;
                    }

                    $_invoice->fill($invoice_data);
                    $invoice_response = $_invoice->persist();

                    if ($invoice_response && config('onecrm.on_order.create.payments')) {
                        $_payment = new WrapperPayment();

                        $customer_reference = $full_name;
                        if (isset($data['payment_intent_id'])) {
                            $customer_reference = $data['payment_intent_id'];
                        }

                        $_payment->fill([
                            'related_invoice_id' => $invoice_response['id'],
                            'account_id' => $account_response['id'],
                            'amount' => $total,
                            'total_amount' => $total,
                            'customer_reference' => $customer_reference,
                        ]);

                        $payment_response = $_payment->persist();
                    }
                }

                if ($order_response && config('onecrm.on_order.create.tasks')) {
                    $_task = new Task();

                    $_task->fill([
                        'name' => "[" . config('app.company.name') . "]" . (!empty($event->order_number) ? " - {$event->order_number}" : " - {$full_name}"),
                        'parent_id' => $order_response['id'],
                        'account_id' => $account_response['id'],
                    ]);

                    $task_response = $_task->persist();
                }
            }
        }

        if (config('checkout.customer_unique')) {
            Customer::create([
                'email' => $email,
            ]);
        }

        PaymentSuccessNotify::dispatch($event);
    }
}
