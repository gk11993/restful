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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource("play", App\Http\Controllers\PlayController::class);

Route::post("user/checkBind", [App\Http\Controllers\UserController::class, 'checkBind']);
Route::post("user/addMoney", [App\Http\Controllers\UserController::class, 'addMoney']);
Route::post("user/queryMoney", [App\Http\Controllers\UserController::class, 'queryMoney']);
Route::post("user/freePlay", [App\Http\Controllers\UserController::class, 'freePlay']);
Route::post("user/play", [App\Http\Controllers\UserController::class, 'play']);

Route::apiResource("user", App\Http\Controllers\UserController::class);

