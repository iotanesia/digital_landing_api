<?php

namespace App\Query\Transaksi;

use App\Models\Transaksi\SkoringPenilaianDetail as Model;
use Illuminate\Support\Facades\DB;

class SkoringPenilaianDetail
{
    public static function store($request, $is_transaction = true)
    {
        if($is_transaction) DB::beginTransaction();
        try {

            $store =  Model::where([
                'id' => $request['id'],
            ])->first();
            if(!$store) $store = new Model;
            $store->fill($request);
            $store->save();

            if($is_transaction) DB::commit();
            return $store;
        } catch (\Throwable $th) {
            if($is_transaction) DB::rollBack();
            throw $th;
        }
    }
}
