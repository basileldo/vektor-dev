<?php

namespace Vektor\Stripe\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Vektor\Api\Api;

class StripeModuleEnabled
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (config('stripe.enabled') === true || config('stripe.request.enabled') === true) {
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
