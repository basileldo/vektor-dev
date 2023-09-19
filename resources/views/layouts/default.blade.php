@include('partials.head_start')
<link href="{{ url('dist/style.css?v=' . config('app.static_version')) }}" rel="stylesheet">
@include('partials.head_end')
</head>

<body>
    <div class="document__wrapper" v-cloak>
        @include('partials.header')
        <main class="document__content" role="main" aria-label="Document Content">
            @yield('content')
        </main>
        @include('partials.footer')
    </div>
    @include('partials.config')
    @if (config('checkout.enabled') === true && in_array(request()->route()->getName(), ['dashboard', 'checkout.checkout.index']))
        @if (config('stripe.enabled') === true || config('stripe.request.enabled') === true)
        <script src="https://js.stripe.com/v3/" v-if="cookiePreferences.allowNecessary"></script>
        @endif
        @if (config('paypal.enabled') === true)
        <script src="https://www.paypalobjects.com/api/checkout.js"></script>
        @endif
    @endif
    <script defer src="{{ url('dist/bundle.js?v=' . config('app.static_version')) }}"></script>
    <!-- <script>
        window.ga = function () { ga.q.push(arguments) }; ga.q = []; ga.l = +new Date;
        ga('create', 'UA-XXXXX-Y', 'auto'); ga('send', 'pageview')
    </script>
    <script src="https://www.google-analytics.com/analytics.js" async defer></script> -->
</body>

</html>