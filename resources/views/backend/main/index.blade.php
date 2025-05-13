<!DOCTYPE html>
<html lang="en">

<head>
    @include('partials._head')
</head>

<body class="hold-transition light-skin sidebar-mini theme-primary fixed">
    <div class="wrapper">
        <div id="loader"></div>
        @include('backend.main.menu.header')
        @include('backend.main.menu.sidebar')
        @yield('content')
        @include('backend.main.menu.footer')
        @include('backend.main.menu.control-sidebar')
    </div>
    @include('partials._scripts')
</body>

</html>
