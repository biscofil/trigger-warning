<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth')->group(function () {

    Route::get('/game', 'GameController@xhr_play');

    Route::post('/rounds', 'RoundController@store');

    Route::get('/rounds/{round}', 'RoundController@show');

    Route::put('/rounds/{round}/cards/{card}/picked', 'CardController@setPicked');

    Route::post('/rounds/{round}/close/{winner}', 'RoundController@close_round');

    //Route::get('deck', 'Auth\AuthController@me');

});
