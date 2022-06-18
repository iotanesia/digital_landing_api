<?php

namespace App\Query;
use App\Models\Eform as Model;
use App\ApiHelper as Helper;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Constants\Group;
use App\Models\Eform as ModelsEform;
use App\Models\MSubProduk;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Eform {

    public static function getData($request)
    {
        try {
            $data = Model::where(function ($query) use ($request){
                $query->where('step',Model::STEP_PENGAJUAN_BARU)->whereNull('kode_aplikasi')->where('kode_cabang',$request->current_user->kode_cabang);
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
                    'email' => $data->email,
                    'tempat_lahir' => $data->tempat_lahir,
                    'tanggal_lahir' => $data->tanggal_lahir,
                    'nama_pasangan' => $data->nama_pasangan,
                    'tempat_lahir_pasangan' => $data->tempat_lahir_pasangan,
                    'tanggal_lahir_pasangan' => $data->tanggal_lahir_pasangan,
                    'id_propinsi' => $data->id_propinsi,
                    'id_kabupaten' => $data->id_kabupaten,
                    'id_kecamatan' => $data->id_kecamatan,
                    'id_kelurahan' => $data->id_kelurahan,
                    'kode_pos' => $data->kode_pos,
                    'alamat_detail' => $data->alamat_detail,
                    'lokasi' => $data->lokasi,
                    'status' => $data->status,
                    'id_jenis_produk' => $data->id_jenis_produk,
                    'id_produk' => $data->id_produk,
                    'id_sub_produk' => $data->id_sub_produk,
                    'plafon' => $data->plafon,
                    'jangka_waktu' => $data->jangka_waktu,
                    'rate' => $data->rate,
                    'angsuran' => $data->angsuran,
                ],
                'attributes' => null,
            ];
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    // eform web
    public static function store($request,$is_transaction = true)
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
            if(!$request->plafon) $require_fileds[] = 'plafon';
            $params = $request->all();
            $params['kode_aplikasi'] = mt_rand(10000,99999).'-'.$request->current_user->kode_cabang.Carbon::now()->format('dmY');
            $params['foto'] = (string) Str::uuid().'.png';
            $params['step'] = ModelsEform::STEP_INPUT_EFORM;
            if(count($require_fileds) > 0) throw new \Exception('This parameter must be filled '.implode(',',$require_fileds),500);
            if(MSubProduk::cekPlafon($request->id_sub_produk,$request->plafon)) throw new \Exception('Plafon tidak sesuai',500);

            $store = Model::find($request->id);

            foreach($params as $key => $val) {
                $store->{$key} = $val;
            }
            $store->save();

            if($is_transaction) DB::commit();
            $image = $request->foto;  // your base64 encoded
            Storage::put($params['foto'], base64_decode($image));
            return [
                'items' => $store
            ];
            return $store;
        } catch (\Throwable $th) {
            if($is_transaction) DB::rollBack();
            throw $th;
        }
    }
}