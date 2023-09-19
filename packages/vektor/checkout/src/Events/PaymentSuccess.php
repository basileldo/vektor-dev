<?php

namespace Vektor\Checkout\Events;

use Auth;
use Cart;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentSuccess
{
    use Dispatchable, SerializesModels;

    public $data;

    public $cart;

    public $count;

    public $subtotal;

    public $tax;

    public $total;

    public $current_user;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($request)
    {
        $this->data = $request->all();
        $this->cart = array_values(Cart::content()->toArray());
        $this->count = Cart::count();
        $this->subtotal = Cart::subtotal();
        $this->tax = Cart::tax();
        $this->total = Cart::total();
        $this->current_user = Auth::user();

        Cart::destroy();
    }
}
