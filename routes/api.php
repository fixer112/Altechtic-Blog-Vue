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

Route::middleware('auths')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => 'guests'], function () {
Route::post('/register', 'API\AuthController@register');
Route::post('/login', 'API\AuthController@login');
});

Route::group(['middleware' => 'jwt.auth'], function () {
Route::get('setting', 'API\SettingController@index')->middleware('role:super-admin');
Route::put('/setting/change', 'API\SettingController@change')->middleware('role:super-admin');
Route::resource('user', 'API\UserController');
Route::resource('post', 'API\PostController');
});