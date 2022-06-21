<?php

namespace App\Query;
use App\ApiHelper as Helper;
use App\Models\LogPrescreening as Model;
use Illuminate\Support\Facades\DB;
use App\Constants\Constants;
class LogPrescreening {

    public static function store($request)
    {
        DB::beginTransaction();
        try {
            Model::create($request);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

    }

}
