<!-- Favicon -->
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="{!! config('master.app.profile.description') !!}">
<meta name="author" content="{!! config('master.app.profile.author') !!}">
<title>@stack('title', config('master.app.profile.name')) | {!! config('master.app.profile.short_name') !!}</title>
<link rel="shortcut icon" href="{{ asset($template . '/images/favicon.ico') }}" />
<link rel="stylesheet" href="{{ asset($template . '/css/vendors_css.css') }}">
<link rel="stylesheet" href="{{ asset($template . '/css/style.css') }}">
<link rel="stylesheet" href="{{ asset($template . '/css/skin_color.css') }}">

<style>
    .modal-content {
        border-radius: 10px;
        /* Ajusta el valor según tus necesidades */
    }

    th.hide-search input {
        display: none;
    }

    .table {
        max-height: 500px;
        height: auto;
    }
</style>

@stack('css')
