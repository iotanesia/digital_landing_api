<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ApiHelper as Helper;
use App\Query\Eform;
use App\Query\Prescreening;

class EformController extends Controller
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
                Eform::getData($request)
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
    public function prescreening(Request $request)
    {
        try {
            return Helper::resultResponse(
                Prescreening::process($request)
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
    public function getDataPrescreening(Request $request)
    {
        try {
            return Helper::resultResponse(
                Prescreening::getAll($request)
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
    public function getDetailPrescreening(Request $request,$id)
    {
        try {
            return Helper::resultResponse(
                Prescreening::byId($id)
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
    public function getAktifitasPrescreening(Request $request,$id)
    {
        try {
            return Helper::resultResponse(
                Prescreening::aktifitas($id)
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
    public function storeWeb(Request $request)
    {
        try {
            return Helper::resultResponse(
                Eform::store($request)
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
    public function storeMobile(Request $request)
    {
        try {
            return Helper::resultResponse(
                Eform::store($request)
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
    public function show(Request $request,$id)
    {
        try {
            return Helper::resultResponse(
                Eform::byId($id)
            );
        } catch (\Throwable $th) {
            return Helper::setErrorResponse($th);
        }
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
