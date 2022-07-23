<?php

namespace App\Query\Transaksi;

use App\Constants\Constants;
use App\Models\Transaksi\Pipeline;
use Carbon\Carbon;
use App\Models\Transaksi\VerifValidasiData as Model;



class Skoring {

    public static function getDataCurrent($request)
    {
        try {
            $data = Pipeline::where(function ($query) use ($request){
                $query->where('tracking',Constants::ANALISA_KREDIT);
                $query->where('step_analisa_kredit',Constants::STEP_DATA_SEDANG_PROSES_SKORING);
                $query->where('id_user',$request->current_user->id);
            })->paginate($request->limit);
            return [
                'items' => $data->getCollection()->transform(function ($item){

                    if($item->id_tipe_calon_nasabah == Constants::TCN_EFORM) $data = $item->refEform;
                    elseif($item->id_tipe_calon_nasabah == Constants::TCN_AKTIFITAS_PEMASARAN) $data = $item->refAktifitasPemasaran;
                    elseif ($item->id_tipe_calon_nasabah == Constants::TCN_LEAD) $data = $item->refLeads;
                    else $data = null;

                    $id_jenis_kelamin = $data->id_jenis_kelamin ?? null;
                    return [
                        'id' => $item->id ?? null,
                        'nik' => $item->nik ?? null,
                        'nama' => $data->nama ?? null,
                        'nama_produk'=> $data->refProduk->nama ?? null,
                        'nama_sub_produk'=> $data->refSubProduk->nama ?? null,
                        'created_at' => $item->created_at ?? null,
                        'nilai' => 80, //  dummy
                        'status' => 'menunggu approval rm', //  dummy
                        'foto' => $id_jenis_kelamin == 2 ? 'female.png' : 'male.png'
                    ];
                }),
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

    public static function approval($id)
    {
        # code...
    }

    public static function storeApproval($request)
    {
        # code...
    }
}
