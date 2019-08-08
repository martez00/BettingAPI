<?php

use Illuminate\Http\Request;

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
Route::prefix('V1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('login', 'Api\V1\Auth\AuthApiController@login');
        Route::post('register', 'Api\V1\Auth\AuthApiController@register');
        Route::group(['middleware' => 'jwt.auth'], function () {
            Route::get('user', 'Api\V1\Auth\AuthApiController@user');
            Route::post('logout', 'Api\V1\Auth\AuthApiController@logout');
        });
    });

    Route::prefix('admin')->group(function () {
        Route::group(['middleware' => ['jwt.auth', 'isAdmin']], function () {
            Route::post('sports', 'Api\V1\Sport\SportApiController@store');
            Route::put('sports/{id}', 'Api\V1\Sport\SportApiController@update');
            Route::delete('sports/{id}', 'Api\V1\Sport\SportApiController@destroy');
        });
    });

    Route::get('sports/{id}', 'Api\V1\Sport\SportApiController@show');

    Route::get('statistic/months', 'Api\V1\Statistic\StatisticApiController@byMonth');

    Route::get('bets/{id}', 'Api\V1\Bet\BetApiController@show');
    Route::get('bets', 'Api\V1\Bet\BetApiController@index');


    Route::get('users', 'Api\V1\User\UserApiController@index');
});
