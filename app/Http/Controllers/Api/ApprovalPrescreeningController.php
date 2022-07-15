<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ApiHelper as Helper;
use App\Query\Transaksi\ApprovalPrescreening;

class ApprovalPrescreeningController extends Controller
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
                ApprovalPrescreening::getDataCurrent($request)
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
                ApprovalPrescreening::byId($request)

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
    public function show(Request $request,$id,$type)
    {
        try {
            return Helper::resultResponse(
                ApprovalPrescreening::byId($id,$type)
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
    public function update(Request $request)
    {
        try {
            return Helper::resultResponse(
                ApprovalPrescreening::approve($request)
            );
        } catch (\Throwable $th) {
            return Helper::setErrorResponse($th);
        }
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

    public function prescreening(Request $request,$id,$tipe)
    {
        try {
            return Helper::resultResponse(
                ApprovalPrescreening::getInfoPrescreening($request,$id,$tipe)
            );
        } catch (\Throwable $th) {
            return Helper::setErrorResponse($th);
        }
    }
}
