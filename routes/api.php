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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('V1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('login', 'Api\V1\Auth\AuthApiController@login');
        Route::group(['middleware' => 'jwt.auth'], function(){
            Route::get('user', 'Api\V1\Auth\AuthApiController@user');
            Route::post('logout', 'Api\V1\Auth\AuthApiController@logout');
        });
        Route::group(['middleware' => 'jwt.refresh'], function(){
            Route::get('refresh', 'Api\V1\Auth\AuthApiController@refresh');
        });
    });
    Route::group(['middleware' => ['sessions']], function () {
        Route::post('bet', 'Api\V1\Bet\BetApiController@store');
    });

    Route::get('bets/{id}', 'Api\V1\Bet\BetApiController@show');
    Route::get('bets', 'Api\V1\Bet\BetApiController@index');

    Route::get('statistic/months', 'Api\V1\Statistic\StatisticApiController@byMonth');

    Route::get('users', 'Api\V1\User\UserApiController@index');
});
