<?php

namespace App\Http\Controllers\Open;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ApiHelper as Helper;
use App\Query\Transaksi\Eform;
use App\Sp\SpListPipeline;
use Illuminate\Support\Facades\Hash;

class EformController extends Controller
{

     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function check(Request $request)
    {
        try {
            return Helper::resultResponse(
                Eform::dwhMikro($request)
            );
        } catch (\Throwable $th) {
            return Helper::setErrorResponse($th);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            return Helper::resultResponse(
                SpListPipeline::getDataCurrent($request)
                // Eform::getListClientData($request)
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
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            return Helper::resultResponse(
                Eform::storeClientEform($request)
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
    public function show(Request $request)
    {
        try {
            return Helper::resultResponse(
                Eform::byNomorAplikasi($request)
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
    public function tracking(Request $request)
    {
        try {
            return Helper::resultResponse(
                Eform::tracking($request)
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
