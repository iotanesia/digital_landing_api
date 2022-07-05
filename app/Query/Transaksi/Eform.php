<?php

namespace App\Query\Transaksi;
use App\Models\Transaksi\Eform as Model;
use App\ApiHelper as Helper;
use App\Query\Master\MCabang;
use App\Services\DwhService;
use Illuminate\Support\Facades\Hash;
use App\Constants\Constants;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class Eform {


    public static function dwhMikro($request)
    {
        try {
            $data = DwhService::mikro($request->all());
            return [
                'items' => $data
            ];
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    // detail data aktifitas pemasaran
    public static function byId($id_aktifitas_pemasaran)
    {
        //code
    }

    // list data mobile form
    /*
        - current user id cabang = id_cabang
        - is_pipeline = 0
        - is_prescreening in 1,2
        - is_cutoff = 0
    */
    public static function getDataCurrent($request)
    {
        //code
        try {
            if($request->dropdown == Constants::IS_ACTIVE) $request->limit = Model::count();
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

    // input data eform web
    /* -
        - proses prescreening
        - notif email
        return nomor aplikasi dan nik
    */
    public static function storeEform($request, $is_transaction = true)
    {
        if($is_transaction) DB::beginTransaction();
        try {
            $require_fileds = [];
            $dataSend = $request->all();
            if(!$request->nik) $require_fileds[] = 'nik';
            if(!$request->email) $require_fileds[] = 'email';
            if(!$request->npwp) $require_fileds[] = 'npwp';
            if(!$request->no_hp) $require_fileds[] = 'no_hp';
            if(!$request->alamat_usaha) $require_fileds[] = 'alamat_usaha';
            if(!$request->jangka_waktu) $require_fileds[] = 'jangka_waktu';
            if(count($require_fileds) > 0) throw new \Exception('This parameter must be filled '.implode(',',$require_fileds),400);
            $dataSend['is_presecreening'] = Constants::IS_ACTIVE;
            $dataSend['is_pipeline'] = Constants::IS_NOL;
            $dataSend['is_cutoff'] = Constants::IS_NOL;
            $dataSend['platform'] = 'WEB';
            $dataSend['nomor_aplikasi'] = Helper::generateNoApliksi(MCabang::getCabangBbrv($request->id_cabang));
            $store = Model::create($dataSend);
            if($is_transaction) DB::commit();
            return ['items' => $store];

        } catch (\Throwable $th) {
            if($is_transaction) DB::rollBack();
            throw $th;
        }
    }

    // input data mobile form
    public static function storeMobileform($request, $is_transaction = true)
    {
        if($is_transaction) DB::beginTransaction();
        try {
            $require_fileds = [];
            $dataSend = $request->all();
            if(!$request->nama) $require_fileds[] = 'Nama nasabah';
            if(!$request->nik) $require_fileds[] = 'nik';
            if(!$request->email) $require_fileds[] = 'email';
            if(!$request->npwp) $require_fileds[] = 'npwp';
            if(!$request->no_hp) $require_fileds[] = 'no_hp';
            if(!$request->alamat_usaha) $require_fileds[] = 'alamat usaha';
            if(!$request->plafond) $require_fileds[] = 'plafond';
            if(!$request->jangka_waktu) $require_fileds[] = 'jangka_waktu';
            if(count($require_fileds) > 0) throw new \Exception('This parameter must be filled '.implode(',',$require_fileds),400);
            $dataSend['id_cabang'] = $request->current_user->id_cabang;
            $dataSend['id_produk'] = $request->current_user->id_produk->id_produk;
            $dataSend['nirk'] = $request->current_user->nirk;
            $dataSend['is_presecreening'] = Constants::IS_ACTIVE;
            $dataSend['is_pipeline'] = Constants::IS_NOL;
            $dataSend['is_cutoff'] = Constants::IS_NOL;
            $dataSend['platform'] = 'MOBILE';
            $dataSend['nomor_aplikasi'] = Helper::generateNoApliksi(MCabang::getCabangBbrv($request->id_cabang));
            $store = Model::create($dataSend);
            if($is_transaction) DB::commit();
            return ['items' => $store];

        } catch (\Throwable $th) {
            if($is_transaction) DB::rollBack();
            throw $th;
        }
    }

    // update data rm
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
}
