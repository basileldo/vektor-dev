@extends('layouts.default')
@section('title', 'Email has been sent!')

@section('content')
    <div class="py-4 py-8:2 py-12:3">
        <div class="container:sm">
            <div class="bg-background shadow-box p-8 p-10:3">
                <h1 class="text-gradient">Email has been sent!</h1>
                <p>We have sent password recovery instructions to your email.</p>

                <p class="text-sm">Did not receive the email? Check your spam filter, <a class="text-secondary" href="{{ route('password.request') }}">try again</a>, or <a class="text-primary" href="{{ route('contact') }}">contact us</a>.</p>
            </div>
        </div>
    </div>
@endsection