<?php

use App\Http\Controllers\Open\EformController;
use Illuminate\Support\Facades\Route;


Route::middleware('client.api')->prefix('api/v1.0')->group(function () {
    Route::prefix('eform')->group(function () {
        Route::get('/',[EformController::class,'index']);
        Route::post('/',[EformController::class,'store']);
        Route::post('/check-nasabah',[EformController::class,'check']);
        Route::get('/check-data-nasabah',[EformController::class,'store']);
        Route::post('/detail',[EformController::class,'show']);
        Route::post('/tracking-status',[EformController::class,'tracking']);
    });;
});
