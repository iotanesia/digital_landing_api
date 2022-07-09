<?php

namespace App\Query\Transaksi;
use App\Models\Transaksi\Leads as Model;
use App\ApiHelper as Helper;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Constants\Constants;
use Carbon\Carbon;

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
                $query->where('is_pipeline', Constants::IS_NOL)
                      ->where('is_cutoff', Constants::IS_NOL)
                      ->where('is_prescreening', Constants::IS_ACTIVE);
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
                            'produk' => $item->refMProduk->nama ?? null,
                            'sub_produk' => $item->refMSubProduk->nama ?? null,
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
            $update->fill($request->all());
            $update->id_user = $request->current_user->id;
            $update->save();
            $update->refPipeline()->create(self::setParamsPipeline($request,$update));
            if($is_transaction) DB::commit();
            return $update;
        } catch (\Throwable $th) {
            if($is_transaction) DB::rollBack();
            throw $th;
        }
    }

    public static function setParamsPipeline($request,$data)
    {
        return [
            'nomor_aplikasi' => $data->nomor_aplikasi,
            'tracking' => 2,
            'id_tipe_calon_nasabah' => 3,
            'id_user' =>  $request->current_user->id,
            'nik' =>  $data->nik,
            'tanggal' =>  Carbon::now()->format('Y-m-d'),
            'step_verifikasi' =>  0,
        ];
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

    // update informasi nasabah dari digi data
    public static function digiData($request,$is_transaction = true)
    {
        if($is_transaction) DB::beginTransaction();
        try {
            $store = Model::find($request['id']);
            $store->nama = $store->nama ?? $request['nama'];
            $store->tempat_lahir = $store->tempat_lahir ?? $request['tempat_lahir'];
            $store->id_jenis_kelamin = $store->id_jenis_kelamin ?? $request['id_jenis_kelamin'];
            $store->tgl_lahir = $store->tgl_lahir ?? $request['tgl_lahir'];
            $store->alamat = $store->alamat ?? $request['alamat'];
            $store->id_status_perkawinan = $store->id_status_perkawinan ?? $request['id_status_perkawinan'];
            $store->id_propinsi = $store->id_propinsi ?? $request['id_propinsi'];
            $store->id_kabupaten = $store->id_kabupaten ?? $request['id_kabupaten'];
            $store->id_kecamatan = $store->id_kecamatan ?? $request['id_kecamatan'];
            $store->id_kelurahan = $store->id_kelurahan ?? $request['id_kelurahan'];
            $store->save();
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

    // fungsi prescreening
    public static function isPrescreeningSuccess($request, $is_transaction = true)
    {
        if($is_transaction) DB::beginTransaction();
        try {
            $store = Model::find($request['id']);
            $store->is_prescreening = $request['status']; // lolos
            $store->save();
            if($is_transaction) DB::commit();
        } catch (\Throwable $th) {
            if($is_transaction) DB::rollBack();
            throw $th;
        }
    }

    // fungsi prescreening
    public static function isPrescreeningFailed($request, $is_transaction = true)
    {
        if($is_transaction) DB::beginTransaction();
        try {
            $store = Model::find($request['id']);
            $store->is_prescreening = 3; // gagal
            $store->save();
            if($is_transaction) DB::commit();
        } catch (\Throwable $th) {
            if($is_transaction) DB::rollBack();
            throw $th;
        }
    }


}
