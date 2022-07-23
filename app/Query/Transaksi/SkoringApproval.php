<?php

namespace App\Query\Transaksi;

use App\Models\Transaksi\SkoringApproval as Model;
use Illuminate\Support\Facades\DB;
class SkoringApproval
{
    public static function store($request, $is_transaction = true)
    {
        if($is_transaction) DB::beginTransaction();
        try {

            $store =  Model::where([
                'id_pipeline' => $request['id_pipeline'],
            ])->first();
            if(!$store) $store = new Model;
            $store->fill($request);
            $store->save();

            if($is_transaction) DB::commit();
        } catch (\Throwable $th) {
            if($is_transaction) DB::rollBack();
            throw $th;
        }
    }

}
