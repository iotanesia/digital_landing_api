<?php

namespace App\Query\Master;
use App\ApiHelper as Helper;
use App\Models\Master\MSubProduk as Model;
use Illuminate\Support\Facades\DB;
use App\Constants\Constants;

class MSubProduk {

    public static function byId($id)
    {
        return ['items' => Model::find($id)];
    }

    public static function byKode($kode)
    {
        return ['items' => Model::where('kode_sub_produk',$kode)->first()];
    }

    public static function byIdSubProduk($id_sub_produk)
    {
        return ['items' => Model::where('id_sub_produk',$id_sub_produk)->first()];
    }

    public static function getAll($request)
    {
        try {
            if($request->dropdown == Constants::IS_ACTIVE) $request->limit = Model::count();
            $data = Model::where(function ($query) use ($request){
                if($request->nama) $query->where('nama','ilike',"%$request->nama%");
                if($request->id_produk) $query->where('id_produk',$request->id_produk);
            })->paginate($request->limit);
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
            if(!$request->nama_sub_produk) $require_fileds[] = 'nama_sub_produk';
            if(!$request->kode_sub_produk) $require_fileds[] = 'kode_sub_produk';
            if(!$request->kode_produk) $require_fileds[] = 'kode_produk';
            if(!$request->suku_bunga) $require_fileds[] = 'suku_bunga';
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

    public static function plafon($id, $plafon) {
        try {
            $sub_produk = Model::where('id_sub_produk',$id)->first();
            if(!$sub_produk) return true;
            return ($plafon > $sub_produk->maks_plafon || $plafon > $sub_produk->min_plafon);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function getIdProduk($id) {
        try {
            return Model::find($id)->id_produk;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
