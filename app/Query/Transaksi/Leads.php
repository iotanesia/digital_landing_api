<?php

namespace App\Query\Transaksi;
use App\Models\Transaksi\Leads as Model;
use App\ApiHelper as Helper;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Constants\Constants;

class Leads {

    // detail data aktifitas pemasaran
    public static function byId($id)
    {
        $data = Model::where('id', $id)->first();

        if ($data) {
            $data->jenis_kelamin = $data->refMJenisKelamin->nama ?? null;
            $data->agama = $data->refMAgama->nama ?? null;
            $data->status_perkawinan = $data->refMStatusPernikahan->nama ?? null;
            $data->produk = $data->refMProduk->nama ?? null;
            $data->sub_produk = $data->refMSubProduk->nama ?? null;
            $data->cabang = $data->refMCabang->nama ?? null;
            $data->status_prescreening = $data->refStsPrescreening->nama ?? null;
            $data->status_cutoff = $data->refStsCutoff->nama ?? null;
            $data->status_pipeline = $data->refStsPipeline->nama ?? null;
            unset($data->refMJenisKelamin); 
            unset($data->refMAgama);  
            unset($data->refMStatusPernikahan);
            unset($data->refMProduk); 
            unset($data->refMSubProduk);
            unset($data->refMCabang);  
            unset($data->refStsPrescreening); 
            unset($data->refStsCutoff);  
            unset($data->refStsPipeline); 
        }
         
        return ['items' => $data];
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
            if($request->dropdown == Constants::IS_ACTIVE) $request->limit = Model::count();
            $data = Model::where(function ($query) use ($request){
            })->paginate($request->limit);
                return [
                    'items' => $data->getCollection()->transform(function ($item){
                        return [
                            'id' => $item->id,
                            'nama' => $item->nama,
                            'nik' => $item->no_hp,
                            'cif' => $item->cif,
                            'nik' => $item->nik,
                            'foto' => $item->foto,
                            'created_at' => $item->created_at,
                        ];
                    }),
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
}
