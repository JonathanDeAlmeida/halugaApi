<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
/*
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
});
*/
Route::post('login', 'App\Http\Controllers\UserController@login');
Route::post('user-create', 'App\Http\Controllers\UserController@create');
Route::post('recover-password', 'App\Http\Controllers\UserController@recoverPassword');
Route::post('get-place', 'App\Http\Controllers\PlaceController@getPlace');
Route::get('get-filter-place', 'App\Http\Controllers\PlaceController@getFilterPlace');

Route::middleware('auth:sanctum')->post('/get-user', 'App\Http\Controllers\UserController@get');
Route::middleware('auth:sanctum')->get('/get-places', 'App\Http\Controllers\PlaceController@getPlaces');
Route::middleware('auth:sanctum')->post('/delete-place', 'App\Http\Controllers\PlaceController@deletePlace');
Route::middleware('auth:sanctum')->post('/get-place-images', 'App\Http\Controllers\PlaceController@getPlaceImages');
Route::middleware('auth:sanctum')->post('/place-create', 'App\Http\Controllers\PlaceController@create');
Route::middleware('auth:sanctum')->post('/place-edit', 'App\Http\Controllers\PlaceController@edit');
Route::middleware('auth:sanctum')->post('/remove-file', 'App\Http\Controllers\PlaceController@removeFile');
Route::middleware('auth:sanctum')->post('/upload-file', 'App\Http\Controllers\PlaceController@postUploadFile');
Route::middleware('auth:sanctum')->post('/user-edit', 'App\Http\Controllers\UserController@edit');
Route::middleware('auth:sanctum')->post('/delete-user', 'App\Http\Controllers\UserController@deleteUser');




Route::post('get-place-times', 'App\Http\Controllers\PlaceController@getPlaceTimes');
Route::get('responsible-create', 'App\Http\Controllers\ResponsibleController@create');
Route::get('message-create', 'App\Http\Controllers\MessageController@create');
Route::post('time-create', 'App\Http\Controllers\TimeController@create');
Route::post('get-times', 'App\Http\Controllers\TimeController@getTimes');
Route::post('time-excluded', 'App\Http\Controllers\TimeController@excluded');
Route::post('time-edit', 'App\Http\Controllers\TimeController@edit');
Route::post('time-edit', 'App\Http\Controllers\TimeController@edit');
Route::get('phone-create', 'App\Http\Controllers\PhoneController@create');
Route::get('adresse-create', 'App\Http\Controllers\AddressController@create');




