<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="format-detection" content="telephone=no">
    <meta name="color-scheme" content="light dark">

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <title>Lottery Generator - @yield('title')</title>
</head>
<body>
<div class="jumbotron">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div class="jumbotron-content">
                <h1 class="display-4">
                    <a href="/" class="text-white text-decoration-none">Lottery Generator</a>
                </h1>
                <p class="mb-0"><em>Just for fun, makes an attempt at 'guessing' the Lotto numbers using a half-arsed bit of logic.</em></p>
            </div>
            @yield('navigation')
        </div>
    </div>
</div>

<div class="container">
    @yield('content')
</div>
</body>
</html>
