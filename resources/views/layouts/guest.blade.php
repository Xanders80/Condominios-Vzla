<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="shortcut icon" href="{{ asset(config('master.app.web.template') . '/images/favicon.ico') }}" />

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset(config('master.app.web.template') . '/css/libs.min.css') }}">
    <link rel="stylesheet" href="{{ asset(config('master.app.web.template') . '/css/hope-ui.css?v=1.0') }}">
    <link rel="stylesheet"
        href="{{ asset(config('master.app.web.template') . '/assets/vendor_components/bootstrap/dist/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset(config('master.app.web.template') . '/css/custom_admin.css') }}">

</head>

<body class="antialiased hold-transition" data-bs-spy="scroll" data-bs-target="#elements-section" data-bs-offset="0">
    <div id="loader" class="loader simple-loader">
        <div class="loader"></div>
    </div>
    <div class="wrapper">
        {{ $slot }}
    </div>
    <script src="{{ asset(config('master.app.web.template') . '/js/libs.min.js') }}"></script>
    <script src="{{ url('/js/' . config('master.app.web.backend') . '/js/auth.js') }}"></script>
    <script src="{{ asset(config('master.app.web.template') . '/js/hope-ui.js') }}"></script>
</body>

</html>
