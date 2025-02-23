<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{!! config('master.app.profile.description') !!}">
    <meta name="author" content="{!! config('master.app.profile.author') !!}">
    <title>@stack('title', config('master.app.profile.name')) | {!! config('master.app.profile.short_name') !!}</title>
    <link rel="icon" href="{{ asset(config('master.app.web.template') . '/images/favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset(config('master.app.web.template') . '/css/vendors_css.css') }}">
    <link rel="stylesheet" href="{{ asset(config('master.app.web.template') . '/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset(config('master.app.web.template') . '/css/skin_color.css') }}">
</head>

<body class="hold-transition theme-primary bg-img"
    style="background-image: asset({{ asset(config('master.app.web.template') . '/images/auth-bg/bg-1.jpg') }})">

    <section class="error-page h-p100">
        <div class="container h-p100">
            <div class="row h-p100 align-items-center justify-content-center text-center">
                <div class="col-lg-7 col-md-10 col-12">
                    <div class="rounded10 p-50">
                        @yield('logo')
                        <h1>@yield('title')</h1>
                        <h3>@yield('message')</h3>
                        <div class="my-30">
                            <a href="{{ route('dashboard') }}" class="btn btn-danger">{{ __('Back to dashboard') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="{{ asset(config('master.app.web.template') . '/js/vendors.min.js') }}"></script>
</body>

</html>
