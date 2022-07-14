<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ApiHelper as Helper;
use App\Query\Transaksi\Pipeline;
use App\Query\Transaksi\VerifOnsiteVisit;

class VerifikasiController extends Controller
{
    /**
     *
     * ambil data dari pipeline where tracking = 2 and id_user (logged_in)
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            return Helper::responseData(
                Pipeline::getDataVerifies($request)
            );
        } catch (\Throwable $th) {
            return Helper::setErrorResponse($th);
        }
    }

    /** cek pipeline
     * jika tracking = 2 dan step analisa kredit= 0 is_checked false semua
     * jika tracking = 2 dan step analisa kredit = 1 is_checked verifikasi data true
     * jika tracking = 2 dan step analisa kredit = 2 is_checked onsite visit true
     * jika tracking = 2 dan step analisa kredit = 3 is_checked kelengkapan data true
     * @return \Illuminate\Http\Response
     */
    public function menu(Request $request,$id)
    {
        $menu = [
            [
                'code' => '01',
                'name' => 'verifikasi data',
                'is_checked' => true
            ],
            [
                'code' => '01',
                'name' => 'onsite visit',
                'is_checked' => true
            ],
            [
                'code' => '01',
                'name' => 'kelengkapan data',
                'is_checked' => true
            ]
        ];
    }

      /**
     * get data by id_pipeline
     * verifikasi_onsite_visit
     *
     * @return \Illuminate\Http\Response
     */
    public function onsiteVisit(Request $request,$id)
    {
        try {
            return Helper::responseData(
                'code here'
            );
        } catch (\Throwable $th) {
            return Helper::setErrorResponse($th);
        }
    }

    /**
     * insert to
     * verifikasi_onsite_visit
     * pipeline update step_analias_kredit 2
     * @return \Illuminate\Http\Response
     */
    public function storeOnsiteVisit(Request $request)
    {
        try {
            return Helper::responseData(
                VerifOnsiteVisit::storeOnsiteVisit($request)
            );
        } catch (\Throwable $th) {
            return Helper::setErrorResponse($th);
        }
    }

     /**
     * get data by id_pipeline
     * verifikasi_kelengkapan_dokumen
     * pipeline update step_analias_kredit 3
     * @return \Illuminate\Http\Response
     */
    public function dokumen(Request $request,$id)
    {
        try {
            return Helper::responseData(
                'code here'
            );
        } catch (\Throwable $th) {
            return Helper::setErrorResponse($th);
        }
    }

       /**
    * insert to
     * verifikasi_kelengkapan_dokumen
     * pipeline update step_analias_kredit 3
     * @return \Illuminate\Http\Response
     */
    public function storeDokumen(Request $request)
    {
        try {
            return Helper::responseData(
                'code here'
            );
        } catch (\Throwable $th) {
            return Helper::setErrorResponse($th);
        }
    }

          /**
      * pipeline update step_analias_kredit 4
     *
     * @return \Illuminate\Http\Response
     */
    public function submit(Request $request)
    {
        try {
            return Helper::responseData(
                'code here'
            );
        } catch (\Throwable $th) {
            return Helper::setErrorResponse($th);
        }
    }

    /**
     * update ke table asal, eform, analisa_pemasaran, leads
     * pipeline update step_analias_kredit 1
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            return Helper::responseData(
                'code here'
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
    public function show($id)
    {
        //
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
