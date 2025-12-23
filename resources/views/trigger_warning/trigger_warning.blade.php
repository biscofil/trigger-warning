@extends('partials.layout')

@section('content')

    <div id="app">

        <trigger-warning></trigger-warning>

        <a class="btn btn-outline-warning" href="{{ \App\TriggerWarningTelegramBot::get_login_url(getAuthUser()) }}">
            Accedi con Telegram
        </a>

    </div>

@endsection
