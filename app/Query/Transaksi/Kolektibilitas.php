<?php

namespace App\Query\Transaksi;
use App\Models\Transaksi\Kolektibilitas as Model;
use App\ApiHelper as Helper;
use App\Constants\Constants;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class Kolektibilitas {

    public static function prescreening($request,$is_transaction = true)
    {
        if($is_transaction) DB::beginTransaction();
        try {

            

            if($is_transaction) DB::commit();
        } catch (\Throwable $th) {
            if($is_transaction) DB::rollBack();
            throw $th;
        }
    }

}
