@extends('layouts.default')
@section('title', 'Order success')

@section('content')
    <div class="py-4 py-8:2 py-20:3 h-screen fixed w-full top-0 ">
        <div class="flex flex-col justify-center h-screen fixed w-full top-0 text-center">
            <svg class="mb-6 mx-auto text-primary fill-current" xmlns="http://www.w3.org/2000/svg" width="60px" height="60px" viewBox="0 0 60 60"><polygon points="51.483,5.936 20.39,37.029 8.517,25.157 0,33.674 20.39,54.064 60,14.453 "/></svg>
            <h1>Thank you for your order</h1>
            <p class="max-w-md mx-auto">We are currently processing your order. You will receive a confirmation email shortly.</p>
            <p>
                <a class="btn bg-primary border-primary text-primary_contrasting" href="{{ route('base') }}">Return to Homepage</a>
            </p>
            <?php //echo '<pre>'; var_dump($_REQUEST); echo '</pre>'; ?>
        </div>
    </div>
    <style>
        .document__footer {
            display: none;
        }
    </style>
@endsection