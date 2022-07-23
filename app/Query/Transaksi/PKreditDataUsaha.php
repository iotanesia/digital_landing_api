<?php

namespace App\Query\Transaksi;
use App\Models\Transaksi\PKreditDataUsaha as Model;
use Illuminate\Support\Facades\DB;
use stdClass;

class PKreditDataUsaha {

    public static function byIdPipeline($id_pipeline)
    {
        $data = Model::where('id_pipeline',$id_pipeline)->first();
        if(!$data) $data = new stdClass;
        return $data;
    }

    public static function store($request,$is_transaction = true)
    {
        if($is_transaction) DB::beginTransaction();
        try {

            $require_fileds = [];
            if(!$request->id_pipeline) $require_fileds[] = 'id_pipeline';
            if(count($require_fileds) > 0) throw new \Exception('This parameter must be filled '.implode(',',$require_fileds),400);

            $attr = $request->all();
            $attr['created_by'] = $request->current_user->id;
            $attr['updated_by'] = $request->current_user->id;
            $store =  Model::where('id_pipeline',$request->id_pipeline)->first();
            if(!$store) $store = new Model;
            $store->fill($attr);
            $store->save();

            if($is_transaction) DB::commit();
            return $store;
        } catch (\Throwable $th) {
            if($is_transaction) DB::rollback();
            throw $th;
        }
    }
}
