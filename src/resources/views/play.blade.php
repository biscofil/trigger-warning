@extends('partials.layout')

@section('scripts')

    <script src="{{asset('js/app.js')}}"></script>

@endsection

@section('content')

    <div id="app">

        <play-component></play-component>

    </div>

@endsection
