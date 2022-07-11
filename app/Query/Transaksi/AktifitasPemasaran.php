<?php

namespace App\Query\Transaksi;
use App\Models\Transaksi\AktifitasPemasaran as Model;
use App\Models\Transaksi\AktifitaPemasaranRiwayat as ModelRiwayat;
use App\Models\Transaksi\Pipeline as ModelPipeline;
use App\ApiHelper as Helper;
use App\Constants\Constants;
use App\Jobs\EformPrescreeningJobs;
use App\Jobs\MailSender;
use App\Jobs\PrescreeningJobs;
use App\Mail\EFormMail;
use App\Mail\PermohonanKredit;
use App\Query\Master\MStatusPernikahan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
class AktifitasPemasaran {

    // detail data aktifitas pemasaran
    public static function byId($id_aktifitas_pemasaran)
    {
        $data = Model::where('id', $id_aktifitas_pemasaran)->first();

        if ($data) {
            $data->jenis_kelamin = $data->refJenisKelamin->nama ?? null;
            $data->agama = $data->refAgama->nama ?? null;
            $data->status_perkawinan = $data->refStatusPernikahan->nama ?? null;
            $data->produk = $data->refProduk->nama ?? null;
            $data->sub_produk = $data->refSubProduk->nama ?? null;
            $data->cabang = $data->refCabang->nama ?? null;
            $data->status_prescreening = $data->refStsPrescreening->nama ?? null;
            $data->status_cutoff = $data->refStsCutoff->nama ?? null;
            $data->status_pipeline = $data->refStsPipeline->nama ?? null;
            $data->nama_status = $data->refStatus->nama ?? null;
            unset(
                $data->refJenisKelamin,
                $data->refStatus,
                $data->refAgama,
                $data->refStatusPernikahan,
                $data->refProduk,
                $data->refSubProduk,
                $data->refCabang,
                $data->refStsPrescreening,
                $data->refStsCutoff,
                $data->refStsPipeline,
            );

        }

        return ['items' => $data];
    }

     // detail data aktifitas pemasaran for piperline
     public static function byIdForPiperline($id_aktifitas_pemasaran)
     {
         $data = Model::where('id', $id_aktifitas_pemasaran)->first();

         if ($data) {
             $data->nama_jenis_kelamin = $data->refMJenisKelamin->nama ?? null;
             $data->nama_agama = $data->refMAgama->nama ?? null;
             $data->nama_status_perkawinan = $data->refMStatusPernikahan->nama ?? null;
             $data->nama_produk = $data->refMProduk->nama ?? null;
             $data->nama_sub_produk = $data->refMSubProduk->nama ?? null;
             $data->nama_cabang = $data->refMCabang->nama ?? null;
             $data->nama_status_prescreening = $data->refStsPrescreening->nama ?? null;
             $data->nama_status_cutoff = $data->refStsCutoff->nama ?? null;
             $data->nama_status_pipeline = $data->refStsPipeline->nama ?? null;
             $data->nama_status = $data->refStatus->nama ?? null;
             unset(
                 $data->refMJenisKelamin,
                 $data->refStatus,
                 $data->refMAgama,
                 $data->refMStatusPernikahan,
                 $data->refMProduk,
                 $data->refMSubProduk,
                 $data->refMCabang,
                 $data->refStsPrescreening,
                 $data->refStsCutoff,
                 $data->refStsPipeline,
             );

         }

         return ['items' => $data];
     }

