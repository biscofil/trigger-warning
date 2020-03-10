<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Trigger Warning</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

    <link href="{{asset('css/app.css')}}" rel="stylesheet">


</head>
<body>
<div>
    @if (Route::has('login'))
        <div class="top-right links">
            @auth
                <a href="{{ route('play') }}">Gioca</a>
            @else
                <a href="{{ route('login') }}">Login</a>
            @endauth
        </div>
    @endif

    <div class="content">

        @yield('content')

    </div>

</div>

@yield('scripts')

</body>
</html>
