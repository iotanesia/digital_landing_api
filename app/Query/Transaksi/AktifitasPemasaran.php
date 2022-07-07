<?php

namespace App\Query\Transaksi;
use App\Models\Transaksi\AktifitasPemasaran as Model;
use App\Models\Transaksi\AktifitaPemasaranRiwayat as ModelRiwayat;
use App\Models\Transaksi\Pipeline as ModelPipeline;
use App\ApiHelper as Helper;
use App\Constants\Constants;
use App\Jobs\EformPrescreeningJobs;
use App\Jobs\MailSender;
use App\Mail\EFormMail;
use App\Mail\PermohonanKredit;
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
            $data->status_prescreening = $data->refStsPrescreening->nama ?? null;
            $data->status_cutoff = $data->refStsCutoff->nama ?? null;
            $data->status_pipeline = $data->refStsPipeline->nama ?? null;
            unset($data->refStsPrescreening); 
            unset($data->refStsCutoff);  
            unset($data->refStsPipeline); 
        }
         
        return ['items' => $data];
    }

    // list data
    /*
        - current id user = id_user
        - is_cutoff = 0
        - is_prescreening = null"
    */
    public static function getDataCurrent($request)
    {
        try {
            if($request->dropdown == Constants::IS_ACTIVE) $request->limit = Model::count();
            $data = Model::where(function ($query) use ($request){
                if($request->nomor_aplikasi) $query->where('nomor_aplikasi','ilike',"%$request->nomor_aplikasi%");
                $query->where('id_user', request()->current_user->id);
                $query->where('is_cutoff', Constants::IS_NOL);
                $query->whereNull('is_prescreening');
                
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
            if(!$request->nomor_aplikasi) $require_fileds[] = 'nomor_aplikasi';
            if(!$request->nik) $require_fileds[] = 'nik';
            if(!$request->cif) $require_fileds[] = 'cif';
            if(!$request->nama) $require_fileds[] = 'nama';
            if(!$request->no_hp) $require_fileds[] = 'no_hp';
            if(!$request->email) $require_fileds[] = 'email';
            if(!$request->tempat_lahir) $require_fileds[] = 'tempat_lahir';
            if(!$request->tgl_lahir) $require_fileds[] = 'tgl_lahir';
            if(!$request->npwp) $require_fileds[] = 'npwp';
            if(!$request->alamat) $require_fileds[] = 'alamat';
            if(!$request->id_jenis_kelamin) $require_fileds[] = 'id_jenis_kelamin';
            if(!$request->id_agama) $require_fileds[] = 'id_agama';
            if(!$request->id_status_perkawinan) $require_fileds[] = 'id_status_perkawinan';
            if(!$request->nama_pasangan) $require_fileds[] = 'nama_pasangan';
            if(!$request->tempat_lahir_pasangan) $require_fileds[] = 'tempat_lahir_pasangan';
            if(!$request->tgl_lahir_pasangan) $require_fileds[] = 'tgl_lahir_pasangan';
            if(!$request->alamat_pasangan) $require_fileds[] = 'alamat_pasangan';
            if(!$request->id_produk) $require_fileds[] = 'id_produk';
            if(!$request->id_sub_produk) $require_fileds[] = 'id_sub_produk';
            if(!$request->plafond) $require_fileds[] = 'plafond';
            if(!$request->jangka_waktu) $require_fileds[] = 'jangka_waktu';
            if(!$request->id_cabang) $require_fileds[] = 'id_cabang';
            if(!$request->status) $require_fileds[] = 'status';

            if($request->is_cutoff) $require_fileds[] = 'is_cutoff';
            if($request->is_pipeline) $require_fileds[] = 'is_pipeline';
            if($request->is_prescreening) $require_fileds[] = 'is_prescreening';
       
            if(count($require_fileds) > 0) throw new \Exception('This parameter must be filled '.implode(',',$require_fileds),400);
           
            $params = $request->all();
            $params['id_user'] = request()->current_user->id;

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

            $image = $request->foto;  // your base64 encoded
            $params['foto'] = (string) Str::uuid().'.png';
            $store = Model::create($params);
            if($is_transaction) DB::commit();
            // after commit process
            Storage::put($params['foto'], base64_decode($image));
            $mail_data = [
                "fullname" => $store->nama,
                "nik" => $store->nik,
                "nomor_aplikasi" => $store->nomor_aplikasi,
                "reciver" =>  $store->email
            ];
            $mail_send = (new MailSender($mail_data));
            dispatch($mail_send);

            if ($store && ((int) $request->status === 1 || (int) $request->status === 2)) {
                $reqRiwayat = $request->all();
                $reqRiwayat['id_aktifitas_pemasaran'] = $store->id;
                $reqRiwayat['id_tujuan_pemasaran'] = $request->id_tujuan_pemasaran;
                $reqRiwayat['id_cara_pemasaran'] = $request->id_cara_pemasaran;
                $reqRiwayat['informasi_aktifitas'] =  $request->informasi_aktifitas;
                $reqRiwayat['foto'] = $store->foto;
                $reqRiwayat['lokasi'] = $request->lokasi;
                $store = ModelRiwayat::create($reqRiwayat);
            }

            return $store;
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
            $params['id_user'] = request()->current_user->id;

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

            $image = $request->foto;  // your base64 encoded
            $params['foto'] = (string) Str::uuid().'.png';
            $update->update($params);
            if($is_transaction) DB::commit();
            // after commit process
            Storage::put($params['foto'], base64_decode($image));
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
                $reqRiwayat['foto'] = $params['foto'];
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
    public static function getHistoryAktifitas($request)
    {
         //code
    }

    public static function getAll($request)
    {
       
    }
}
