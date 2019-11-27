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

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::prefix('competitions')->group(function ()
{
    Route::get('/', 'CompetitionController@list');
    Route::get('/{id}', 'CompetitionController@findById');
});

Route::prefix('team')->group(function ()
{
    Route::get('/', 'TeamController@list');
    Route::get('/{id}', 'TeamController@listById');
});

Route::prefix('players')->group(function ()
{
    Route::get('/', 'PlayerController@list');
});
