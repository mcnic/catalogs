<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title')</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <!-- script src="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.js"></script -->

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <!-- link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900" rel="stylesheet">
    <!-- link href="https://cdn.jsdelivr.net/npm/@mdi/font@4.x/css/materialdesignicons.min.css" rel="stylesheet" -->

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                @include('layouts.topmenu')
            </div>
        </nav>
        <main class="py-4">
            <div class="container" >
                @yield('content')
            </div>  
        </main>
    </div>

    <div id="footer" class="footer">
        <div class="container">
            @include('layouts.footer')
        </div>
    </div>

    <div id="lift" class="lift" title="Наверх">
        <span class="glyphicon glyphicon-arrow-up"></span>
    </div>  

    <script src="/js/app.js"></script>      
</body>
</html>
