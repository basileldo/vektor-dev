@extends('layouts.default')
@section('title', 'Dashboard')

@section('content')
    <div>
        <div class="container:xl:3 dashboard__navigation">
            <nav><ul>
                @include('partials.dashboard_navigation')
            </ul></nav>
        </div>
        <section class="py-4 py-8:2 py-12:3">
            <div class="container:xl">
                @yield('content.dashboard')
            </div>
        </section>
    </div>
@endsection