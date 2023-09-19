<?php

namespace Vektor\Account;

use Illuminate\Http\Request;

class AccountPayment
{
    public function handle(Request $request)
    {
        return [
            'success' => true,
            'success_message' => 'Your transaction was processed successfully.',
        ];
    }
}
