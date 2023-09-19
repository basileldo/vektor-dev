<?php

namespace Vektor\Paypal\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Vektor\Api\Api;

class PaypalModuleEnabled
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (config('paypal.enabled') === true) {
            return $next($request);
        }

        if ($request->ajax()) {
            $api = new Api();

            return $api->response([
                'error' => true,
                'http_code' => 404,
            ]);
        }

        return redirect()->route('base');
    }
}
