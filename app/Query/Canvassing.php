<?php

namespace App\Query;
use App\Models\Canvassing as Model;
use App\ApiHelper as Helper;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Constants\Group;
use App\Jobs\EformPrescreeningJobs;
use App\Mail\EFormMail;
use App\Models\Canvassing as ModelsCanvassing;
use App\Models\Eform as ModelsEform;
use Carbon\Carbon;
use App\Models\Aktifitas;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
class Canvassing {

    public static function getDataPusat($request)
    {
        try {
            $data = Model::where('platfrom','<>',Model::WEB)
            ->where('kode_cabang', (int)$request->current_user->kode_cabang)
            ->where('step',Model::STEP_PENGAJUAN_BARU)
            ->where(function ($query) use ($request){
                if($request->nama) $query->where('nama','ilike',"%$request->nama%");
                if($request->nik) $query->where('nik',$request->nik);
            })->paginate($request->limit);
                return [
                    'items' => $data->getCollection()->transform(function ($item){
                        return [
                            'id' => $item->id,
                            'nama' => $item->nama,
                            'no_hp' => $item->no_hp,
                            'alamat' => $item->alamat,
                            'nik' => $item->nik,
                            'created_at' => $item->created_at,
                            'nama_produk' => $item->refProduk->nama_produk ?? null,
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

    public static function getDataWeb($request)
    {
        try {
            $data = Model::where('platfrom', Model::WEB)
            ->where('kode_cabang', $request->current_user->kode_cabang)
            ->where('step',Model::STEP_PENGAJUAN_BARU)
            ->where(function ($query) use ($request){
                if($request->nama) $query->where('nama','ilike',"%$request->nama%");
                if($request->nik) $query->where('nik',$request->nik);
            })->paginate($request->limit);
                return [
                    'items' => $data->getCollection()->transform(function ($item){
                        return [
                            'id' => $item->id,
                            'nama' => $item->nama,
                            'no_hp' => $item->no_hp,
                            'alamat' => $item->alamat,
                            'nik' => $item->nik,
                            'created_at' => $item->created_at,
                            'nama_produk' => $item->refProduk->nama_produk ?? null,
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
                    'alamat' => $data->alamat,
                    'email' => $data->email,
                    'tempat_lahir' => $data->tempat_lahir,
                    'tgl_lahir' => $data->tgl_lahir,
                    'id_propinsi' => $data->id_propinsi,
                    'id_kabupaten' => $data->id_kabupaten,
                    'id_kecamatan' => $data->id_kecamatan,
                    'id_kelurahan' => $data->id_kelurahan,
                    'kode_pos' => $data->kode_pos,
                    'id_produk' => $data->id_produk,
                    'id_jenis_produk' => $data->id_jenis_produk,
                    'id_sub_produk' => $data->id_sub_produk,
                    'npwp' => $data->npwp,
                    'status' => $data->status,
                    'id_produk' => $data->id_produk,
                    'platfrom' => $data->platfrom,
                    'nama_pasangan' => $data->nama_pasangan,
                    'tempat_lahir_pasangan' => $data->tempat_lahir_pasangan,
                    'tanggal_lahir_pasangan' => $data->tanggal_lahir_pasangan,
                    'nama_produk' => $data->refProduk->nama_produk ?? null,
                    'nama_propinsi' => $data->refPropinsi->nama_propinsi ?? null,
                    'nama_kelurahan' => $data->refKelurahan->nama_kelurahan ?? null,
                    'nama_kabupaten' => $data->refKabupaten->nama_kabupaten ?? null,
                    'nama_kecamatan' => $data->refKecamatan->nama_kecamatan ?? null
                ],
                'attributes' => null,
            ];
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    // eform web
    public static function storeWeb($request,$is_transaction = true)
    {
        if($is_transaction) DB::beginTransaction();
        try {
            $require_fileds = [];
            if(!$request->nik) $require_fileds[] = 'nik';
            // if(!$request->nama) $require_fileds[] = 'nama';
            if(!$request->no_hp) $require_fileds[] = 'no_hp';
            // if(!$request->email) $require_fileds[] = 'email';
            if(!$request->id_propinsi) $require_fileds[] = 'id_propinsi';
            if(!$request->id_kabupaten) $require_fileds[] = 'id_kabupaten';
            if(!$request->id_kecamatan) $require_fileds[] = 'id_kecamatan';
            if(!$request->id_kelurahan) $require_fileds[] = 'id_kelurahan';
            if(!$request->kode_pos) $require_fileds[] = 'kode_pos';
            if(!$request->alamat) $require_fileds[] = 'alamat';
            if(!$request->id_produk) $require_fileds[] = 'id_produk';
            if(!$request->id_jenis_produk) $require_fileds[] = 'id_jenis_produk';
            // if(!$request->lokasi) $require_fileds[] = 'lokasi';
            if(!$request->kode_cabang) $require_fileds[] = 'kode_cabang';
            if(count($require_fileds) > 0) throw new \Exception('This parameter must be filled '.implode(',',$require_fileds),400);
            $request->informasi_aktifitas = 'e-form: Pengajuan Baru Via Web';
            if($request->nirk)  $request->informasi_aktifitas = 'e-form: Input Via Web';
            $params = $request->all();
            // default include params
            $params['step'] = ModelsCanvassing::STEP_PENGAJUAN_BARU;
            if($request->nirk) $params['step'] = ModelsCanvassing::STEP_INPUT_CANVASSING;
            $params['status'] = ModelsCanvassing::STS_WARN;
            $params['platfrom'] = ModelsCanvassing::WEB;
            $params['nomor_aplikasi'] =Helper::generateNoApliksi($request->kode_cabang);
            $image = $request->foto;  // your base64 encoded
            $params['foto'] =(string) Str::uuid().'.png';
            // $kode_cabang = MCabang::getDistanceBetweenPoints($request->lat_long_lokasi_usaha);
            // $params['kode_cabang'] = $kode_cabang;
            $store = Model::create($params);
            $params['step'] = ModelsEform::STEP_PRESCREENING;
            $params['step_proses_prescreening'] = Prescreening::PROSES;
            $params['id_canvassing'] = $store->id;
            $eform = ModelsEform::create($params);
            $params['id'] = $eform->id;
            $store->refAktifitas()->create(self::setParamsRefAktifitas($request,$store));
            if($is_transaction) DB::commit();
            Storage::put($params['foto'], base64_decode($image));
            // prescreening
            $data = [
                'items' => $eform
            ];
            $prescreening = (new EformPrescreeningJobs($data));
            dispatch($prescreening);
            $email = $store->email;
            $mail_data = [
                "fullname" => $store->nama,
                "nik" => $store->nik,
                "nomor_aplikasi" => $store->nomor_aplikasi,
            ];
            Mail::to($email)->send(new EFormMail($mail_data));
            return [
                'items' => $store
            ];
        } catch (\Throwable $th) {
            if($is_transaction) DB::rollBack();
            throw $th;
        }
    }

    public static function setParamsRefAktifitas($request,$data)
    {
         return [
            'id_canvassing' => $data->id,
            'waktu' => Carbon::now()->format('H:i'),
            'tanggal' => Carbon::now()->format('Y-m-d'),
            'nama_rm' => $data->refRm->nama ?? null,
            'lokasi' => $request->lokasi,
            'informasi_aktifitas' =>  $request->informasi_aktifitas,
            'id_tujuan_pemasaran' => $request->id_tujuan_pemasaran,
            'id_cara_pemasaran' => $request->id_cara_pemasaran,
            'foto' => $request->foto
         ];
    }

    // set rm assignmeent data
    public static function assign($request,$is_transaction = true)
    {
        if($is_transaction) DB::beginTransaction();
        try {

            $require_fileds = [];
            if(!$request->id_canvassing) $require_fileds[] = 'id_canvassing';
            if(count($require_fileds) > 0) throw new \Exception('This parameter must be filled '.implode(',',$require_fileds),400);
            $data = Model::find($request->id_canvassing);
            $request->informasi_aktifitas = $data->platfrom == 'WEB' ? 'e-form: Input Via Web' : ($data->platfrom == 'MOBILE' ? 'e-form: Input Via Mobile' : 'e-form: Input Via Data Pusat');
            $data->step = ModelsCanvassing::STEP_INPUT_CANVASSING;
            $data->nirk = $request->current_user->nirk; // assign rm
            $data->save();
            $data->refAktifitas()->create(self::setParamsRefAktifitas($request,$data));

            if($is_transaction) DB::commit();
        } catch (\Throwable $th) {
            if($is_transaction) DB::rollBack();
            throw $th;
        }
    }

     // eform web
     public static function storeMobile($request,$is_transaction = true)
     {
         if($is_transaction) DB::beginTransaction();
         try {
             $require_fileds = [];
             if(!$request->nik) $require_fileds[] = 'nik';
             if(!$request->nama) $require_fileds[] = 'nama';
             if(!$request->no_hp) $require_fileds[] = 'no_hp';
             if(!$request->email) $require_fileds[] = 'email';
             if(!$request->npwp) $require_fileds[] = 'npwp';
             if(!$request->id_produk) $require_fileds[] = 'id_produk';
             if(!$request->id_sub_produk) $require_fileds[] = 'id_sub_produk';
             if(!$request->status) $require_fileds[] = 'status';
             if(!$request->lokasi) $require_fileds[] = 'lokasi';
             if(count($require_fileds) > 0) throw new \Exception('This parameter must be filled '.implode(',',$require_fileds),400);
             $params = $request->all();
             // default include params
            //  $params['nomor_aplikasi'] = Helper::generateNoApliksi($request->current_user->kode_cabang);
             $params['kode_cabang'] = $request->current_user->kode_cabang;
             $params['nirk'] = $request->current_user->nirk;
             $params['step'] = $request->status == ModelsCanvassing::STS_HOT ? ModelsCanvassing::STEP_SUDAH_CANVASSING : ModelsCanvassing::STEP_PROSES_CANVASSING;
             $params['id_jenis_produk'] = $request->current_user->id_jenis_produk;
             $image = $request->foto;  // your base64 encoded
             $params['foto'] =(string) Str::uuid().'.png';
             if($request->id) {
                 $store = Model::where('id', $request->id)->first()->fill($params);
                 $store->save();
             } else {
                 $params['platfrom'] = ModelsCanvassing::MOBILE;
                 $store = Model::create($params);
             }
             $store->refAktifitas()->create(self::setParamsRefAktifitas($request,$store));
             if($request->status == ModelsCanvassing::STS_HOT) {
                 $params['id_canvassing'] =  $store->id;
                 $checkEform = Eform::byIdCanvassing($store->id);
                 if(!$checkEform) {
                     $params['step'] = Prescreening::BELUM_DIPROSES;
                     unset(
                        $params['status']
                     );
                     ModelsEform::create($params);
                 }
             }


             if($is_transaction) DB::commit();
             Storage::put($params['foto'], base64_decode($image));
             return [
                 'items' => $store
             ];
         } catch (\Throwable $th) {
             if($is_transaction) DB::rollBack();
             throw $th;
         }
     }

     public static function getHistoryActivities($request,$id)
     {
        try {
                $data = Model::with(['manyAktifitas' => function ($query){
                     $query->orderBy('created_at','desc');
                }])->find($id);
                if(!$data) throw new \Exception("Data not found.", 400);
                return [
                    'items' => isset($data->manyAktifitas) ? $data->manyAktifitas->map(function($item) {
                        $item->nama_tujuan_pemasaran = $item->refTujuanPemasaran->nama_tujuan_pemasaran ?? null;
                        $item->nama_cara_pemasaran = $item->refCaraPemasaran->nama_cara_pemasaran ?? null;
                        unset($item->refCaraPemasaran,$item->refTujuanPemasaran);
                        return $item;
                     }): [],
                    'attributes' => null
                ];
        } catch (\Throwable $th) {
            throw $th;
        }
     }


     // profil rm
     public static function getData($request)
     {
         try {
             $data = Model::whereIn('step', [ModelsCanvassing::STEP_INPUT_CANVASSING,ModelsCanvassing::STEP_PROSES_CANVASSING])
                     ->with(['refAktifitas' => function($query){
                        $query->orderBy('id','desc');
                     }])
                     ->where('kode_cabang', $request->current_user->kode_cabang)
                     ->where(function ($query) use ($request){
                 $query->where('nirk',$request->current_user->nirk);
                 if($request->nama) $query->where('nama','ilike',"%$request->nama%");
                 if($request->nik) $query->where('nik',$request->nik);
             })->paginate($request->limit);
                 return [
                     'items' => $data->getCollection()->transform(function ($item){
                         return [
                             'id' => $item->id,
                             'nama' => $item->nama,
                             'nik' => $item->nik,
                             'nama_produk' => $item->refProduk->nama_produk ?? null,
                             'created_at' => $item->created_at,
                             'foto' => $item->foto,
                             'aktifitas' => $item->refAktifitas->informasi_aktifitas ?? null
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

}
