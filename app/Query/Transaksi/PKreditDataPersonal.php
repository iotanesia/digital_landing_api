<?php

namespace App\Query\Transaksi;
use App\Models\Transaksi\PKreditDataPersonal as Model;
use Illuminate\Support\Facades\DB;

class PKreditDataPersonal {

    public static function store($request,$is_transaction = true)
    {
        if($is_transaction) DB::beginTransaction();
        try {

            $require_fileds = [];
            if(!$request->id_pipeline) $require_fileds[] = 'id_pipeline';
            if(count($require_fileds) > 0) throw new \Exception('This parameter must be filled '.implode(',',$require_fileds),400);

            $store =  Model::where('id_pipeline',$request->id_pipeline)->first();
            if(!$store) $store = new Model;
            $store->fill($request->all());
            $store->save();

            if($is_transaction) DB::commit();
        } catch (\Throwable $th) {
            if($is_transaction) DB::rollback();
            throw $th;
        }
    }
}
