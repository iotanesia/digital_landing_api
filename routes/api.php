<?php

use App\Http\Controllers\Api\AuthControler;
use App\Http\Controllers\Api\CanvasingController;
use App\Http\Controllers\Api\CanvassingController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\EformController;
use App\Http\Controllers\Api\EmailController;
use App\Http\Controllers\Api\FileController;
use App\Http\Controllers\Api\UserControler;
use App\Http\Controllers\Api\SimulasiController;
use App\Http\Controllers\JenisProdukController;
use App\Http\Controllers\Master\AgamaController;
use App\Http\Controllers\Master\BannerController;
use App\Http\Controllers\Master\KabuatenController;
use App\Http\Controllers\Master\KecamatanController;
use App\Http\Controllers\Master\KelurahanController;
use App\Http\Controllers\Master\ProdukController;
use App\Http\Controllers\Master\PropinsiController;
use App\Http\Controllers\Master\SubProdukController;
use App\Http\Controllers\Master\CabangController;
use App\Http\Controllers\Master\JenisInstansiController;
use App\Http\Controllers\Master\JenisKelaminController;
use App\Http\Controllers\Master\JenisPekerjaanController;
use App\Http\Controllers\Master\TingkatPendidikanController;
use App\Http\Controllers\Master\StatusPernikahanController;
use App\Http\Controllers\Master\TujuanPemasaranController;
use App\Http\Controllers\Master\CaraPemasaranController;
use App\Http\Controllers\Master\StatusTempatTinggalController;
use App\Http\Controllers\Master\HubunganController;
use App\Http\Controllers\Master\PromoController;
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
    Route::get('/mail',[EmailController::class,'index']);
    Route::prefix('file')->group(function () {
        Route::get('/{filename}',[FileController::class,'show']);
    });

    Route::group(['middleware' => 'access'],function () {
        // reoute canvasing
        Route::prefix('canvasing')->group(function () {
            Route::prefix('web')->group(function () {
                Route::post('/',[CanvassingController::class,'storeWeb']);
            });

            Route::prefix('mobile')->group(function () {
                Route::post('/',[CanvassingController::class,'storeMobile']);
            });

            Route::prefix('main')->group(function () {
                Route::get('/',[CanvassingController::class,'index']);
                Route::get('/data-pusat',[CanvassingController::class,'dataPusat']);
                Route::get('/data-web',[CanvassingController::class,'dataWeb']);
                Route::get('/{id}',[CanvassingController::class,'show']);
                Route::post('/assign',[CanvassingController::class,'assign']);
                Route::get('/riwayat-aktifitas/{id}',[CanvassingController::class,'riwayatAktifitas']);
            });
        });
        // users
        Route::prefix('user')->group(function () {
            Route::get('/',[UserControler::class,'getAll']);
            Route::post('/',[UserControler::class,'save']);
            Route::put('/{id}',[UserControler::class,'update']);
            Route::delete('/{id}',[UserControler::class,'delete']);
            Route::get('/detail',[UserControler::class,'detail']);
        });
        // master
        Route::prefix('master')->group(function () {
             // route agama
             Route::prefix('agama')->group(function () {
                Route::get('/',[AgamaController::class,'index']);
                Route::post('/',[AgamaController::class,'store']);
                Route::put('/{id}',[AgamaController::class,'update']);
                Route::delete('/{id}',[AgamaController::class,'destroy']);
            });
            // route propinsi
            Route::prefix('propinsi')->group(function () {
                Route::get('/',[PropinsiController::class,'index']);
                Route::post('/',[PropinsiController::class,'store']);
                Route::get('/{id}',[PropinsiController::class,'show']);
                Route::put('/{id}',[PropinsiController::class,'update']);
                Route::delete('/{id}',[PropinsiController::class,'destroy']);
            });
            // route kabupaten
            Route::prefix('kabupaten')->group(function () {
                Route::get('/',[KabuatenController::class,'index']);
                Route::post('/',[KabuatenController::class,'store']);
                Route::get('/{id}',[KabuatenController::class,'show']);
                Route::put('/{id}',[KabuatenController::class,'update']);
                Route::delete('/{id}',[KabuatenController::class,'destroy']);
            });
            // route kecamatan
            Route::prefix('kecamatan')->group(function () {
                Route::get('/',[KecamatanController::class,'index']);
                Route::post('/',[KecamatanController::class,'store']);
                Route::get('/{id}',[KecamatanController::class,'show']);
                Route::put('/{id}',[KecamatanController::class,'update']);
                Route::delete('/{id}',[KecamatanController::class,'destroy']);
            });
            // route kelurahan
            Route::prefix('kelurahan')->group(function () {
                Route::get('/',[KelurahanController::class,'index']);
                Route::post('/',[KelurahanController::class,'store']);
                Route::get('/{id}',[KelurahanController::class,'show']);
                Route::put('/{id}',[KelurahanController::class,'update']);
                Route::delete('/{id}',[KelurahanController::class,'destroy']);
            });
             // route jenis roduk
             Route::prefix('jenis-produk')->group(function () {
                Route::get('/',[JenisProdukController::class,'index']);
                Route::post('/',[JenisProdukController::class,'store']);
                Route::get('/{id}',[JenisProdukController::class,'show']);
                Route::put('/{id}',[JenisProdukController::class,'update']);
                Route::delete('/{id}',[JenisProdukController::class,'destroy']);
            });
            // route produk
            Route::prefix('produk')->group(function () {
                Route::get('/',[ProdukController::class,'index']);
                Route::post('/',[ProdukController::class,'store']);
                Route::get('/{id}',[ProdukController::class,'show']);
                Route::put('/{id}',[ProdukController::class,'update']);
                Route::delete('/{id}',[ProdukController::class,'destroy']);
            });
            // route banner
            Route::prefix('banner')->group(function () {
                Route::get('/',[BannerController::class,'index']);
                Route::post('/',[BannerController::class,'store']);
                Route::get('/{id}',[BannerController::class,'show']);
                Route::put('/{id}',[BannerController::class,'update']);
                Route::delete('/{id}',[BannerController::class,'destroy']);
            });
            // route promo
            Route::prefix('promo')->group(function () {
                Route::get('/',[PromoController::class,'index']);
                Route::post('/',[PromoController::class,'store']);
                Route::get('/{id}',[PromoController::class,'show']);
                Route::put('/{id}',[PromoController::class,'update']);
                Route::delete('/{id}',[PromoController::class,'destroy']);
            });
            // route sub produk
            Route::prefix('sub-produk')->group(function () {
                Route::get('/',[SubProdukController::class,'index']);
                Route::post('/',[SubProdukController::class,'store']);
                Route::get('/{id}',[SubProdukController::class,'show']);
                Route::put('/{id}',[SubProdukController::class,'update']);
                Route::delete('/{id}',[SubProdukController::class,'destroy']);
            });
            // route cabang
            Route::prefix('cabang')->group(function () {
                Route::get('/',[CabangController::class,'index']);
                Route::get('/lokasi',[CabangController::class,'getLocation']);
                Route::post('/',[CabangController::class,'store']);
                Route::get('/{id}',[CabangController::class,'show']);
                Route::put('/{id}',[CabangController::class,'update']);
                Route::delete('/{id}',[CabangController::class,'destroy']);
            });
            // route jenis instansi
            Route::prefix('jenis-instansi')->group(function () {
                Route::get('/',[JenisInstansiController::class,'index']);
                Route::post('/',[JenisInstansiController::class,'store']);
                Route::get('/{id}',[JenisInstansiController::class,'show']);
                Route::put('/{id}',[JenisInstansiController::class,'update']);
                Route::delete('/{id}',[JenisInstansiController::class,'destroy']);
            });
            // route jenis kelamin
            Route::prefix('jenis-kelamin')->group(function () {
                Route::get('/',[JenisKelaminController::class,'index']);
                Route::post('/',[JenisKelaminController::class,'store']);
                Route::get('/{id}',[JenisKelaminController::class,'show']);
                Route::put('/{id}',[JenisKelaminController::class,'update']);
                Route::delete('/{id}',[JenisKelaminController::class,'destroy']);
            });
            // route jenis pekerjaan
            Route::prefix('jenis-pekerjaan')->group(function () {
                Route::get('/',[JenisPekerjaanController::class,'index']);
                Route::post('/',[JenisPekerjaanController::class,'store']);
                Route::get('/{id}',[JenisPekerjaanController::class,'show']);
                Route::put('/{id}',[JenisPekerjaanController::class,'update']);
                Route::delete('/{id}',[JenisPekerjaanController::class,'destroy']);
            });
            // route tingkat pendidikan
            Route::prefix('tingkat-pendidikan')->group(function () {
                Route::get('/',[TingkatPendidikanController::class,'index']);
                Route::post('/',[TingkatPendidikanController::class,'store']);
                Route::get('/{id}',[TingkatPendidikanController::class,'show']);
                Route::put('/{id}',[TingkatPendidikanController::class,'update']);
                Route::delete('/{id}',[TingkatPendidikanController::class,'destroy']);
            });
            // route tingkat pendidikan
            Route::prefix('status-pernikahan')->group(function () {
                Route::get('/',[StatusPernikahanController::class,'index']);
                Route::post('/',[StatusPernikahanController::class,'store']);
                Route::get('/{id}',[StatusPernikahanController::class,'show']);
                Route::put('/{id}',[StatusPernikahanController::class,'update']);
                Route::delete('/{id}',[StatusPernikahanController::class,'destroy']);
            });
            // route tujuan pemasaran
            Route::prefix('tujuan-pemasaran')->group(function () {
                Route::get('/',[TujuanPemasaranController::class,'index']);
                Route::post('/',[TujuanPemasaranController::class,'store']);
                Route::get('/{id}',[TujuanPemasaranController::class,'show']);
                Route::put('/{id}',[TujuanPemasaranController::class,'update']);
                Route::delete('/{id}',[TujuanPemasaranController::class,'destroy']);
            });
            // route Hubungan
            Route::prefix('hubungan')->group(function () {
                Route::get('/',[HubunganController::class,'index']);
                Route::post('/',[HubunganController::class,'store']);
                Route::get('/{id}',[HubunganController::class,'show']);
                Route::put('/{id}',[HubunganController::class,'update']);
                Route::delete('/{id}',[HubunganController::class,'destroy']);
            });
            // cara pemasaran
            Route::prefix('cara-pemasaran')->group(function () {
                Route::get('/',[CaraPemasaranController::class,'index']);
                Route::post('/',[CaraPemasaranController::class,'store']);
                Route::get('/{id}',[CaraPemasaranController::class,'show']);
                Route::put('/{id}',[CaraPemasaranController::class,'update']);
                Route::delete('/{id}',[CaraPemasaranController::class,'destroy']);
            });
            // status tempat tinggal
            Route::prefix('status-tempat-tinggal')->group(function () {
                Route::get('/',[StatusTempatTinggalController::class,'index']);
                Route::post('/',[StatusTempatTinggalController::class,'store']);
                Route::get('/{id}',[StatusTempatTinggalController::class,'show']);
                Route::put('/{id}',[StatusTempatTinggalController::class,'update']);
                Route::delete('/{id}',[StatusTempatTinggalController::class,'destroy']);
            });
        });
        // simulasi
        Route::prefix('simulasi')->group(function () {
            Route::post('/kredit',[SimulasiController::class,'process']);
            Route::post('/kredit-web',[SimulasiController::class,'processWeb']);
        });
        // eform
        Route::prefix('eform')->group(function () {
            Route::prefix('main')->group(function () {
                Route::get('/',[EformController::class,'index']);
                Route::get('/aktifitas',[EformController::class,'aktifitas']);
                Route::post('/keputusan',[EformController::class,'keputusan']);
                Route::prefix('prescreening')->group(function (){
                    Route::get('/',[EformController::class,'getDataPrescreening']);
                    Route::get('/aktifitas/{id}',[EformController::class,'getAktifitasPrescreening']);
                    Route::get('/{id}',[EformController::class,'getDetailPrescreening']);
                });
                Route::get('/{id}',[EformController::class,'show']);
            });
            Route::prefix('mobile')->group(function () {
                Route::post('/',[EformController::class,'prescreening']);
            });
        });

        Route::prefix('dashboard')->group(function () {
            Route::get('/segmen-penjaminan',[DashboardController::class,'segmenPenjaminan']);
        });

    });
});
