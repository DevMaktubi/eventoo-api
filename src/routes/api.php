<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
// AUTH
Route::post("/login", 'App\Http\Controllers\AuthController@login');
Route::post("/register", 'App\Http\Controllers\AuthController@register');
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::middleware('auth:sanctum')->post('/logout', 'App\Http\Controllers\AuthController@logout');

// NOTIFICATIONS
Route::middleware('auth:sanctum')->get('/notifications', 'App\Http\Controllers\NotificationController@index');
Route::middleware('auth:sanctum')->get('/notifications/mark-all-as-read', 'App\Http\Controllers\NotificationController@markAllAsRead');
// EVENTS
Route::get('/events', 'App\Http\Controllers\EventController@index');
Route::get('/events/{id}', 'App\Http\Controllers\EventController@show');
Route::middleware('auth:sanctum')->post('/events', 'App\Http\Controllers\EventController@store');
Route::middleware('auth:sanctum')->put('/events/{id}', 'App\Http\Controllers\EventController@update');
Route::middleware('auth:sanctum')->delete('/events/{id}', 'App\Http\Controllers\EventController@destroy');

// RESERVATIONS
Route::middleware('auth:sanctum')->get('/reservations', 'App\Http\Controllers\ReservationController@index');
Route::middleware('auth:sanctum')->get('/reservations/{id}', 'App\Http\Controllers\ReservationController@show');
Route::middleware('auth:sanctum')->post('/reservations', 'App\Http\Controllers\ReservationController@store');
Route::middleware('auth:sanctum')->put('/reservations/{id}', 'App\Http\Controllers\ReservationController@update');
Route::middleware('auth:sanctum')->delete('/reservations/{id}', 'App\Http\Controllers\ReservationController@destroy');