     public static function getDataCurrentByDate($request)
     {

     }
    // list data
    /*
        - current id user = id_user
        - is_cutoff = 0
        - is_prescreening = null"
    */
    public static function getDataCurrent($request)
    {
        $filter_tanggal = Helper::filterByDate($request);
        try {
            if($request->dropdown == Constants::IS_ACTIVE) $request->limit = Model::count();
            $data = Model::where(function ($query) use ($request, $filter_tanggal){
                if($request->nomor_aplikasi) $query->where('nomor_aplikasi','ilike',"%$request->nomor_aplikasi%");
                $query->where('id_user', request()->current_user->id);
                $query->where('is_cutoff', Constants::IS_NOL);
                $query->where('is_pipeline', Constants::IS_NOL);
                $query->whereNull('is_prescreening');
                if($filter_tanggal['tanggal_mulai'] || $filter_tanggal['tanggal_akhir']) $query->whereBetween('created_at',$filter_tanggal['filter']);
            })->paginate($request->limit);
                return [
                    'items' => $data->getCollection()->transform(function ($item){
                        return [
                            'id' => $item->id,
                            'nama' => $item->nama,
                            'nik' => $item->nik,
                            'no_hp'=> $item->no_hp,
                            'nomor_aplikasi' => $item->nomor_aplikasi,
                            'cif' => $item->cif,
                            'nik' => $item->nik,
                            'foto_ktp' => $item->foto_ktp,
                            'foto_selfie' => $item->foto_selfie,
                            'created_at' => $item->created_at,
                            'updated_at' => $item->updated_at,
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

    // input data
    /* -
        - jika berminat terdapat proses prescreening
        - notif email
        return nomor aplikasi dan nik
    */
    public static function store($request,$is_transaction = true)
    {
        if($is_transaction) DB::beginTransaction();
        try {

            $require_fileds = [];
            if(!$request->nik) $require_fileds[] = 'nik';
            if(!$request->nama) $require_fileds[] = 'nama';
            if(!$request->no_hp) $require_fileds[] = 'no_hp';
            if(!$request->email) $require_fileds[] = 'email';
            if(!$request->tempat_lahir) $require_fileds[] = 'tempat_lahir';
            if(!$request->tgl_lahir) $require_fileds[] = 'tgl_lahir';
            if(!$request->npwp) $require_fileds[] = 'npwp';
            if(!$request->alamat) $require_fileds[] = 'alamat';
            if(!$request->id_jenis_kelamin) $require_fileds[] = 'jenis_kelamin';
            if(!$request->id_agama) $require_fileds[] = 'agama';
            if(!$request->id_status_perkawinan) $require_fileds[] = 'status_perkawinan';
            // if($request->id_status_perkawinan == MStatusPernikahan::getStatusMenikah(true) && !$request->nama_pasangan) $require_fileds[] = 'nama_pasangan';
            // if($request->id_status_perkawinan == MStatusPernikahan::getStatusMenikah(true) && !$request->tempat_lahir_pasangan) $require_fileds[] = 'tempat_lahir_pasangan';
            // if($request->id_status_perkawinan == MStatusPernikahan::getStatusMenikah(true) && !$request->tgl_lahir_pasangan) $require_fileds[] = 'tgl_lahir_pasangan';
            // if($request->id_status_perkawinan == MStatusPernikahan::getStatusMenikah(true) && !$request->alamat_pasangan) $require_fileds[] = 'alamat_pasangan';
            if(!$request->id_produk) $require_fileds[] = 'id_produk';
            if(!$request->id_sub_produk) $require_fileds[] = 'id_sub_produk';
            if(!$request->plafond) $require_fileds[] = 'plafond';
            if(!$request->jangka_waktu) $require_fileds[] = 'jangka_waktu';
            if(!$request->status) $require_fileds[] = 'status';
            if(count($require_fileds) > 0) throw new \Exception('This parameter must be filled '.implode(',',$require_fileds),400);

            $params = $request->all();
            $params['id_user'] = $request->current_user->id;
            $params['id_cabang'] = $request->current_user->id_cabang;

            if ((int) $request->status === 2) {
                $cekPipeline = ModelPipeline::where('nik', $request->nik)->first();
                $is_prescreening = null;
                $is_pipeline = 0;
                $is_cutoff = 0;

                if ($cekPipeline) {
                    $is_prescreening = 0;
                    if ((int) $cekPipeline->tracking !== 5) {
                        $is_prescreening = 3;
                        $is_pipeline = 0;
                        $is_cutoff = 0;
                    }
                } else {
                    $is_prescreening = 0;
                }

                $params['is_prescreening'] = $is_prescreening;
                $params['is_pipeline'] = $is_pipeline;
                $params['is_cutoff'] = $is_cutoff;
            }

            if ((int) $request->status === 1) {
                $params['is_prescreening'] = null;
                $params['is_pipeline'] = 0;
                $params['is_cutoff'] = 0;
            }

            $imageKtp = $request->foto_ktp;
            $imageSelfie = $request->foto_selfie;  // your base64 encoded
            $params['foto_ktp'] = (string) Str::uuid().'.png';
            $params['foto_selfie'] = (string) Str::uuid().'.png';
            $params['nomor_aplikasi'] = Helper::generateNoApliksi($request->current_user->id_cabang);
            $store = Model::create($params);
            if($is_transaction) DB::commit();

            if($request->status == 2) {
                $pscrng = (new PrescreeningJobs([
                    'items' => $store,
                    'modul' => 'aktifitas_pemasaran'
                ]));
                dispatch($pscrng);
            }
            // after commit process
            Storage::put($params['foto_ktp'], base64_decode($imageKtp));
            Storage::put($params['foto_selfie'], base64_decode($imageSelfie));
            $mail_data = [
                "fullname" => $store->nama,
                "nik" => $store->nik,
                "nomor_aplikasi" => $store->nomor_aplikasi,
                "reciver" =>  $store->email
            ];
            // $mail_send = (new MailSender($mail_data));
            // dispatch($mail_send);

            if ($store && ((int) $request->status === 1 || (int) $request->status === 2)) {
                $reqRiwayat = $request->all();
                $reqRiwayat['id_aktifitas_pemasaran'] = $store->id;
                $reqRiwayat['id_tujuan_pemasaran'] = $request->id_tujuan_pemasaran;
                $reqRiwayat['id_cara_pemasaran'] = $request->id_cara_pemasaran;
                $reqRiwayat['informasi_aktifitas'] =  $request->informasi_aktifitas;
                $reqRiwayat['lokasi'] = $request->lokasi;
                $store = ModelRiwayat::create($reqRiwayat);
            }

            return ["items" => $store];
        } catch (\Throwable $th) {
            if($is_transaction) DB::rollBack();
            throw $th;
        }
    }



    // update data rm
    public static function updated($request,$id,$is_transaction = true)
    {
        if($is_transaction) DB::beginTransaction();
        try {
            $update = Model::find($id);
            if(!$update) throw new \Exception("Data not found.", 400);
            $params = $request->all();
            $params['id_user'] = $request->current_user->id;
            $params['id_cabang'] = $request->current_user->id_cabang;

            if ((int) $request->status === 2) {
                $cekPipeline = ModelPipeline::where('nik', $request->nik)->first();
                $is_prescreening = null;
                $is_pipeline = 0;
                $is_cutoff = 0;

                if ($cekPipeline) {
                    $is_prescreening = 0;
                    if ((int) $cekPipeline->tracking !== 5) {
                        $is_prescreening = 3;
                        $is_pipeline = 0;
                        $is_cutoff = 0;
                    }
                } else {
                    $is_prescreening = 0;
                }

                $params['is_prescreening'] = $is_prescreening;
                $params['is_pipeline'] = $is_pipeline;
                $params['is_cutoff'] = $is_cutoff;
            }

            if ((int) $request->status === 1) {
                $params['is_prescreening'] = null;
                $params['is_pipeline'] = 0;
                $params['is_cutoff'] = 0;
            }

            if ($request->foto_ktp) {
                $imageKtp = $request->foto_ktp;  // your base64 encoded
                $params['foto_ktp'] = (string) Str::uuid().'.png';
                Storage::put($params['foto_ktp'], base64_decode($imageKtp));
                $params['foto_ktp'] = (string) Str::uuid().'.png';
            }

            if ($request->foto_selfie) {
                $imageselfie = $request->foto_selfie;  // your base64 encoded
                $params['foto_selfie'] = (string) Str::uuid().'.png';
                Storage::put($params['foto_selfie'], base64_decode($imageselfie));
                $params['foto_selfie'] = (string) Str::uuid().'.png';
            }
            $update->update($params);
            if($is_transaction) DB::commit();
            // after commit process

            $mail_data = [
                "fullname" => $update->nama,
                "nik" => $update->nik,
                "nomor_aplikasi" => $update->nomor_aplikasi,
                "reciver" =>  $update->email
            ];

            $mail_send = (new MailSender($mail_data));
            dispatch($mail_send);

            if ($update && ((int) $request->status === 1 || (int) $request->status === 2)) {
                $reqRiwayat = $request->all();
                $reqRiwayat['id_aktifitas_pemasaran'] = $id;
                $reqRiwayat['id_tujuan_pemasaran'] = $request->id_tujuan_pemasaran;
                $reqRiwayat['id_cara_pemasaran'] = $request->id_cara_pemasaran;
                $reqRiwayat['informasi_aktifitas'] =  $request->informasi_aktifitas;
                $reqRiwayat['lokasi'] = $request->lokasi;
                $store = ModelRiwayat::create($reqRiwayat);
            }

            if($is_transaction) DB::commit();
            return $update;
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
    public static function getHistoryAktifitas($request, $id)
    {
        try {
            if($request->dropdown == Constants::IS_ACTIVE) $request->limit = Model::count();
            $data = ModelRiwayat::where(function ($query) use ($id){
                $query->where('id_aktifitas_pemasaran', $id);

            })->paginate($request->limit);
                return [
                    'items' => $data->getCollection()->transform(function ($item){
                        return [
                            'id' => $item->id,
                            'id_aktifitas_pemasaran' => $item->id_aktifitas_pemasaran,
                            'aktifitas_pemasaran' => $item->refMstAktifitasPemasaran->nama ?? null,
                            'id_tujuan_pemasaran' => $item->id_tujuan_pemasaran,
                            'tujuan_pemasaran' => $item->refMstTujuanPemasaran->nama ?? null,
                            'id_cara_pemasaran' => $item->id_tujuan_pemasaran,
                            'cara_pemasaran' => $item->refMstCaraPemasaran->nama ?? null,
                            'informasi_aktifitas' => $item->informasi_aktifitas,
                            'foto' => $item->foto,
                            'lokasi' => $item->lokasi,
                            "waktu_aktifitas"=> $item->waktu_aktifitas,
                            "tanggal_aktifitas"=> $item->tanggal_aktifitas,
                            "mulai_aktifitas"=> $item->mulai_aktifitas,
                            "selesai_aktifitas"=> $item->selesai_aktifitas,
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


    public static function getAll($request)
    {

    }

    // fungsi prescreening
    public static function isPrescreeningSuccess($request, $is_transaction = true)
    {
        if($is_transaction) DB::beginTransaction();
        try {
            $store = Model::find($request['id_prescreening_modul']);
            $store->is_prescreening = $request['status']; // lolos
            $store->save();
            $store->refPipeline()->create(self::setParamsPipeline($store)); // langsung pipeline
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
            'step_verifikasi' =>  0,
        ];
    }
}
