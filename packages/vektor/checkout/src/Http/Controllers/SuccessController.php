<?php

namespace Vektor\Checkout\Http\Controllers;

use Illuminate\Http\Request;
use Vektor\Api\Http\Controllers\ApiController;

class SuccessController extends ApiController
{
    public function index(Request $request)
    {
        return view('success');
    }
}
