<?php

namespace Vektor\Checkout\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CheckoutComplete
{
    use Dispatchable, SerializesModels;

    public $request;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($request)
    {
        $this->request = $request;
    }
}
