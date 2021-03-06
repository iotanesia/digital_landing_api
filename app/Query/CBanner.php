<?php

namespace App\Query;
use App\ApiHelper as Helper;
use App\Models\CBanner as Model;
use Illuminate\Support\Facades\DB;
use App\Constants\Constants;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CBanner {

    public static function byId($id)
    {
        return ['items' => Model::find($id)];
    }

    public static function byKode($kode)
    {
        return ['items' => Model::where('kode_produk',$kode)->first()];
    }

    public static function getAll($request)
    {
        try {
            if($request->dropdown == Constants::IS_ACTIVE) $request->limit = Model::count();
            $data = Model::where(function ($query) use ($request){
                if($request->judul) $query->where('judul','ilike',"%$request->judul%");
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
            if(!$request->judul) $require_fileds[] = 'judul';
            if(count($require_fileds) > 0) throw new \Exception('This parameter must be filled '.implode(',',$require_fileds),400);

            if($request->foto){
                $image = $request->foto;  // your base64 encoded
                $namafoto =(string) Str::uuid().'.png';
            }

            $store = Model::create($request->all());
            if($is_transaction) DB::commit();
            if($request->foto) Storage::put($namafoto, base64_decode($image));
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

            if($request->foto){
                $image = $request->foto;  // your base64 encoded
                $namafoto =(string) Str::uuid().'.png';
            }

            if(!$update) throw new \Exception("Data not found.", 400);
            $update->update($request->all());
            if($is_transaction) DB::commit();
            if($request->foto) Storage::put($namafoto, base64_decode($image));

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
