<?php

namespace Vektor\Checkout\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentSuccessNotify
{
    use Dispatchable, SerializesModels;

    public $data;

    public $cart;

    public $count;

    public $subtotal;

    public $tax;

    public $total;

    public $current_user;

    public $notification_email;

    public $order_number;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(PaymentSuccess $payment_success_event)
    {
        $this->data = $payment_success_event->data;
        $this->cart = $payment_success_event->cart;
        $this->count = $payment_success_event->count;
        $this->subtotal = $payment_success_event->subtotal;
        $this->tax = $payment_success_event->tax;
        $this->total = $payment_success_event->total;
        $this->current_user = $payment_success_event->current_user;
        $this->notification_email = null;
        if (!empty($payment_success_event->notification_email)) {
            $this->notification_email = $payment_success_event->notification_email;
        }
        $this->order_number = null;
        if (!empty($payment_success_event->order_number)) {
            $this->order_number = $payment_success_event->order_number;
        }
    }
}
