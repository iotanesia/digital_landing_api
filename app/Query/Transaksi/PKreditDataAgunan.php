<?php

namespace App\Query\Transaksi;

use App\Models\Transaksi\PKreditDataAgunan as Model;
use Illuminate\Support\Facades\DB;
class PKreditDataAgunan
{

    public static function byId($id)
    {
        return Model::find($id);

    }

    public static function byIdpipeline($id_pipeline)
    {
        return Model::where('id_pipeline',$id_pipeline)->orderBy('id','desc')->get()->map(function ($item)
        {
            $item->nama_agunan = $item->refAgunan->nama ?? null;
            unset(
                $item->refAgunan
            );
            return $item;
        });

    }

    public static function store($request,$is_transaction = true)
    {
        if($is_transaction) DB::beginTransaction();
        try {

            $require_fileds = [];
            if(!$request->id_pipeline) $require_fileds[] = 'id_pipeline';
            if(!$request->id_agunan) $require_fileds[] = 'id_agunan';
            if(count($require_fileds) > 0) throw new \Exception('This parameter must be filled '.implode(',',$require_fileds),400);
            // set ltv taksasi
            $params_agunan = ProsesKredit::setLtvTaksasi($request);

            $attr = $request->all();
            $attr['ltv'] = $params_agunan['ltv'];
            $attr['taksasi'] = $params_agunan['taksasi'];
            $attr['created_by'] = $request->current_user->id;
            $attr['updated_by'] = $request->current_user->id;
            $store =  Model::where([
                'id' => $request->id_proses_data_agunan,
            ])->first();
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
