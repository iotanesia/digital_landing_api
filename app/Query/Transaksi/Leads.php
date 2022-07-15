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
            $data->nama_status_perkawinan = $data->refStatusPernikahan->nama ?? null;
            $data->nama_jenis_kelamin = $data->refJenisKelamin->nama ?? null;
            $data->nama_agama = $data->refAgama->nama ?? null;
            $data->nama_produk = $data->refProduk->nama ?? null;
            $data->nama_sub_produk = $data->refSubProduk->nama ?? null;
            $data->nama_cabang = $data->refCabang->nama ?? null;
            $data->nama_status = $data->refStatus->nama ?? null;
            $data->status_prescreening = $data->refStsPrescreening->nama ?? null;
            $data->status_cutoff = $data->refStsCutoff->nama ?? null;
            $data->status_pipeline = $data->refStsPipeline->nama ?? null;
            unset($data->refJenisKelamin);
            unset($data->refAgama);
            unset($data->refStatusPernikahan);
            unset($data->refProduk);
            unset($data->refSubProduk);
            unset($data->refCabang);
            unset($data->refStsPrescreening);
            unset($data->refStsCutoff);
            unset($data->refStsPipeline);
        }

        return ['items' => $data];
    }

    public static function getDataCurrentByDate($request)
    {
       $filter_tanggal = Helper::filterByDateDefaultWeek($request);
       try {
           if($request->dropdown == Constants::IS_ACTIVE) $request->limit = Model::count();
           $data = Model::where(function ($query) use ($request, $filter_tanggal){
               $query->where('is_pipeline', Constants::IS_NOL)
               ->where('is_cutoff', Constants::IS_NOL)
               ->where('is_prescreening', Constants::IS_ACTIVE)
               ->whereBetween('leads.created_at',$filter_tanggal['filter']);
           })
           ->leftJoin('master.produk as produk', 'produk.id', 'id_produk')
           ->leftJoin('master.sub_produk as sub_produk', 'sub_produk.id', 'id_sub_produk')
           ->select('leads.id','leads.nama','nik','no_hp', 'cif','produk.nama as nama_produk','sub_produk.nama as nama_sub_produk',
                    'leads.foto', DB::raw('DATE(leads.created_at) as date'))
           ->orderBy('date','desc')
           ->get()
           ->groupBy('date');

           $itemsArr = [];
           foreach($data as $key => $group) {
               $itemsArr[] = ["date" => $key, "data" => $group];
           }

           return ['items' => $itemsArr];

       } catch (\Throwable $th) {
           throw $th;
       }
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
        $filter_tanggal = Helper::filterByDate($request);
        try {
            if($request->dropdown == Constants::IS_ACTIVE) $request->limit = Model::count();
            $data = Model::where(function ($query) use ($request,$filter_tanggal){
                $query->where('is_pipeline', Constants::IS_NOL)
                      ->where('is_cutoff', Constants::IS_NOL)
                      ->where('is_prescreening', Constants::IS_ACTIVE);
                      if($filter_tanggal['tanggal_mulai'] || $filter_tanggal['tanggal_akhir']) $query->whereBetween('created_at',$filter_tanggal['filter']);
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
                            'produk' => $item->refProduk->nama ?? null,
                            'sub_produk' => $item->refSubProduk->nama ?? null,
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
            $update->is_pipeline = Constants::IS_ACTIVE;
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
            'id_tipe_calon_nasabah' => Constants::TCN_LEAD,
            'id_user' =>  $request->current_user->id,
            'nik' =>  $data->nik,
            'tanggal' =>  Carbon::now()->format('Y-m-d'),
            'step_analisa_kredit' => Constants::IS_INACTIVE,
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
            $store = Model::find($request['id_prescreening_modul']);
            if(!$store) return false;
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
            $store = Model::find($request['id_prescreening_modul']);
            if(!$store) return false;
            $store->is_prescreening = 3; // gagal
            $store->save();
            if($is_transaction) DB::commit();
        } catch (\Throwable $th) {
            if($is_transaction) DB::rollBack();
            throw $th;
        }
    }


}
