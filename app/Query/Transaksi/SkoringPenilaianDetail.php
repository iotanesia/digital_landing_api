<?php

namespace App\Query\Transaksi;

use App\Models\Transaksi\SkoringPenilaianDetail as Model;
class SkoringPenilaianDetail
{
    public static function store($request, $is_transaction = true)
    {
        if($is_transaction) DB::beginTransaction();
        try {

            $store =  Model::where([
                'id_skoring_penilaian' => $request['id_skoring_penilaian'],
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
