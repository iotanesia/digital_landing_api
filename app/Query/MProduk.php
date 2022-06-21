<?php

namespace App\Query;
use App\ApiHelper as Helper;
use App\Models\MProduk as Model;
use Illuminate\Support\Facades\DB;
use App\Constants\Constants;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MProduk {

    public static function byId($id)
    {
        return ['items' => Model::find($id)];
    }

    public static function byKode($kode)
    {
        return ['items' => Model::where('kode_produk',$kode)->first()];
    }

    public static function byIdProduk($id_produk)
    {
        return ['items' => Model::where('id_produk',$id_produk)->first()];
    }

    public static function getAll($request)
    {
        try {
            if($request->dropdown == Constants::IS_ACTIVE) $request->limit = Model::count();
            $data = Model::where(function ($query) use ($request){
                if($request->nama_produk) $query->where('nama_produk','ilike',"%$request->nama_produk%");
                if($request->id_jenis_produk) $query->where('id_jenis_produk',$request->id_jenis_produk);
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
            if(!$request->nama_produk) $require_fileds[] = 'nama_produk';
            if(count($require_fileds) > 0) throw new \Exception('This parameter must be filled '.implode(',',$require_fileds),400);

            if($request->foto_produk){
                $image = $request->foto_produk;  // your base64 encoded
                $namafoto =(string) Str::uuid().'.png';
            }

            if($request->banner_produk){
                $banner = $request->banner_produk;  // your base64 encoded
                $namabanner =(string) Str::uuid().'.png';
            }

            $store = Model::create($request->all());
            if($is_transaction) DB::commit();
            if($request->foto_produk) Storage::put($namafoto, base64_decode($image));
            if($request->banner_produk) Storage::put($namabanner, base64_decode($banner));
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

            if($request->foto_produk){
                $image = $request->foto_produk;  // your base64 encoded
                $namafoto =(string) Str::uuid().'.png';
            }

            if($request->banner_produk){
                $banner = $request->banner_produk;  // your base64 encoded
                $namabanner =(string) Str::uuid().'.png';
            }

            if(!$update) throw new \Exception("Data not found.", 400);
            $update->update($request->all());
            if($is_transaction) DB::commit();
            if($request->foto_produk) Storage::put($namafoto, base64_decode($image));
            if($request->banner_produk) Storage::put($namabanner, base64_decode($banner));

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
            $produk = Model::where('id_produk',$id)->first();
            if(!$produk) return false;
            if($plafon > $produk->maks_plafon) return true;
            if($plafon > $produk->min_plafon) return true;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
