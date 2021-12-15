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

Route::middleware(['auth:sanctum'])->group(function (){

    Route::group(['prefix'=>'user'],function (){
        Route::get("/",[\App\Http\Controllers\AuthController::class,'index']);
        Route::put("/",[\App\Http\Controllers\AuthController::class,'update']);
        Route::delete("/",[\App\Http\Controllers\AuthController::class,'destroy']);
    });

    Route::group(['prefix'=>'sayembara'],function (){

        Route::group(['prefix'=>'category'],function (){
            Route::get('/',[\App\Http\Controllers\Sayembara\CategoryController::class,'index']);
        });

        Route::group(['prefix'=>'present'],function (){
            Route::get('type',[\App\Http\Controllers\Sayembara\PresentController::class,'getPresentType']);
        });
        Route::get('/{sayembara_id?}',[\App\Http\Controllers\Sayembara\SayembaraController::class,'index']);

        /** sayembara participant */
        Route::group(['prefix'=>'{sayembara_id}'],function () {
            Route::group(['prefix'=>'join'],function (){
                Route::post("/",[\App\Http\Controllers\Sayembara\ParticipantController::class,'joinSayembara']);
            });

            Route::middleware('validate.participant')->group(function (){
                Route::group(['prefix'=>'out'],function (){
                    Route::post("/",[\App\Http\Controllers\Sayembara\ParticipantController::class,'outSayembara']);
                });

                Route::group(['prefix'=>'present'],function (){
                    Route::post("/receive",[\App\Http\Controllers\Sayembara\ParticipantController::class,'receiveSayembaraPresent']);
                });
            });

        });
        /** end sayembara participant */

        /** sayembara owner */
        Route::post('/',[\App\Http\Controllers\Sayembara\SayembaraController::class,'createNewSayembara']);

        Route::group(['prefix'=>'{sayembara_id}','middleware'=>['validate.owner']],function (){
            Route::put('/',[\App\Http\Controllers\Sayembara\SayembaraController::class,'updateExistingSayembara']);
            Route::delete('/',[\App\Http\Controllers\Sayembara\SayembaraController::class,'deleteExistingSayembara']);

            Route::group(['prefix'=>'verify'],function (){
                Route::post("/{participant_id}",[\App\Http\Controllers\Sayembara\SayembaraController::class,'verifyParticipantTask']);
            });

            Route::group(['prefix'=>'win'],function (){
                Route::post("/{participant_id}",[\App\Http\Controllers\Sayembara\SayembaraController::class,'setParticipantAsWinner']);
            });

            Route::group(['prefix'=>'status'],function (){
                Route::put('/',[\App\Http\Controllers\Sayembara\SayembaraController::class,'setExistingSayembaraStatus']);
            });

        });
        /** end sayembara owner */

    });

    Route::group(['prefix'=>'geo'],function (){
        Route::get('/province',[\App\Http\Controllers\Geo\ProvinceController::class,'index']);
        Route::get('/city',[\App\Http\Controllers\Geo\CityController::class,'index']);
        Route::get('/district',[\App\Http\Controllers\Geo\DistrictController::class,'index']);
        Route::get('/subdistrict',[\App\Http\Controllers\Geo\SubDistrictController::class,'index']);
    });

    Route::group(['prefix'=>'attachment'],function (){
       Route::post('/',[\App\Http\Controllers\AttachmentController::class,'store']);
    });

});




Route::group(['prefix'=>'auth'],function (){
    Route::post('/login',[\App\Http\Controllers\AuthController::class,'login']);
    Route::post("/register",[\App\Http\Controllers\AuthController::class,'store']);
});

Route::post('/tokens/create', function (Request $request) {
    $token = $request->user()->createToken($request->token_name);

    return ['token' => $token->plainTextToken];
});
