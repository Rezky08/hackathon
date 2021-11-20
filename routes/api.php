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

Route::group(['prefix'=>'user','middleware'=>'auth:sanctum'],function (){
   Route::get("/",[\App\Http\Controllers\AuthController::class,'index']);
   Route::put("/",[\App\Http\Controllers\AuthController::class,'update']);
   Route::delete("/",[\App\Http\Controllers\AuthController::class,'destroy']);
});

Route::group(['prefix'=>'auth'],function (){
    Route::post('/login',[\App\Http\Controllers\AuthController::class,'login']);
    Route::post("/register",[\App\Http\Controllers\AuthController::class,'store']);
});

Route::post('/tokens/create', function (Request $request) {
    $token = $request->user()->createToken($request->token_name);

    return ['token' => $token->plainTextToken];
});
