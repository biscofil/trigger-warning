@extends('partials.layout')

@section('content')

    <div id="app">

        <trigger-warning></trigger-warning>

        @if(is_null(getAuthUser()->telegram_auth_code))
            <a class="btn btn-outline-warning" href="{{ \App\TriggerWarningTelegramBot::get_login_url(getAuthUser()) }}">
                Accedi con Telegram
            </a>
        @endif

    </div>

@endsection
