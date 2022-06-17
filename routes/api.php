<?php

use App\Http\Controllers\Api\AuthControler;
use App\Http\Controllers\Api\Bri\BriController;
use App\Http\Controllers\Api\Mandiri\MandiriController;
use App\Http\Controllers\Api\RSAController;
use App\Http\Controllers\Api\UserControler;
use App\Http\Controllers\Master\AgamaController;
use App\Http\Controllers\Master\CabangController;
use App\Http\Controllers\Master\JenisInstansiController;
use App\Http\Controllers\Master\JenisKelaminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Spatie\Health\Http\Controllers\HealthCheckJsonResultsController;


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


Route::get('health', HealthCheckJsonResultsController::class);

//with middleware
Route::prefix('v1')
->namespace('Api')
->middleware('write.log')
->group(function () {

    Route::post('/login',[AuthControler::class,'login']);
    Route::get('/refresh-token',[AuthControler::class,'refreshToken']);

    Route::group(['middleware' => 'access'],function () {
        Route::prefix('user')->group(function () {
            Route::get('/',[UserControler::class,'getAll']);
            Route::post('/',[UserControler::class,'save']);
            Route::put('/{id}',[UserControler::class,'update']);
            Route::delete('/{id}',[UserControler::class,'delete']);
            Route::get('/detail',[UserControler::class,'detail']);
        });

        Route::prefix('master')->group(function () {
            // route agama
            Route::prefix('agama')->group(function () {
                Route::get('/',[AgamaController::class,'index']);
                Route::post('/',[AgamaController::class,'store']);
                Route::put('/{id}',[AgamaController::class,'update']);
                Route::delete('/{id}',[AgamaController::class,'destroy']);
            });
            // route cabang
            Route::prefix('cabang')->group(function () {
                Route::get('/',[CabangController::class,'index']);
                Route::get('/{id}',[CabangController::class,'show']);
                Route::post('/',[CabangController::class,'store']);
                Route::put('/{id}',[CabangController::class,'update']);
                Route::delete('/{id}',[CabangController::class,'destroy']);
            });
            // route jenis instansi
            Route::prefix('jenis-instansi')->group(function () {
                Route::get('/',[JenisInstansiController::class,'index']);
                Route::get('/{id}',[JenisInstansiController::class,'show']);
                Route::post('/',[JenisInstansiController::class,'store']);
                Route::put('/{id}',[JenisInstansiController::class,'update']);
                Route::delete('/{id}',[JenisInstansiController::class,'destroy']);
            });
            // route jenis kelamin
            Route::prefix('jenis-kelamin')->group(function () {
                Route::get('/',[JenisKelaminController::class,'index']);
                Route::get('/{id}',[JenisKelaminController::class,'show']);
                Route::post('/',[JenisKelaminController::class,'store']);
                Route::put('/{id}',[JenisKelaminController::class,'update']);
                Route::delete('/{id}',[JenisKelaminController::class,'destroy']);
            });
        });

        Route::get('/test',function (Request $request){
            return "service up";
        });
    });


});

