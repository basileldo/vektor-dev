<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        return view('dashboard');
    }

    public function orders(Request $request)
    {
        return view('dashboard_onecrm_orders');
    }

    public function order(Request $request, $id)
    {
        return view('dashboard_onecrm_order', ['id' => $id]);
    }
}
