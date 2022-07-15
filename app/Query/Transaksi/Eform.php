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
use App\Query\Master\MSubProduk;
use App\Sp\SpListPipeline;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
    public static function byId($id)
    {
        try {
            $data = Model::find($id);
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
                    'alamat' => $data->alamat,
                    'lokasi' => $data->lokasi,
                    'status' => $data->status,
                    'id_jenis_produk' => $data->id_jenis_produk,
                    'id_status_perkawinan' => $data->id_status_perkawinan,
                    'id_produk' => $data->id_produk,
                    'id_produk' => $data->id_produk,
                    'id_sub_produk' => $data->id_sub_produk,
                    'id_cabang' => $data->id_cabang,
                    'id_agama' => $data->id_agama,
                    'id_jenis_kelamin' => $data->id_jenis_kelamin,
                    'plafond' => $data->plafond,
                    'alamat_usaha' => $data->alamat_usaha,
                    'jangka_waktu' => $data->jangka_waktu,
                    'npwp' => $data->npwp,
                    'foto_ktp' => $data->foto_ktp,
                    'foto_selfie' => $data->foto_selfie,
                    'nama_produk' => $data->refProduk->nama ?? null,
                    'nama_status_perkawinan' => $data->refStatusPerkawinan->nama ?? null,
                    'nama_cabang' => $data->refCabang->nama_cabang ?? null,
                    'lat' => $data->refCabang->lat ?? null,
                    'lng' => $data->refCabang->lng ?? null,
                    'nama_sub_produk' => $data->refSubProduk->nama ?? null,
                    'nama_jenis_kelamin' => $data->refJenisKelamin->nama ?? null,
                    'nama_agama' => $data->refAgama->nama ?? null,
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
        $data->nama_produk = $data->refProduk->nama ?? null;
        $data->nama_sub_produk = $data->refSubProduk->nama ?? null;
        $data->jenis_kelamin = $data->refJenisKelamin->nama ?? null;
        $data->status = null; // dummy
        $data->foto_ktp = $data->foto_ktp;
        $data->foto_selfie = $data->foto_selfie;
        $data->profil_usaha = $data->manyProfilUsaha->map(function ($item){
            return [
                'id_perizinan' => $item->id_perizinan,
                'npwp' => $item->npwp,
                'nama_usaha' => $item->nama_usaha,
                'profil_usaha' => $item->profil_usaha,
                'alamat_usaha' => $item->alamat_usaha,
                'mulai_operasi' => $item->mulai_operasi,
                'lat' => $item->lat,
                'lng' => $item->lng,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,
            ];
        }); // dummy
        unset(
            $data->refStatusPerkawinan,
            $data->refCabang,
            $data->refAgama,
            $data->cif,
            $data->platform,
            $data->id_agama,
            $data->refProduk,
            $data->refSubProduk,
            $data->is_pipeline,
            $data->is_cutoff,
            $data->is_prescreening,
            $data->id_client_api,
            $data->id,
            $data->foto,
            $data->manyProfilUsaha,
            $data->refJenisKelamin
        );
        return ['items' => $data];
    }

    public static function byNomorAplikasiNik($request)
    {
        $data = Model::where('nomor_aplikasi',$request->nomor_aplikasi)
        ->where('nik',$request->nik)
        ->first();
        if(!$data) throw new \Exception("Data tidak ditemukan.", 400);
        $data->status_perkawinan = $data->refStatusPerkawinan->nama ?? null;
        $data->nama_cabang = $data->refCabang->nama_cabang ?? null;
        $data->nama_produk = $data->refProduk->nama ?? null;
        $data->nama_sub_produk = $data->refSubProduk->nama ?? null;
        $data->jenis_kelamin = $data->refJenisKelamin->nama ?? null;
        $data->profil_usaha = $data->manyProfilUsaha->map(function ($item){
            return [
                'id_perizinan' => $item->id_perizinan,
                'npwp' => $item->npwp,
                'nama_usaha' => $item->nama_usaha,
                'profil_usaha' => $item->profil_usaha,
                'alamat_usaha' => $item->alamat_usaha,
                'mulai_operasi' => $item->mulai_operasi,
                'lat' => $item->lat,
                'lng' => $item->lng,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,
            ];
        }); // dummy
        unset(
            $data->refStsPrescreening,
            $data->refStatusPerkawinan,
            $data->refCabang,
            $data->refAgama,
            $data->cif,
            $data->platform,
            $data->id_agama,
            $data->refProduk,
            $data->refSubProduk,
            $data->is_pipeline,
            $data->is_cutoff,
            $data->id_client_api,
            $data->id,
            $data->foto,
            $data->manyProfilUsaha,
            $data->refJenisKelamin,
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
        $filter_tanggal = Helper::filterByDate($request);
        try {
            $data = Model::where(function ($query) use ($request, $filter_tanggal){
                        $query->where('id_client_api',$request->client->id);
                        if($request->nama) $query->where('nama','ilike',"%$request->nama%");
                        if($request->nik) $query->where('nik',$request->nik);
                        // if($request->status) $query->where('status',$request->status);
                        if($filter_tanggal['tanggal_mulai'] || $filter_tanggal['tanggal_akhir']) $query->whereBetween('created_at',$filter_tanggal['filter']);
                    })->orderBy('id','desc')->paginate($request->limit);
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
        $filter_tanggal = Helper::filterByDate($request);
        try {
            $data = Model::where(function ($query) use ($request,$filter_tanggal){
                        $query->where('is_pipeline',Constants::IS_NOL)
                              ->where('is_prescreening',1)
                              ->where('is_cutoff',Constants::IS_NOL)
                              ->where('id_cabang',$request->current_user->id_cabang);
                              if($request->nama) $query->where('nama','ilike',"%$request->nama%");
                              if($request->nik) $query->where('nik',$request->nik);
                              if($filter_tanggal['tanggal_mulai'] || $filter_tanggal['tanggal_akhir']) $query->whereBetween('created_at',$filter_tanggal['filter']);
                        if($request->kueri) $query->where(function ($query) use ($request){
                             $query->where('nama','ilike',"%$request->kueri%");
                             $query->orWhere('nomor_aplikasi','ilike',"%$request->kueri%");
                             $query->orWhere('nik','ilike',"%$request->kueri%");
                             $query->orWhere('no_hp','ilike',"%$request->kueri%");
                        });
                    })->paginate($request->limit);
                return [
                    'items' => $data->getCollection()->transform(function ($item){
                        return [
                            'id' => $item->id,
                            'nama' => $item->nama,
                            'nik' => $item->nik,
                            'no_hp' => $item->no_hp,
                            'nama_produk' => $item->refProduk->nama ?? null,
                            'nama_sub_produk' => $item->refSubProduk->nama ?? null,
                            'created_at' => $item->created_at,
                            'foto' => $item->id_jenis_kelamin == 2 ? 'female.png' : 'male.png'
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
            if(!$request->alamat) $require_fileds[] = 'alamat';
            if(!$request->jangka_waktu) $require_fileds[] = 'jangka_waktu';
            if(!$request->profil_usaha) $require_fileds[] = 'profil_usaha';
            if(!$request->foto_ktp) $require_fileds[] = 'foto_ktp';
            if(!$request->foto_selfie) $require_fileds[] = 'foto_selfie';
            if(count($require_fileds) > 0) throw new \Exception('Parameter berikut harus diisi '.implode(',',$require_fileds),400);
            $image = $request->foto_ktp;  // your base64 encoded
            $image_selfie = $request->foto_selfie;  // your base64 encoded

            $dataSend['is_prescreening'] = Constants::IS_ACTIVE;
            $dataSend['is_pipeline'] = Constants::IS_NOL;
            $dataSend['is_cutoff'] = Constants::IS_NOL;
            $dataSend['platform'] = 'WEB';
            $dataSend['nomor_aplikasi'] = Helper::generateNoApliksi($request->id_cabang);
            $dataSend['id_client_api'] = $request->client->id;
            $dataSend['foto_ktp'] =(string) Str::uuid().'.png';
            $dataSend['foto_selfie'] =(string) Str::uuid().'.png';
            $store = Model::create($dataSend);
            if($request->profil_usaha) $store->manyProfilUsaha()->createMany(self::setParamsProfilUsaha($dataSend,$store->id));
            if($is_transaction) DB::commit();
            // after commit process
            Storage::put($dataSend['foto_ktp'], base64_decode($image));
            Storage::put($dataSend['foto_selfie'], base64_decode($image_selfie));
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
            $mail_send = (new MailSender($mail_data));
            dispatch($mail_send);
            return ['items' => [
                'nik' => $store->nik,
                'nomor_aplikasi' => $store->nomor_aplikasi,
            ]];

        } catch (\Throwable $th) {
            if($is_transaction) DB::rollBack();
            throw $th;
        }
    }

    public static function setParamsProfilUsaha($request,$id_eform)
    {
         return array_map(function ($item) use ($id_eform){
            $item['id_eform'] = $id_eform;
            return $item;
         },$request['profil_usaha']);
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
            $store = $request->all();
            if(!$request->nik) $require_fileds[] = 'nik';
            if(!$request->email) $require_fileds[] = 'email';
            if(!$request->npwp) $require_fileds[] = 'npwp';
            if(!$request->no_hp) $require_fileds[] = 'no_hp';
            if(!$request->alamat_usaha) $require_fileds[] = 'alamat_usaha';
            if(!$request->jangka_waktu) $require_fileds[] = 'jangka_waktu';
            if(!$request->foto_ktp) $require_fileds[] = 'Foto Ktp';
            if(!$request->foto_selfie) $require_fileds[] = 'Foto selfie';
            if(count($require_fileds) > 0) throw new \Exception('This parameter must be filled '.implode(',',$require_fileds),400);
            $checkipeline = Pipeline::checkNasabah($request->nik);
            $store['is_prescreening'] = $checkipeline['is_prescreening'];
            $store['is_pipeline'] = $checkipeline['is_pipeline'];
            $store['is_cutoff'] = $checkipeline['is_cutoff'];
            $store['platform'] = 'WEB';
            $store['nomor_aplikasi'] = Helper::generateNoApliksi($request->id_cabang);
            $image = $request->foto_ktp;  // your base64 encoded
            $image_selfie = $request->foto_selfie;  // your base64 encoded
            // dd(base64_decode($image_selfie));
            $store['foto_ktp'] =(string) Str::uuid().'.png';
            $store['foto_selfie'] =(string) Str::uuid().'.png';
            $store = Model::create($store);
            if($is_transaction) DB::commit();
            // after commit process
            Storage::put($store['foto_ktp'], base64_decode($image));
            Storage::put($store['foto_selfie'], base64_decode($image_selfie));
            // prescreening
            if($checkipeline['is_prescreening'] == constants::IS_NOL && $checkipeline['is_pipeline'] == constants::IS_NOL && $checkipeline['is_cutoff'] == constants::IS_NOL) {
                $pscrng = (new PrescreeningJobs([
                    'items' => $store,
                    'modul' => 'eform'
                ]));
                dispatch($pscrng);
            }
            $mail_data = [
                "fullname" => $store->nama,
                "nik" => $store->nik,
                "nomor_aplikasi" => $store->nomor_aplikasi,
                "reciver" =>  $store->email
            ];
            $mail_send = (new MailSender($mail_data));
            dispatch($mail_send);
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
            $store = $request->all();
            if(!$request->nama) $require_fileds[] = 'Nama nasabah';
            if(!$request->nik) $require_fileds[] = 'nik';
            if(!$request->email) $require_fileds[] = 'email';
            if(!$request->npwp) $require_fileds[] = 'npwp';
            if(!$request->no_hp) $require_fileds[] = 'no_hp';
            if(!$request->alamat_usaha) $require_fileds[] = 'alamat usaha';
            if(!$request->plafond) $require_fileds[] = 'plafond';
            if(!$request->jangka_waktu) $require_fileds[] = 'jangka_waktu';
            if(count($require_fileds) > 0) throw new \Exception('This parameter must be filled '.implode(',',$require_fileds),400);
            $store['id_cabang'] = $request->current_user->id_cabang;
            $store['id_produk'] = MSubProduk::getIdProduk($request->id_sub_produk);
            // $store['id_sub_produk'] = 3;
            // $store['id_produk'] = 1;
            $checkipeline = Pipeline::checkNasabah($request->nik);
            $store['is_prescreening'] = $checkipeline['is_prescreening'];
            $store['is_pipeline'] = $checkipeline['is_pipeline'];
            $store['is_cutoff'] = $checkipeline['is_cutoff'];
            $store['id_user'] = $request->current_user->id;
            $store['platform'] = 'MOBILE';
            $store['nomor_aplikasi'] = Helper::generateNoApliksi($request->id_cabang);
            $image = $request->foto_ktp;  // your base64 encoded
            $image_selfie = $request->foto_selfie;  // your base64 encoded
            $store['foto_ktp'] =(string) Str::uuid().'.png';
            $store['foto_selfie'] =(string) Str::uuid().'.png';
            $store = Model::create($store);
            // if($checkipeline['is_pipeline']) $store->refPipeline()->create(self::setParamsRefPipeline($request,$store));
            if($is_transaction) DB::commit();
            // after commit process
            Storage::put($store['foto_ktp'], base64_decode($image));
            Storage::put($store['foto_selfie'], base64_decode($image_selfie));

            // prescreening
            if($checkipeline['is_prescreening'] == constants::IS_NOL && $checkipeline['is_pipeline'] == constants::IS_NOL && $checkipeline['is_cutoff'] == constants::IS_NOL) {
                $pscrng = (new PrescreeningJobs([
                    'items' => $store,
                    'modul' => 'eform'
                ]));
                dispatch($pscrng);
            }
            $mail_data = [
                "fullname" => $store->nama,
                "nik" => $store->nik,
                "nomor_aplikasi" => $store->nomor_aplikasi,
                "reciver" =>  $store->email
            ];
            $mail_send = (new MailSender($mail_data));
            dispatch($mail_send);


            return ['items' => $store];

        } catch (\Throwable $th) {
            if($is_transaction) DB::rollBack();
            throw $th;
        }
    }

    public static function tracking($request)
    {
        $data = Model::where('nomor_aplikasi',$request->nomor_aplikasi)
        ->where('nik',$request->nik)
        ->first();
        if(!$data) throw new \Exception("Data tidak ditemukan.", 400);
        $ext = new \stdClass;
        $ext->nomor_aplikasi = $data->nomor_aplikasi;
        $ext->nik = $data->nik;
        $ext->plafond = $data->plafond;
        $ext->npwp = $data->npwp;
        $ext->email = $data->email;
        $ext->no_hp = $data->no_hp;
        $ext->jangka_waktu = $data->jangka_waktu;
        $ext->foto_ktp = $data->foto_ktp;
        $ext->foto_selfie = $data->foto_selfie;
        if(!$data) throw new \Exception('No Aplikasi dan NIK tidak sesuai');
        $ext->step = [
            [
                'kode' => '01',
                'label' => 'Prescreening',
                'tanggal' => Carbon::parse($data->created_at)->format('Y-m-d'),
                'status' => $data->refStsPrescreening->nama ?? null,
                'id_status' => $data->is_prescreening ?? null,
                'keterangan' => null,
                'step' => null
            ],
            [
                'kode' => '02',
                'label' => 'Analisa Kredit',
                'tanggal' => $data->is_prescreening == Constants::CUT_OFF ? null : Carbon::now()->format('Y-m-d'),
                'status' =>  $data->is_prescreening == Constants::CUT_OFF ? null : 'Sedang Diproses',
                'id_status' => 0,
                'keterangan' => null,
                'step' => $data->is_prescreening == Constants::CUT_OFF ? null : 'Verifikasi Data'
            ],
            [
                'kode' => '03',
                'label' => 'Approval',
                'tanggal' => null,
                'status' => null,
                'keterangan' => null,
                'step' => null
            ],
            [
                'kode' => '04',
                'label' => 'Cetak Dokumen',
                'tanggal' => null,
                'status' => null,
                'keterangan' => null,
                'step' => null

            ],
            [
                'kode' => '04',
                'label' => 'Disbursement',
                'tanggal' => null,
                'status' => null,
                'keterangan' => null,
                'step' => null

            ]
        ];

        return [
            'items' => $ext
        ];
    }

    // update data rm
    public static function updateDataRm($request, $id, $is_transaction = true)
    {
        if($is_transaction) DB::beginTransaction();
        try {
            $require_fileds = [];
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
            $update = Model::find($id);
            unset($dataSend['id']);
            foreach($dataSend as $key => $val) {
                $update->{$key} = $val;
            }
            $update->is_pipeline = Constants::IS_ACTIVE;
            $update->id_user = $request->current_user->id;
            if($request->foto_ktp) $update->foto_ktp = (string) Str::uuid().'.png';
            if($request->foto_selfie) $update->foto_selfie = (string) Str::uuid().'.png';
            $update->save();
            if($is_transaction) DB::commit();
            if($update->is_prescreening) $update->refPipeline()->create(self::setParamsRefPipeline($request,$update));
            if($request->foto_ktp) Storage::put($update->foto_ktp, base64_decode($request->foto_ktp));
            if($request->foto_selfie) Storage::put($update->foto_selfie, base64_decode($request->foto_selfie));
            return ['items' => $update];

        } catch (\Throwable $th) {
            if($is_transaction) DB::rollBack();

            throw $th;
        }
    }

    public static function setParamsRefPipeline($request,$data)
    {
         return [
            'nomor_aplikasi' => $data->nomor_aplikasi,
            'id_user' => $request->current_user->id,
            'nik' => $data->nik,
            'tanggal' => Carbon::now()->format('Y-m-d'),
            'step_analisa_kredit' => Constants::PROSES_VERIFIKASI,
            'tracking'=>Constants::ANALISA_KREDIT,
            'id_tipe_calon_nasabah'=>Constants::TCN_EFORM,
         ];
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

    // fungsi prescreening
    public static function isPrescreeningSuccess($request, $is_transaction = true)
    {
        if($is_transaction) DB::beginTransaction();
        try {
            $store = Model::find($request['id_prescreening_modul']);
            if(!$store) return false;
            $store->is_prescreening = $request['status']; // lolos
            if(in_array($store->platform,['MOBILE']))  $store->is_pipeline = 1;
            $store->save();
            if(in_array($store->platform,['MOBILE'])) $store->refPipeline()->create(self::setParamsPipeline($store));
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

    // fungsi prescreening
    // start pipeline
    public static function setParamsPipeline($data)
    {
        return [
            'nomor_aplikasi' => $data->nomor_aplikasi,
            'tracking' => 2,
            'id_tipe_calon_nasabah' => 2,
            'id_user' =>  $data->id_user,
            'nik' =>  $data->nik,
            'tanggal' =>  Carbon::now()->format('Y-m-d'),
            'step_analisa_kredit' =>  0,
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
