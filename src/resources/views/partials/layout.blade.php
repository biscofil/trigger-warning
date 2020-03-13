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

<div class="container-fluid">

    @if ($errors->any())
        @foreach ($errors->all() as $error)
            <div class="alert alert-danger">
                <b>{{ $error }}</b>
            </div>
        @endforeach
    @endif

    @yield('content')

</div>

@yield('scripts')

</body>
</html>
