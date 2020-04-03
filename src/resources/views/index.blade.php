@extends('partials.layout')

@section('content')

    <div align="center">

        <div class="title m-b-md">
            Games
        </div>

        <div>

            @auth
                <a class="btn btn-danger" href="{{ route('logout') }}">Logout</a>
            @else
                <a class="btn btn-info" href="{{ route('login') }}">Login</a>
            @endauth

        </div>

        <div id="app">
            <Home></Home>
        </div>

        @auth

            <a class="btn btn-success" href="{{ route('play.trigger_warning') }}">Gioca a Trigger Warning</a>

            <a class="btn btn-success" href="{{ route('play.one_word_each') }}">Gioca a One Word Each</a>

        @endauth

    </div>

    </div>


@endsection
