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

    Route::get('/users', 'HomeController@xhr_play');
    Route::get('/heartbeat', 'HomeController@heartBeat');

    Route::prefix('games')->group(function () {

        /* TRIGGER WARNING */
        Route::prefix('trigger_warning')->namespace('TriggerWarning')->group(function () {

            Route::get('/game', 'TriggerWarningController@xhr_play');

            Route::post('/cards', 'CardController@store');

            Route::post('/rounds', 'RoundController@store');
            Route::get('/rounds/{round}', 'RoundController@show');
            Route::put('/rounds/{round}/cards/{card}/picked', 'CardController@setPicked');
            Route::post('/rounds/{round}/close/{winner}', 'RoundController@close_round');

        });

        /* ONE WORD EACH */
        Route::prefix('one_word_each')->namespace('OneWordEach')->group(function () {

            Route::get('/game', 'OneWordEachController@xhr_play');

            Route::post('/words', 'WordController@store');

            Route::post('/rounds', 'RoundController@store');
            Route::get('/rounds/{round}', 'RoundController@show');
            Route::post('/rounds/{round}/close', 'RoundController@close_round');

        });

    });

});
