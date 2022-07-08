<?php

namespace App\Query\Transaksi;
use App\Models\Transaksi\Pipeline as Model;
use App\ApiHelper as Helper;
use App\Constants\Constants;
use App\Query\Status\StsTracking;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class Pipeline {

    // detail data aktifitas pemasaran
    public static function byId($id_aktifitas_pemasaran)
    {
        //code
    }

    // list data
    /*
        - current user id cabang = id_cabang
        - is_pipeline = 0
        - is_prescreening in 1,2
        - is_cutoff = 0
    */
    public static function getDataCurrent($request)
    {
        try {
            $data = Model::where(function ($query) use ($request){
                $query->where('id_user',$request->current_user->id);
                      if($request->nik) $query->where('nik',$request->nik);
            })->paginate($request->limit);
        return [
            'items' => $data->getCollection()->transform(function ($item){
                return [
                    'id' => $item->id,
                    'nama' => $item->refEfrom()->nama ? $item->refEfrom()->nama : ($item->refAktifitasPemasaran()->nama),
                    'nik' => $item->nik,
                    'nama_produk' => $item->refProduk->nama ?? null,
                    'nama_sub_produk' => $item->refSubProduk->nama ?? null,
                    'created_at' => $item->created_at,
                    'foto' => $item->foto
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


    // update data rm
     /*
        - notif email
    */
    public static function updateDataRm($request, $is_transaction = true)
    {
        if($is_transaction) DB::beginTransaction();
        try {

            //code
            if($is_transaction) DB::commit();

        } catch (\Throwable $th) {
            if($is_transaction) DB::rollBack();
            throw $th;
        }
    }

    // list data pipeline eform
    public static function getDataPipeline($request)
    {
        //code
    }

    // list informasi prescreening
    public static function getInfoPrescreening($request)
    {
         //code
    }

    // list history aktifitas
    public static function getHistoryAktifitas($request)
    {
         //code
    }

    public static function checkNasabah($nik) {
        try {
            $data = Model::where('nik', $nik)->first();
            $result = ['is_pipeline'=>Constants::IS_NOL,'is_cutoff'=>Constants::IS_NOL,'is_prescreening'=>Constants::IS_NOL];
            if($data && $data->tracking != Constants::DISBURSMENT) $result = ['is_pipeline'=>Constants::IS_NOL,'is_cutoff'=>Constants::IS_ACTIVE,'is_prescreening'=>Constants::CUT_OFF];

            return $result;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
