<?php

namespace App\Query;
use App\ApiHelper as Helper;
use App\Models\MKelurahan as Model;
use Illuminate\Support\Facades\DB;
use App\Constants\Constants;
class MKelurahan {

    public static function byId($id)
    {
        return ['items' => Model::find($id)];
    }

    public static function getAll($request)
    {
        try {
            if($request->dropdown == Constants::IS_ACTIVE) $request->limit = Model::count();
            $data = Model::where(function ($query) use ($request){
                if($request->nama_kelurahan) $query->where('nama_kelurahan','ilike',"%$request->nama_kelurahan%");
                if($request->id_kecamatan) $query->where('id_kecamatan',$request->id_kecamatan);
            })
            ->orderBy('id_kelurahan','asc')
            ->paginate($request->limit);
                return [
                    'items' => $data->items(),
                    'attributes' => [
                        'total' => $data->total(),
                        'current_page' => $data->currentPage(),
                        'from' => $data->currentPage(),
                        'per_page' => (int) $data->perPage(),
                    ]
                ];
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function store($request,$is_transaction = true)
    {
        if($is_transaction) DB::beginTransaction();
        try {

            $require_fileds = [];
            if(!$request->id_kecamatan) $require_fileds[] = 'id_kecamatan';
            if(!$request->nama_kelurahan) $require_fileds[] = 'nama_kelurahan';
            if(count($require_fileds) > 0) throw new \Exception('This parameter must be filled '.implode(',',$require_fileds),400);

            $store = Model::create($request->all());
            if($is_transaction) DB::commit();
            return $store;
        } catch (\Throwable $th) {
            if($is_transaction) DB::rollBack();
            throw $th;
        }
    }

    public static function updated($request,$id,$is_transaction = true)
    {
        if($is_transaction) DB::beginTransaction();
        try {
            $update = Model::find($id);
            if(!$update) throw new \Exception("Data not found.", 400);
            $update->update($request->all());
            if($is_transaction) DB::commit();
            return $update;
        } catch (\Throwable $th) {
            if($is_transaction) DB::rollBack();
            throw $th;
        }
    }

    public static function destroy($id,$is_transaction = true)
    {
        if($is_transaction) DB::beginTransaction();
        try {
            $delete = Model::destroy($id);
            if($is_transaction) DB::commit();
            return $delete;
        } catch (\Throwable $th) {
            if($is_transaction) DB::rollback();
            throw $th;
        }
    }
}
