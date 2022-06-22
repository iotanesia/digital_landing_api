<?php

namespace App\Query;
use App\ApiHelper as Helper;
use App\Jobs\EformPrescreeningJobs;
use App\Mail\EFormMail;
use App\Models\Eform as ModelsEform;
use App\Models\Prescreening as ModelsPrescreening;
use Illuminate\Support\Facades\DB;
use App\Query\Eform;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
class Prescreening {

    const BELUM_DIPROSES = 0;
    const PROSES = 1;
    const SELESAI = 2;

    public static function byId($id)
    {
        $data =  ModelsEform::find($id);
        $data->nama_propinsi = $data->refPropinsi->nama_propinsi ?? null;
        $data->nama_kabupaten = $data->refKabupaten->nama_kabupaten ?? null;
        $data->nama_kecamatan = $data->refKecamatan->nama_kecamatan ?? null;
        $data->nama_kelurahan = $data->refKelurahan->nama_kelurahan ?? null;
        $status = 'menunggu proses';
        if($data->step_proses_prescreening) $status = self::score($data->manyAktifitas) == 0 ? 'tidak lolos' : 'lolos';
        $data->status_proses_prescreening = $status;
        unset(
            $data->manyAktifitas,
            $data->refPropinsi,
            $data->refKabupaten,
            $data->refKecamatan,
            $data->refKelurahan,
        );
        return [
            'items' => $data,
        ];
    }

    public static function aktifitas($id)
    {
        $data = ModelsEform::with(['manyAktifitas' => function($query){
            $query->join('map_rules_skema_eksternal','map_rules_skema_eksternal.id','prescreening_aktifitas.id_map_rules_skema_eksternal');
            $query->orderBy('map_rules_skema_eksternal.urutan','asc');
        }])->find($id);
        return [
            'items' => $data->manyAktifitas
        ];
    }

    public static function getAll($request)
    {
        try {
            $data = ModelsEform::with(['manyAktifitas' => function ($query){
                $query->join('map_rules_skema_eksternal','map_rules_skema_eksternal.id','prescreening_aktifitas.id_map_rules_skema_eksternal');
                $query->orderBy('map_rules_skema_eksternal.urutan','asc');
            }])->where(function ($query) use ($request){
                if($request->nama) $query->where('nama','ilike',"%$request->nama%");
                if($request->nomor_aplikasi) $query->where('nomor_aplikasi','ilike',"$request->nomor_aplikasi%");
                if($request->nik) $query->where('nik',$request->nik);
                //  $query->where('id',28);

                 // belum difilter berdasrkan cabang
                 // belum difilter berdasrkan rm
            })->paginate($request->limit);

                return [
                    'items' => $data->getCollection()->transform(function ($item){

                        $status = 'menunggu proses';
                        if($item->step_proses_prescreening) $status = self::score($item->manyAktifitas) == 0 ? 'tidak lolos' : 'lolos';
                        return [
                            'id' => $item->id,
                            'nama' => $item->nama,
                            'no_hp' => $item->no_hp,
                            'alamat' => $item->alamat,
                            'nomor_aplikasi' => $item->nomor_aplikasi,
                            'nik' => $item->nik,
                            'foto' => $item->foto,
                            'status' => $item->status,
                            'status_proses_prescreening' => $status,
                            'step_proses_prescreening' => $item->step_proses_prescreening,
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

    public static function score($data)
    {
        $score = 0;
        foreach ($data as $key => $item) {
            $score += $item->disetujui == $item->status ? 1 : 0;
        }
        return $score;
    }

    public static function process($request, $is_transaction = true){

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
            if(!$request->alamat_detail) $require_fileds[] = 'alamat_detail';
            if(!$request->id_produk) $require_fileds[] = 'id_produk';
            if(!$request->id_sub_produk) $require_fileds[] = 'id_sub_produk';
            if(!$request->plafon) $require_fileds[] = 'plafon';
            if(count($require_fileds) > 0) throw new \Exception('This parameter must be filled '.implode(',',$require_fileds),500);
            if(!MProduk::plafon($request->id_sub_produk,$request->plafon)) throw new \Exception('Plafon tidak sesuai',400);

            $data = Eform::byId($request->id);
            if(!$data) throw new \Exception("Data not found.", 400);
            $skema = MSkemaEkternal::skema($data['items']);
            if(!$skema) throw new \Exception("Skema Ekternal belum terdaftar.", 400);
            $store = ModelsEform::find($request->id);
            $params = $request->all();
            $params['step_proses_prescreening'] = self::PROSES;
            $params['nomor_aplikasi'] =Helper::generateNoApliksi($request->current_user->kode_cabang);
            $image = $request->foto;  // your base64 encoded
            $params['foto'] =(string) Str::uuid().'.png';
            $store->fill($params);
            $store->save();
            if($is_transaction) DB::commit();
            Storage::put($request->foto, base64_decode($image));
            $prescreening = (new EformPrescreeningJobs($data));
            dispatch($prescreening);
            $email = $store->email;
            $mail_data = [
                "fullname" => $store->nama,
                "nik" => $store->nik,
                "nomor_aplikasi" => $store->nomor_aplikasi,
            ];
            Mail::to($email)->send(new EFormMail($mail_data));
            unset($store->foto);
            return [
                'items' => $store
            ];
        } catch (\Throwable $th) {
            if($is_transaction) DB::rollBack();
            throw $th;
        }
    }

    public static function saveAktifitas($request,$is_transaction = true)
    {
       if($is_transaction) DB::beginTransaction();
       try {
            $store = ModelsPrescreening::create($request);
            $store->refLog()->create([
                'id_prescreening' => $store->id,
                'id_eform' => $request['id_eform'],
                'request' => $request['request_body'],
                'response' => $request['response_data'],
            ]);
            if($is_transaction) DB::commit();
            return true;
       } catch (\Throwable $th) {
            if($is_transaction) DB::rollBack();
            throw $th;
       }
    }


}
