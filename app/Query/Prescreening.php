<?php

namespace App\Query;
use App\ApiHelper as Helper;
use Illuminate\Support\Facades\DB;

class Prescreening {

    public static function process($request, $is_transaction = true){

        if($is_transaction) DB::beginTransaction();
        try {
            
            if($is_transaction) DB::commit();
        } catch (\Throwable $th) {
            if($is_transaction) DB::rollBack();
            throw $th;
        }
    }

}
