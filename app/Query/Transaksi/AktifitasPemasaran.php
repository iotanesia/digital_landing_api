<?php

namespace App\Query\Transaksi;
use App\Models\Transaksi\AktifitasPemasaran as Model;
use App\ApiHelper as Helper;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AktifitasPemasaran {

    // detail data aktifitas pemasaran
    public static function byId($id_aktifitas_pemasaran)
    {
        //code
    }

    // list data
    /*
        - current id user = id_user
        - is_cutoff = 0
        - is_pipeline = 0"
    */
    public static function getDataCurrent($request)
    {
        //code
    }

    // input data
    /* -
        - jika berminat terdapat proses prescreening
        - notif email
        return nomor aplikasi dan nik
    */
    public static function store($request, $is_transaction = true)
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



    // update data rm
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
}