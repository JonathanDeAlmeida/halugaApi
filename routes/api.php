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
Route::get('time-create', 'App\Http\Controllers\TimeController@create');
Route::get('phone-create', 'App\Http\Controllers\PhoneController@create');
Route::get('adresse-create', 'App\Http\Controllers\AddressController@create');




