<?php

namespace App\Query\Transaksi;
use App\Models\Transaksi\VerifProfilUsaha as Model;
use App\Models\Transaksi\Pipeline as ModelPipeline;
use App\ApiHelper as Helper;
use App\Constants\Constants;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class VerifProfilUsaha {

    public static function store($request,$id_verifikasi_validasi_data, $is_transaction = true)
    {
        if($is_transaction) DB::beginTransaction();
        try {
            if($request->profil_usaha){
                foreach ($request->profil_usaha as $key => $item) {
                    $check = Model::where('id',$item['id'])->first();
                    if($check) $store = $check;
                    else {
                        $store = new Model;
                        $store->id_verifikasi_validasi_data = $id_verifikasi_validasi_data;
                    };
                    $store->npwp =$item['npwp'] ?? null;
                    $store->nama_usaha =$item['nama_usaha'] ?? null;
                    $store->profil_usaha =$item['profil_usaha'] ?? null;
                    $store->alamat_usaha =$item['alamat_usaha'] ?? null;
                    $store->mulai_operasi =$item['mulai_operasi'] ?? null;
                    $store->id_propinsi =$item['id_propinsi'] ?? null;
                    $store->id_kabupaten =$item['id_kabupaten'] ?? null;
                    $store->id_kecamatan =$item['id_kecamatan'] ?? null;
                    $store->id_kelurahan =$item['id_kelurahan'] ?? null;
                    $store->created_by = $request->current_user->id;
                    $store->save();
                }
            }
            if($is_transaction) DB::commit();
        } catch (\Throwable $th) {
            if($is_transaction) DB::rollBack();
            throw $th;
        }
    }
}
