@extends('partials.layout')

@section('content')

    <div align="center">

        <div class="title m-b-md">
            Trigger Warning
        </div>

        <img src="{{asset('img/trigger_lady.jpg')}}">

        <div>
            @auth
                <a class="btn btn-success" href="{{ route('play') }}">Gioca</a>
                <a class="btn btn-danger" href="{{ route('logout') }}">Logout</a>
            @else
                <a class="btn btn-info" href="{{ route('login') }}">Login</a>
            @endauth
        </div>

    </div>


@endsection
