<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ApiHelper as Helper;
use App\Query\Transaksi\ProsesKredit;

class ProsesKreditController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            return Helper::resultResponse(
                ProsesKredit::getDataCurrent($request)
            );
        } catch (\Throwable $th) {
            return Helper::setErrorResponse($th);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function menu(Request $request,$id)
    {
        try {
            return Helper::resultResponse(
                ProsesKredit::getMenu($request,$id)
            );
        } catch (\Throwable $th) {
            return Helper::setErrorResponse($th);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateDataPersonal(Request $request)
    {
        try {
            return Helper::resultResponse(
                ProsesKredit::updateDataPersonal($request)
            );
        } catch (\Throwable $th) {
            return Helper::setErrorResponse($th);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function dataPersonal(Request $request,$id)
    {
        try {
            return Helper::resultResponse(
                ProsesKredit::dataPersonal($id)
            );
        } catch (\Throwable $th) {
            return Helper::setErrorResponse($th);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateDataKeuangan(Request $request)
    {
        try {
            return Helper::resultResponse(
                ProsesKredit::updateDataKeuangan($request)
            );
        } catch (\Throwable $th) {
            return Helper::setErrorResponse($th);
        }
    }

     /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function agunan(Request $request,$id)
    {
        try {
            return Helper::resultResponse(
                ProsesKredit::agunan($id)
            );
        } catch (\Throwable $th) {
            return Helper::setErrorResponse($th);
        }
    }

     /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function storeAgunan(Request $request)
    {
        try {
            return Helper::resultResponse(
                ProsesKredit::storeAgunan($request)
            );
        } catch (\Throwable $th) {
            return Helper::setErrorResponse($th);
        }
    }
         /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function tanahBangunan(Request $request,$id)
    {
        try {
            return Helper::resultResponse(
                ProsesKredit::tanahBangunan($id)
            );
        } catch (\Throwable $th) {
            return Helper::setErrorResponse($th);
        }
    }

     /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function storeTanahBangunan(Request $request)
    {
        try {
            return Helper::resultResponse(
                ProsesKredit::storeTanahBangunan($request)
            );
        } catch (\Throwable $th) {
            return Helper::setErrorResponse($th);
        }
    }


             /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function tanahKosong(Request $request,$id)
    {
        try {
            return Helper::resultResponse(
                ProsesKredit::tanahKosong($id)
            );
        } catch (\Throwable $th) {
            return Helper::setErrorResponse($th);
        }
    }

     /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function storeTanahKosong(Request $request)
    {
        try {
            return Helper::resultResponse(
                ProsesKredit::storeTanahKosong($request)
            );
        } catch (\Throwable $th) {
            return Helper::setErrorResponse($th);
        }
    }

             /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function kios(Request $request,$id)
    {
        try {
            return Helper::resultResponse(
                ProsesKredit::Kios($id)
            );
        } catch (\Throwable $th) {
            return Helper::setErrorResponse($th);
        }
    }

     /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function storeKios(Request $request)
    {
        try {
            return Helper::resultResponse(
                ProsesKredit::storeKios($request)
            );
        } catch (\Throwable $th) {
            return Helper::setErrorResponse($th);
        }
    }

             /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function kendaraan(Request $request,$id)
    {
        try {
            return Helper::resultResponse(
                ProsesKredit::Kendaraan($id)
            );
        } catch (\Throwable $th) {
            return Helper::setErrorResponse($th);
        }
    }

     /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function storeKendaraan(Request $request)
    {
        try {
            return Helper::resultResponse(
                ProsesKredit::storeKendaraan($request)
            );
        } catch (\Throwable $th) {
            return Helper::setErrorResponse($th);
        }
    }

             /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function Deposito(Request $request,$id)
    {
        try {
            return Helper::resultResponse(
                ProsesKredit::Deposito($id)
            );
        } catch (\Throwable $th) {
            return Helper::setErrorResponse($th);
        }
    }

     /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function storeDeposito(Request $request)
    {
        try {
            return Helper::resultResponse(
                ProsesKredit::storeDeposito($request)
            );
        } catch (\Throwable $th) {
            return Helper::setErrorResponse($th);
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function dataKeuangan(Request $request,$id)
    {
        try {
            return Helper::resultResponse(
                ProsesKredit::dataKeuangan($id)
            );
        } catch (\Throwable $th) {
            return Helper::setErrorResponse($th);
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function dataUsaha(Request $request,$id)
    {
        try {
            return Helper::resultResponse(
                ProsesKredit::dataUsaha($id)
            );
        } catch (\Throwable $th) {
            return Helper::setErrorResponse($th);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function storeDataUsaha(Request $request)
    {
        try {
            return Helper::resultResponse(
                ProsesKredit::storeDataUsaha($request)
            );
        } catch (\Throwable $th) {
            return Helper::setErrorResponse($th);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
