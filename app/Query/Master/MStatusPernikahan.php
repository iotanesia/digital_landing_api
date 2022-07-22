<?php

namespace App\Query\Master;
use App\ApiHelper as Helper;
use App\Models\Master\MStatusPernikahan as Model;
use Illuminate\Support\Facades\DB;

class MStatusPernikahan {

    public static function idByNama($nama)
    {
        $data = Model::where('nama',$nama)->first();
        return $data->id ?? null;
    }

    public static function byId($id)
    {
        return ['items' => Model::find($id)];
    }

    public static function getAll($request)
    {
        try {
            $data = Model::where(function ($query) use ($request){
                if($request->nama) $query->where('nama','ilike',"%$request->nama%");
            })->paginate($request->limit);
                return [
                    'items' => $data->items(),
                    'attributes' => [
                        'total' => $data->total(),
                        'current_page' => $data->currentPage(),
                        'from' => $data->currentPage(),
                        'per_page' => $data->perPage(),
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
            if(!$request->nama_status_pernikahan) $require_fileds[] = 'nama_status_pernikahan';
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

    public static function getStatusMenikah($id) {
        try {
            $result = Model::where('nama', 'KAWIN')->first();
            return $id ? $result->id : $result;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function getStatusBelumMenikah($id) {
        try {
            $result = Model::where('nama', 'Belum Menikah')->first();
            return $id ? $result->id : $result;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function getStatusCerai($id) {
        try {
            $result = Model::where('nama', 'Cerai')->first();
            return $id ? $result->id : $result;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
