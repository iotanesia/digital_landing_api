<?php

namespace App\Query\Transaksi;
use App\Models\Transaksi\Pipeline as Model;
use App\ApiHelper as Helper;
use App\Constants\Constants;
use App\Query\Status\StsTracking;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class Pipeline {

    // detail data aktifitas pemasaran
    public static function byId($id_aktifitas_pemasaran)
    {
        //code
    }

    // list data
    /*
        - current user id cabang = id_cabang
        - is_pipeline = 0
        - is_prescreening in 1,2
        - is_cutoff = 0
    */
    public static function getDataCurrent($request)
    {
        //code
    }


    // update data rm
     /*
        - notif email
    */
    public static function updateDataRm($request, $is_transaction = true)
    {
        if($is_transaction) DB::beginTransaction();
        try {

            //code
            if($is_transaction) DB::commit();

        } catch (\Throwable $th) {
            if($is_transaction) DB::rollBack();
            throw $th;
        }
    }

    // list data pipeline eform
    public static function getDataPipeline($request)
    {
        //code
    }

    // list informasi prescreening
    public static function getInfoPrescreening($request)
    {
         //code
    }

    // list history aktifitas
    public static function getHistoryAktifitas($request)
    {
         //code
    }

    public static function checkNasabah($nik) {
        try {
            $data = Model::where('nik', $nik)->first();
            $result = ['is_pipeline'=>Constants::IS_ACTIVE,'is_cutoff'=>Constants::IS_NOL];

            if($data && $data->tracking != StsTracking::getIdDisbursment(true)) $result = ['is_pipeline'=>Constants::IS_NOL,'is_cutoff'=>Constants::IS_ACTIVE];

            return $result;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
