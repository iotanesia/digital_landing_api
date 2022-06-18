<?php

namespace App\Query;
use App\Models\Canvassing as Model;
use App\ApiHelper as Helper;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Constants\Group;
use App\Models\Canvassing as ModelsCanvassing;
use App\Models\Eform as ModelsEform;
use Carbon\Carbon;
use App\Models\Aktifitas;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
class Canvassing {

    public static function getDataPusat($request)
    {
        try {
            $data = Model::where('platfrom','<>',Model::WEB)->where(function ($query) use ($request){
                $query->where('step',Model::STEP_PENGAJUAN_BARU);
                $query->whereNull('nirk');
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
            ->where(function ($query) use ($request){
                $query->where('step',Model::STEP_PENGAJUAN_BARU);
                $query->whereNull('nirk');
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
                    'nik' => $data->nik,
                    'nama' => $data->nama,
                    'no_hp' => $data->no_hp,
                    'alamat' => $data->alamat,
                    'status' => $data->status,
                    'id_produk' => $data->id_produk,
                    'nama_produk' => $data->refProduk->nama_produk ?? null,
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
            if(!$request->nama) $require_fileds[] = 'nama';
            if(!$request->no_hp) $require_fileds[] = 'no_hp';
            if(!$request->email) $require_fileds[] = 'email';
            if(!$request->id_propinsi) $require_fileds[] = 'id_propinsi';
            if(!$request->id_kabupaten) $require_fileds[] = 'id_kabupaten';
            if(!$request->id_kecamatan) $require_fileds[] = 'id_kecamatan';
            if(!$request->id_kelurahan) $require_fileds[] = 'id_kelurahan';
            if(!$request->kode_pos) $require_fileds[] = 'kode_pos';
            if(!$request->alamat) $require_fileds[] = 'alamat';
            if(!$request->id_produk) $require_fileds[] = 'id_produk';
            if(!$request->id_sub_produk) $require_fileds[] = 'id_sub_produk';
            if(!$request->lokasi) $require_fileds[] = 'lokasi';
            if(!$request->kode_cabang) $require_fileds[] = 'kode_cabang';
            if(count($require_fileds) > 0) throw new \Exception('This parameter must be filled '.implode(',',$require_fileds),400);
            $request->informasi_aktifitas = 'e-form: Pengajuan Baru Via Web';
            if($request->nirk)  $request->informasi_aktifitas = 'e-form: Input Via Web';
            $params = $request->all();
            // default include params
            $params['status'] = ModelsCanvassing::STS_HOT;
            $params['step'] = ModelsCanvassing::STEP_PROSES_CANVASSING;
            $params['platfrom'] = ModelsCanvassing::WEB;
            $params['nomor_aplikasi'] = mt_rand(10000000,99999999);
            $image = $request->foto;  // your base64 encoded
            $request->foto =(string) Str::uuid().'.png';

            $store = Model::create($params);
            $params['step'] = ModelsEform::STEP_INPUT_EFORM;
            $params['id_canvassing'] = $store->id;
            $storeEform = ModelsEform::create($params);
            $store->refAktifitas()->create(self::setParamsRefAktifitas($request,$store));
            if($is_transaction) DB::commit();
            Storage::put($request->foto, base64_decode($image));
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
            $request->informasi_aktifitas = 'e-form: Input Via Web';
            $data = Model::find($request->id_canvassing);
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
            //  $params['status'] = ModelsCanvassing::STS_COLD;
            //  $params['step'] = ModelsCanvassing::STEP_PROSES_CANVASSING;
             $params['nomor_aplikasi'] = mt_rand(10000000,99999999);
             $params['kode_cabang'] = $request->current_user->kode_cabang;
             $params['nirk'] = $request->current_user->nirk;
             $image = $request->foto;  // your base64 encoded
             $request->foto =(string) Str::uuid().'.png';
             $params['step'] = $request->status == ModelsCanvassing::STS_HOT ? ModelsCanvassing::STEP_SUDAH_CANVASSING : ModelsCanvassing::STEP_PROSES_CANVASSING;

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
                 $params['step'] =  ModelsCanvassing::STEP_PENGAJUAN_BARU;
                 ModelsEform::create($params);
             }

             if($is_transaction) DB::commit();
             Storage::put($request->foto, base64_decode($image));
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
                    'items' => $data->manyAktifitas ?? [],
                    'attributes' => null
                ];
        } catch (\Throwable $th) {
            throw $th;
        }
     }

     public static function getData($request)
     {
         try {
             $data = Model::whereIn('step', [ModelsCanvassing::STEP_INPUT_CANVASSING,ModelsCanvassing::STEP_PROSES_CANVASSING])
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

}
