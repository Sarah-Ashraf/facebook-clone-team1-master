<?php

use Illuminate\Support\Facades\Route;
use App\Mail\MailtrapExample;
use Illuminate\Support\Facades\Mail;

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

Route::apiResource("posts","postcontroller");

Route::get('/', function () {
    return view('welcome');
});


Route::get('verify-email/{code}','AuthController@verify')->name('verify');
