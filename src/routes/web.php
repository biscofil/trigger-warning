<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'HomeController@index')->name('homepage');

Route::middleware('guest')->group(function () {

    Route::get('/redirect', 'Auth\LoginController@redirectToProvider')->name('login');
    Route::get('/callback', 'Auth\LoginController@handleProviderCallback');

});

Route::middleware('auth')->group(function () {

    Route::get('/logout', 'Auth\LoginController@logout')->name('logout');

    Route::prefix('games')->group(function () {

        Route::prefix('trigger_warning')->namespace('TriggerWarning')->group(function () {
            Route::get('/', 'TriggerWarningController@index')->name('play.trigger_warning');
        });

        Route::prefix('one_word_each')->namespace('OneWordEach')->group(function () {
            Route::get('/', 'OneWordEachController@index')->name('play.one_word_each');
        });

    });

});

