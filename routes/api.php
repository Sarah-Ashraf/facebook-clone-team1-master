<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::apiResource('posts', 'postcontroller');

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth',
], function ($router) {
    Route::post('signup', 'AuthController@signup');
    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');
    Route::post('sendresetpasswordemail', 'AuthController@sendresetpasswordemail');
    Route::post('confirm_pin', 'AuthController@confirm_pin');
    Route::post('resetpassword', 'AuthController@resetpassword');
    Route::post('changepassword', 'AuthController@changepassword');
    Route::post('edit_profile', 'AuthController@edit_profile');
});
