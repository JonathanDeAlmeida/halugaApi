<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
/*
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
});
*/
Route::post('user-create', 'App\Http\Controllers\UserController@create');
Route::get('responsible-create', 'App\Http\Controllers\ResponsibleController@create');
Route::get('message-create', 'App\Http\Controllers\MessageController@create');
Route::post('place-create', 'App\Http\Controllers\PlaceController@create');

Route::post('time-create', 'App\Http\Controllers\TimeController@create');
Route::post('get-times', 'App\Http\Controllers\TimeController@getTimes');
Route::post('time-excluded', 'App\Http\Controllers\TimeController@excluded');
Route::post('time-edit', 'App\Http\Controllers\TimeController@edit');

Route::get('phone-create', 'App\Http\Controllers\PhoneController@create');
Route::get('adresse-create', 'App\Http\Controllers\AddressController@create');




