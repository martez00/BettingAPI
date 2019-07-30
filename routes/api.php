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
    Route::group(['middleware' => ['sessions']], function () {
        Route::post('bet', 'Api\V1\Bet\BetApiController@store');
    });
    Route::get('bet/{id}', 'Api\V1\Bet\BetApiController@show');
    Route::get('bets', 'Api\V1\Bet\BetApiController@index');
    Route::get('statistic/months', 'Api\V1\Statistic\StatisticApiController@byMonth');
});
