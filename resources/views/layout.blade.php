<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <title>Lottery Generator - @yield('title')</title>
</head>
<body>
<div class="jumbotron" style="background-color: #4599ff">
    <h1 class="display-4">Lottery Generator</h1>
    <p><i>Just for fun, makes an attempt at 'guessing' the Lotto numbers using a half-arsed bit of logic.</i></p>
</div>

<div class="container">
    @yield('content')
</div>
</body>
</html>
