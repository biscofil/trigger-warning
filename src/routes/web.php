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

    Route::get('/home', 'GameController@play')->name('play');

});

