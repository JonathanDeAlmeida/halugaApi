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
Route::post('user-edit', 'App\Http\Controllers\UserController@edit');
Route::post('get-user', 'App\Http\Controllers\UserController@get');
Route::get('get-all', 'App\Http\Controllers\UserController@getAll');
Route::post('login', 'App\Http\Controllers\UserController@login');
Route::post('delete-user', 'App\Http\Controllers\UserController@deleteUser');

Route::post('upload-file', 'App\Http\Controllers\PlaceController@postUploadFile');
Route::post('remove-file', 'App\Http\Controllers\PlaceController@removeFile');

Route::post('get-place-images', 'App\Http\Controllers\PlaceController@getPlaceImages');

Route::post('delete-place', 'App\Http\Controllers\PlaceController@deletePlace');
Route::post('get-places', 'App\Http\Controllers\PlaceController@getPlaces');
Route::post('recover-password', 'App\Http\Controllers\UserController@recoverPassword');

Route::post('get-place', 'App\Http\Controllers\PlaceController@getPlace');
Route::post('get-filter-place', 'App\Http\Controllers\PlaceController@getFilterPlace');
Route::post('get-place-times', 'App\Http\Controllers\PlaceController@getPlaceTimes');

Route::get('responsible-create', 'App\Http\Controllers\ResponsibleController@create');
Route::get('message-create', 'App\Http\Controllers\MessageController@create');
Route::post('place-create', 'App\Http\Controllers\PlaceController@create');
Route::post('place-edit', 'App\Http\Controllers\PlaceController@edit');

Route::post('time-create', 'App\Http\Controllers\TimeController@create');
Route::post('get-times', 'App\Http\Controllers\TimeController@getTimes');
Route::post('time-excluded', 'App\Http\Controllers\TimeController@excluded');
Route::post('time-edit', 'App\Http\Controllers\TimeController@edit');

Route::post('time-edit', 'App\Http\Controllers\TimeController@edit');
Route::get('phone-create', 'App\Http\Controllers\PhoneController@create');
Route::get('adresse-create', 'App\Http\Controllers\AddressController@create');




