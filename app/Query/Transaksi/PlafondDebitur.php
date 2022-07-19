<?php

namespace App\Query\Transaksi;
use Carbon\Carbon;
use App\Models\Transaksi\PlafondDebitur as Model;
use App\ApiHelper as Helper;
use App\Constants\Constants;
use App\Models\Transaksi\VerifValidasiData;
use Illuminate\Support\Facades\DB;


class PlafondDebitur {

    public static function prescreening($request,$is_transaction = true)
    {
        if($is_transaction) DB::beginTransaction();
        try {

            Model::create($request);    

            if($is_transaction) DB::commit();
        } catch (\Throwable $th) {
            if($is_transaction) DB::rollBack();
            throw $th;
        }
    }

}
