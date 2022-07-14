<?php

namespace App\Query\Transaksi;
use App\Query\Transaksi\AktifitasPemasaran;
use App\Query\Transaksi\Leads;
use App\Query\Transaksi\Eform;
use Illuminate\Support\Facades\DB;

class ApprovalPrescreening {

    public static function getDataCurrent($request)
    {
        # code...
    }

    public static function byId($id)
    {
        # code...
    }

    public static function store($request,$is_transaction = true)
    {
        if($is_transaction) DB::beginTransaction();
        try {
            //code...

            if($is_transaction) DB::commit();
        } catch (\Throwable $th) {
            if($is_transaction) DB::rollback();
            throw $th;
        }
    }
}
