@php
$navigation_items = [];
if (config('app.external.url') && config('app.external.label')) {
    $navigation_items[] = [ 'title' => config('app.external.label'), 'href' => config('app.external.url'), 'target' => '_blank' ];
}
if (config('checkout.only') === false) {
    $navigation_items[] = [ 'title' => 'DRINK MENU', 'href' => route('tabs') ];
    $navigation_items[] = [ 'title' => 'FOOD MENU', 'href' => route('article') ];
    $navigation_items[] = [ 'title' => 'EVENTS', 'href' => route('map') ];
    $navigation_items[] = [ 'title' => 'ABOUT US', 'href' => route('contact') ];
}

$routes = Route::getRoutes();
$named_api_routes = [];

foreach ($routes as $route) {
    if (preg_match('/^\/?api/', $route->getPrefix())) {
        $route_name = $route->getName();

        if ($route_name) {
            $route_url = $route->uri();
            $route_parameters = $route->parameterNames();

            if (!empty($route_parameters)) {
                foreach ($route_parameters as $parameter_name) {
                    $route_url = str_replace('{' . $parameter_name . '}', '', $route_url);
                }
            }

            $route_url = trim($route_url, '/');
            $named_api_routes[$route_name] = url($route_url);
        }
    }
}
@endphp

<script id="_configParams">
window._configParams = {
'env': '<?php echo config('app.env'); ?>',
'base': '{{ route('base') }}',
'user.is_logged_in': <?php echo auth()->check() ? 'true' : 'false'; ?>,
@if (!empty($named_api_routes))
@php
ksort($named_api_routes);
@endphp
@foreach ($named_api_routes as $named_api_route_name => $named_api_route_url)
{!! "'" . $named_api_route_name . "': '" . $named_api_route_url . "'," !!}
@endforeach
@endif
'navigation_items': {!! json_encode($navigation_items) !!},
'checkout.enabled': <?php echo config('checkout.enabled') === true ? 'true' : 'false'; ?>,
'checkout.pagination.enabled': <?php echo config('checkout.pagination.enabled') === true ? 'true' : 'false'; ?>,
'checkout.pagination.per_pages': [<?php echo config('checkout.pagination.per_pages'); ?>],
'checkout.hide_pricing': <?php echo config('checkout.hide_pricing') === true ? 'true' : 'false'; ?>,
'checkout.billing_required': <?php echo config('checkout.billing_required') === true ? 'true' : 'false'; ?>,
'checkout.shipping_required': <?php echo config('checkout.shipping_required') === true ? 'true' : 'false'; ?>,
'checkout.customer_unique': <?php echo config('checkout.customer_unique') === true ? 'true' : 'false'; ?>,
'checkout.email_domain_check.enabled': <?php echo config('checkout.email_domain_check.enabled') === true ? 'true' : 'false'; ?>,
'checkout.email_domain_check.list': '<?php echo config('checkout.email_domain_check.list'); ?>',
'checkout.agree_terms': <?php echo config('checkout.agree_terms') === true ? 'true' : 'false'; ?>,
'payments.account.enabled': <?php echo config('account.enabled') === true ? 'true' : 'false'; ?>,
'payments.cash.enabled': <?php echo config('cash.enabled') === true ? 'true' : 'false'; ?>,
'payments.paypal.enabled': <?php echo config('paypal.enabled') === true ? 'true' : 'false'; ?>,
'payments.stripe.enabled': <?php echo config('stripe.enabled') === true ? 'true' : 'false'; ?>,
'payments.stripe.public_key': '<?php echo config('stripe.public_key'); ?>',
'payments.stripe.request.enabled': <?php echo config('stripe.request.enabled') === true ? 'true' : 'false'; ?>,
@yield('config')
};
</script>

<?php
    $override_css = resource_path('assets/style.css');
    if (file_exists($override_css)) {
        echo "<style>\n";
            require_once $override_css;
        echo "\n</style>";
    }
?>