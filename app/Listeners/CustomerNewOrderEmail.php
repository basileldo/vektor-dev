<?php

namespace App\Listeners;

use App\Mail\OrderCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Vektor\Checkout\Events\PaymentSuccessNotify;
use Vektor\Checkout\Utilities as CheckoutUtilities;
use Vektor\Utilities\Formatter;


class CustomerNewOrderEmail implements ShouldQueue
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
    public function handle(PaymentSuccessNotify $event): void
    {
        if (config('onecrm.enabled')) {
            $data = $event->data;
            $cart = $event->cart;
            $count = $event->count;
            $subtotal = $event->subtotal;
            $tax = $event->tax;
            $total = $event->total;

            $email = Formatter::email($data['email']);
            if (!empty($event->notification_email)) {
                $email = Formatter::email($event->notification_email);
            }

            $product_subtotal = 0;
            $shipping_subtotal = 0;

            $_email_product_lines = array_values(array_filter($cart, CheckoutUtilities::class . '::detectProductLines'));
            $email_product_lines = CheckoutUtilities::transformLines($_email_product_lines, true);

            if (!empty($email_product_lines)) {
                foreach ($email_product_lines as $email_product_line) {
                    $product_subtotal = $product_subtotal + $email_product_line['unit_price'];
                }
            }

            $_email_shipping_lines = array_values(array_filter($cart, CheckoutUtilities::class . '::detectShippingLines'));
            $email_shipping_lines = CheckoutUtilities::transformLines($_email_shipping_lines, true);

            if (!empty($email_shipping_lines)) {
                foreach ($email_shipping_lines as $email_shipping_line) {
                    $shipping_subtotal = $shipping_subtotal + $email_shipping_line['unit_price'];
                }
            }

            $data['shipping_address'] = implode('<br />', array_map(function ($address_line) {
                return trim($address_line);
            }, array_values(array_filter([
                $data['shipping_address_line_1'],
                $data['shipping_address_line_2'],
                $data['shipping_city'],
                $data['shipping_county'],
                $data['shipping_postcode'],
            ]))));

            $data['billing_address'] = implode('<br />', array_map(function ($address_line) {
                return trim($address_line);
            }, array_values(array_filter([
                $data['billing_address_line_1'],
                $data['billing_address_line_2'],
                $data['billing_city'],
                $data['billing_county'],
                $data['billing_postcode'],
            ]))));

            Mail::to($email)->send(new OrderCreated([
                'data' => $data,
                'notification_email' => $event->notification_email,
                'order_number' => $event->order_number,
                'product_lines' => $email_product_lines,
                'shipping_lines' => $email_shipping_lines,
                'product_subtotal' => Formatter::currency($product_subtotal),
                'shipping_subtotal' => Formatter::currency($shipping_subtotal),
                'tax' => Formatter::currency($tax),
                'total' => Formatter::currency($total),
            ]));
        }
    }
}
