<!doctype html>
<html class="no-js" lang="en-GB">

<head>
    <!-- Google Tag Manager Consent Initialization -->
    <!-- Wait for consent to drop analytics cookies -->
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() {
            window.dataLayer.push(arguments);
        }

        gtag("consent", "default", {
            ad_storage: "denied",
            analytics_storage: "denied",
            functionality_storage: "denied",
            personalization_storage: "denied",
            security_storage: "granted",
            wait_for_update: 2000,
        });
        gtag("set", "ads_data_redaction", true);

        // PASTE GTM SNIPPET HERE
    </script>
    <!-- End Google Tag Manager -->


    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">

    <title>{{ config('app.name') }}@hasSection('title') | @yield('title')@endif</title>
    <meta name="description" content="Smoke without Fire">

    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, viewport-fit=cover">
    <meta name="format-detection" content="telephone=no">
    <base href="{{ route('base') }}" />

    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-title" content="{{ config('app.name') }}">
    <link rel="icon" type="{{ app('App\Http\Controllers\AssetController')->favicon_type() }}" href="{{ route('favicon') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,300;0,400;0,700;1,300;1,400;1,700&display=swap" rel="stylesheet">