<?php

namespace App\Query\Transaksi;
use App\Models\Transaksi\Eform as Model;
use App\ApiHelper as Helper;
use App\Query\Master\MCabang;
use App\Services\DwhService;
use Illuminate\Support\Facades\Hash;
use App\Constants\Constants;
use App\Jobs\MailSender;
use App\Jobs\PrescreeningJobs;
use App\Mail\PermohonanKredit;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
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
        try {
            $data = Model::find($id_aktifitas_pemasaran);
            if(!$data) throw new \Exception("Data not found.", 400);

            return [
                'items' => [
                    'id' => $data->id,
                    'nik' => $data->nik,
                    'nama' => $data->nama,
                    'no_hp' => $data->no_hp,
                    'email' => $data->email,
                    'tempat_lahir' => $data->tempat_lahir,
                    'tanggal_lahir' => $data->tgl_lahir,
                    'nama_pasangan' => $data->nama_pasangan,
                    'tempat_lahir_pasangan' => $data->tempat_lahir_pasangan,
                    'tgl_lahir_pasangan' => $data->tgl_lahir_pasangan,
                    'alamat_detail' => $data->alamat_detail,
                    'lokasi' => $data->lokasi,
                    'lokasi_usaha' => $data->lokasi_usaha,
                    'status' => $data->status,
                    'id_jenis_produk' => $data->id_jenis_produk,
                    'id_status_perkawinan' => $data->id_status_perkawinan,
                    'id_produk' => $data->id_produk,
                    'id_sub_produk' => $data->id_sub_produk,
                    'id_cabang' => $data->id_cabang,
                    'plafon' => $data->plafon,
                    'jangka_waktu' => $data->jangka_waktu,
                    'npwp' => $data->npwp,
                    'nama_produk' => $data->refProduk->nama ?? null,
                    'nama_status_perkawinan' => $data->refStatusPerkawinan->nama ?? null,
                    'nama_cabang' => $data->refCabang->nama_cabang ?? null,
                    'nama_sub_produk' => $data->refSubProduk->nama ?? null
                ],
                'attributes' => null,
            ];
        } catch (\Throwable $th) {
            throw $th;
        }
    }


    public static function byNomorAplikasi($request)
    {
        $data = Model::where('nomor_aplikasi',$request->nomor_aplikasi)->first();
        if(!$data) throw new \Exception("Data tidak ditemukan.", 400);
        $data->status_perkawinan = $data->refStatusPerkawinan->nama ?? null;
        $data->nama_cabang = $data->refCabang->nama_cabang ?? null;
        $data->nama_agama = $data->refAgama->nama ?? null;
        $data->nama_produk = $data->refProduk->nama ?? null;
        $data->nama_sub_produk = $data->refSubProduk->nama ?? null;
        $data->status = null; // dummy
        $data->foto_ktp = null; // dummy
        $data->foto_selfi = null; // dummy
        unset(
            $data->refStatusPerkawinan,
            $data->refCabang,
            $data->refAgama,
            $data->refProduk,
            $data->refSubProduk,
            $data->is_pipeline,
            $data->is_cutoff,
            $data->is_prescreening,
            $data->id_client_api,
            $data->id,
            $data->foto,
        );
        return ['items' => $data];
    }

       // list data mobile form
    /*
        - current user id cabang = id_cabang
        - is_pipeline = 0
        - is_prescreening in 1,2
        - is_cutoff = 0
    */
    public static function getListClientData($request)
    {
        //code
        try {
            $data = Model::where(function ($query) use ($request){
                        $query->where('id_client_api',$request->client->id);
                        if($request->nama) $query->where('nama','ilike',"%$request->nama%");
                        if($request->nik) $query->where('nik',$request->nik);
                    })->paginate($request->limit);
                return [
                    'items' => $data->getCollection()->transform(function ($item){
                        return [
                            'nama' => $item->nama,
                            'nik' => $item->nik,
                            'nomor_aplikasi' => $item->nomor_aplikasi,
                            'nama_produk' => $item->refProduk->nama ?? null,
                            'nama_sub_produk' => $item->refSubProduk->nama ?? null,
                            'created_at' => $item->created_at,
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
            $data = Model::where(function ($query) use ($request){
                        $query->where('is_pipeline',Constants::IS_NOL)
                              ->whereIn('is_prescreening',[1,2])
                              ->where('is_cutoff',Constants::IS_NOL)
                              ->where('id_cabang',$request->current_user->id_cabang);
                              if($request->nama) $query->where('nama','ilike',"%$request->nama%");
                              if($request->nik) $query->where('nik',$request->nik);
                    })->paginate($request->limit);
                return [
                    'items' => $data->getCollection()->transform(function ($item){
                        return [
                            'id' => $item->id,
                            'nama' => $item->nama,
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

    // input data eform web
    /* -
        - proses prescreening
        - notif email
        return nomor aplikasi dan nik
    */
    public static function storeClientEform($request, $is_transaction = true)
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
            if(count($require_fileds) > 0) throw new \Exception('Parameter berikut harus diisi '.implode(',',$require_fileds),400);
            $dataSend['is_prescreening'] = Constants::IS_ACTIVE;
            $dataSend['is_pipeline'] = Constants::IS_NOL;
            $dataSend['is_cutoff'] = Constants::IS_NOL;
            $dataSend['platform'] = 'WEB';
            $dataSend['nomor_aplikasi'] = Helper::generateNoApliksi($request->id_cabang);
            $dataSend['id_client_api'] = $request->client->id;
            $store = Model::create($dataSend);
            if($is_transaction) DB::commit();

            // prescreening
            $pscrng = (new PrescreeningJobs([
                'items' => $store,
                'modul' => 'eform'
            ]));
            dispatch($pscrng);

            $mail_data = [
                "fullname" => $store->nama,
                "nik" => $store->nik,
                "nomor_aplikasi" => $store->nomor_aplikasi,
                "reciver" =>  $store->email
            ];
            // $mail_send = (new MailSender($mail_data));
            // dispatch($mail_send);
            return ['items' => [
                'nik' => $store->nik,
                'nomor_aplikasi' => $store->nomor_aplikasi,
            ]];

        } catch (\Throwable $th) {
            if($is_transaction) DB::rollBack();
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
            $dataSend['is_prescreening'] = Constants::IS_ACTIVE;
            $dataSend['is_pipeline'] = Constants::IS_NOL;
            $dataSend['is_cutoff'] = Constants::IS_NOL;
            $dataSend['platform'] = 'WEB';
            $dataSend['nomor_aplikasi'] = Helper::generateNoApliksi($request->id_cabang);
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
            $dataSend['id_produk'] = $request->current_user->id_produk;
            $dataSend['is_prescreening'] = Constants::IS_ACTIVE;
            $checkipeline = Pipeline::checkNasabah($request->nik);
            $dataSend['is_pipeline'] = $checkipeline['is_pipeline'];
            $dataSend['is_cutoff'] = $checkipeline['is_cutoff'];
            $dataSend['platform'] = 'MOBILE';
            $dataSend['nomor_aplikasi'] = Helper::generateNoApliksi($request->id_cabang);
            $store = Model::create($dataSend);
            if($checkipeline['is_pipeline']) $store->refPipeline->create(self::setParamsRefPipeline($request,$store));
            if($is_transaction) DB::commit();
            return ['items' => $store];

        } catch (\Throwable $th) {
            if($is_transaction) DB::rollBack();
            throw $th;
        }
    }

    public static function tracking($request)
    {
        $data = self::byNomorAplikasi($request);
        $ext = new \stdClass;
        $ext->nomor_aplikasi = $data['items']->nomor_aplikasi;
        $ext->nik = $data['items']->nik;
        $ext->plafond = $data['items']->plafond;
        $ext->npwp = $data['items']->npwp;
        $ext->email = $data['items']->email;
        $ext->no_hp = $data['items']->no_hp;
        $ext->jangka_waktu = $data['items']->jangka_waktu;
        $ext->foto_ktp = null;
        $ext->foto_selfi = null;
        if(!$data['items']) throw new \Exception('No Aplikasi dan NIK tidak sesuai');
        $ext->step = [
            [
                'label' => 'Prescreening',
                'tanggal' => Carbon::now()->format('Y-m-d'),
                'status' => 'lolos',
                'keterangan' => null,
                'step' => null
            ],
            [
                'label' => 'Analisa Kredit',
                'tanggal' => Carbon::now()->format('Y-m-d'),
                'status' => 'Sedang Diproses',
                'keterangan' => null,
                'step' => 'Verifikasi Data'
            ],
            [
                'label' => 'Approval',
                'tanggal' => null,
                'status' => null,
                'keterangan' => null,
                'step' => null
            ],
            [
                'label' => 'Cetak Dokumen',
                'tanggal' => null,
                'status' => null,
                'keterangan' => null,
                'step' => null

            ],
            [
                'label' => 'Disbursement',
                'tanggal' => null,
                'status' => null,
                'keterangan' => null,
                'step' => null

            ]
        ];

        return [
            'items' => [ $ext ]
        ];
    }

    // update data rm
    public static function updateDataRm($request, $is_transaction = true)
    {
        if($is_transaction) DB::beginTransaction();
        try {
            if(!$request->id) $require_fileds[] = 'id';
            if(!$request->nama) $require_fileds[] = 'Nama nasabah';
            if(!$request->nik) $require_fileds[] = 'nik';
            if(!$request->email) $require_fileds[] = 'email';
            if(!$request->npwp) $require_fileds[] = 'npwp';
            if(!$request->no_hp) $require_fileds[] = 'no_hp';
            if(!$request->alamat_usaha) $require_fileds[] = 'alamat usaha';
            if(!$request->plafond) $require_fileds[] = 'plafond';
            if(!$request->jangka_waktu) $require_fileds[] = 'jangka_waktu';
            if(count($require_fileds) > 0) throw new \Exception('This parameter must be filled '.implode(',',$require_fileds),400);
            $dataSend = $request->all();
            $update = Model::find($request->id);
            unset($dataSend['id']);
            foreach($dataSend as $key => $val) {
                $update->{$key} = $val;
            }
            $checkipeline = Pipeline::checkNasabah($request->nik);
            $update->is_pipeline = $checkipeline['is_pipeline'];
            $update->is_cutoff = $checkipeline['is_cutoff'];
            $update->save();
            if($checkipeline['is_pipeline']) $update->refPipeline->create(self::setParamsRefPipeline($request,$update));
            if($is_transaction) DB::commit();

        } catch (\Throwable $th) {
            if($is_transaction) DB::rollBack();
            throw $th;
        }
    }

    public static function setParamsRefPipeline($request,$data)
    {
         return [
            'nomor_aplikasi' => $data->id,
            'id_user' => $request->current_user->id,
            'tanggal' => Carbon::now()->format('Y-m-d'),
            'step_verifikasi' => Constants::PROSES_VERIFIKASI,
            'tracking'=>Constants::ANALISA_KREDIT,
         ];
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
