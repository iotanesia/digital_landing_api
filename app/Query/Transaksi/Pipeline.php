<?php

namespace App\Query\Transaksi;
use App\Models\Transaksi\Pipeline as Model;
use App\View\Transaksi\VListPipeline as View;
use App\ApiHelper as Helper;
use App\Constants\Constants;
use App\Models\Transaksi\AktifitasPemasaranPrescreening;
use App\Models\Transaksi\EfomPrescreening;
use App\Models\Transaksi\LeadsPrescreening;
use App\Query\Status\StsTracking;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Query\Transaksi\AktifitasPemasaran;
use App\Query\Transaksi\Leads;
use App\Query\Transaksi\Eform;

class Pipeline {

    public static function byId($id_pipeline)
    {
        try {
            $data = View::find($id_pipeline);
            if(!$data) throw new \Exception("Data not found.", 400);

            $tipeNasabah = $data->tipe_calon_nasabah;
            $refId =  $data->ref_id;
            $dataNasabah = [];

            if($tipeNasabah=='Eform')$dataNasabah = Eform::byId($refId);
            if($tipeNasabah=='Leads')$dataNasabah = Leads::byId($refId);
            if($tipeNasabah=='Aktifitas Pemasaran')$dataNasabah = AktifitasPemasaran::byIdForPiperline($refId);
            return [
                'items' => [
                    'id' => $data->id,
                    'nomor_aplikasi' => $data->nomor_aplikasi,
                    'nama' => $data->nama,
                    'nik' => $data->nik,
                    'email' => $data->email,
                    'plafond' => $data->plafond,
                    'no_hp' => $data->no_hp,
                    'ref_id' => $data->ref_id,
                    'tipe_calon_nasabah' => $data->tipe_calon_nasabah,
                    'foto_selfie' => $data->foto_selfie,
                    'data_nasabah' => $dataNasabah['items'],
                ],
                'attributes' => null,
            ];
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function getDataCurrent($request)
    {
        try {
            $filter_tanggal = Helper::filterByDateDefaultWeek($request);
            $data = View::where(function ($query) use ($request, $filter_tanggal){
                $query->where('id_user',$request->current_user->id);
                $query->whereBetween('v_list_pipeline.created_at',$filter_tanggal['filter']);
                if($request->nik) $query->where('nik',$request->nik);
                if($request->tipe_calon_nasabah) $query->where('tipe_calon_nasabah',$request->tipe_calon_nasabah);
            })->paginate($request->limit);
            return [
                'items' => $data->getCollection()->transform(function ($item){
                    return [
                        'id' => $item->id,
                        'nik' => $item->nik,
                        'nama' => $item->nama,
                        'tipe_calon_nasabah' => $item->tipe_calon_nasabah,
                        'foto_selfie' => $item->foto_selfie,
                        'status_prescreening'=> 'Lolos'
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

    public static function checkNasabah($nik) {
        try {
            $data = Model::where('nik', $nik)->first();
            $result = ['is_pipeline'=>Constants::IS_NOL,'is_cutoff'=>Constants::IS_NOL,'is_prescreening'=>Constants::IS_NOL];
            if($data && $data->tracking != Constants::DISBURSMENT) $result = ['is_pipeline'=>Constants::IS_NOL,'is_cutoff'=>Constants::IS_ACTIVE,'is_prescreening'=>Constants::CUT_OFF];

            return $result;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function getInfoPrescreening($request, $id) {
        try {
            $dataPipeline = View::where('id', $id)->first();
            // $dataPipeline->ref_id = 109; //hard code sementara

            if(!$dataPipeline) throw new \Exception("Data not found.", 400);
            if($dataPipeline->tipe_calon_nasabah == 'Eform') $data = EfomPrescreening::where('id_eform',$dataPipeline->ref_id)->paginate($request->limit);
            if($dataPipeline->tipe_calon_nasabah == 'Leads') $data = LeadsPrescreening::where('id_leads',$dataPipeline->ref_id)->paginate($request->limit);
            if($dataPipeline->tipe_calon_nasabah == 'Aktifitas Pemasaran') $data = AktifitasPemasaranPrescreening::where('id_aktifitas_pemasaran',$dataPipeline->ref_id)->paginate($request->limit);
        return [
            'items' => $data->getCollection()->transform(function ($item){
                return [
                    'id' => $item->id,
                    'metode' => $item->refRules->refMetode->metode ?? null,
                    'skema' => $item->refRules->refSkema->skema ?? null,
                    'status' =>  $item->keterangan,
                    'response' => isset(json_decode($item->response,true)['keterangan']) ? json_decode($item->response,true)['keterangan'] : (isset(json_decode($item->response,true)['message']) ? json_decode($item->response,true)['message'] : null)
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

    public static function getDataVerifies($request) {
        try {
            $data = Model::where(function ($query) use ($request){
                $query->where('tracking',Constants::ANALISA_KREDIT);
                $query->where('id_user',$request->current_user->id);
            })->paginate($request->limit);
            return [
                'items' => $data->getCollection()->transform(function ($item){

                    if($item->id_tipe_calon_nasabah == Constants::TCN_EFORM) $data = $item->refEform;
                    elseif($item->id_tipe_calon_nasabah == Constants::TCN_AKTIFITAS_PEMASARAN)   $data = $item->refAktifitasPemasaran;
                    else $data = $item->refLeads;

                    return [
                        'id' => $item->id,
                        'nik' => $item->nik,
                        'nama' => $data->nama,
                        'nama_produk'=> $data->refProduk->nama,
                        'nama_sub_produk'=> $data->refSubProduk->nama,
                        'created_at' => $item->created_at,
                        'foto' => $data->id_jenis_kelamin == 2 ? 'female.png' : 'male.png'

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

    public static function getDataVerifiesMenu($request, $id_pipeline) {
        try {
            $data = Model::find($id_pipeline);
            if(!$data) throw new \Exception("Data tidak ditemukan",400);
            $menuArr = [
                [
                    'code' => 1,
                    'name' => 'verifikasi data',
                    'is_checked' => in_array($data->step_analisa_kredit,[
                        Constants::STEP_ANALISA_VERIF_DATA,
                        Constants::STEP_ANALISA_ONSITE_VISIT,
                        Constants::STEP_ANALISA_KELENGKAPAN,
                        Constants::STEP_ANALISA_SUBMIT,
                    ])
                ],
                [
                    'code' => 2,
                    'name' => 'onsite visit',
                    'is_checked' => in_array($data->step_analisa_kredit,[
                        Constants::STEP_ANALISA_ONSITE_VISIT,
                        Constants::STEP_ANALISA_KELENGKAPAN,
                        Constants::STEP_ANALISA_SUBMIT,
                    ])
                ],
                [
                    'code' => 3,
                    'name' => 'kelengkapan data',
                    'is_checked' => in_array($data->step_analisa_kredit,[
                        Constants::STEP_ANALISA_KELENGKAPAN,
                        Constants::STEP_ANALISA_SUBMIT,
                    ])
                ]];


            return [
                'items' => $menuArr,
                'attributes' => null,
            ];

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function updateStepAnalisaKredit($request, $is_transaction = true)
    {
        if($is_transaction) DB::beginTransaction();
        try {
            $data = Model::find($request['id_pipeline']);
            if(!$data) return throw new \Exception("Data tidak ditemukan", 400);
            $data->step_analisa_kredit = $request['step_analisa_kredit'];
            $data->updated_by = request()->current_user->id;
            $data->save();
            if($is_transaction) DB::commit();
        } catch (\Throwable $th) {
            if($is_transaction) DB::rollBack();
            throw $th;
        }
    }

    public static function submit($request, $is_transaction = true)
    {
        if($is_transaction) DB::beginTransaction();
        try {
            $require_fileds = [];
            if(!$request->id_pipeline) $require_fileds[] = 'id_pipeline';
            if(count($require_fileds) > 0) throw new \Exception('This parameter must be filled '.implode(',',$require_fileds),400);
            $data = Model::find($request->id_pipeline);
            if(!$data) return throw new \Exception("Data tidak ditemukan", 400);
            $data->step_analisa_kredit = Constants::STEP_ANALISA_SUBMIT;
            $data->updated_by = request()->current_user->id;
            $data->save();
            if($is_transaction) DB::commit();
        } catch (\Throwable $th) {
            if($is_transaction) DB::rollBack();
            throw $th;
        }
    }

    public static function detailVerifikasi($id)
    {
        $data = Model::find($id);
        if(!$data) return throw new \Exception("Data tidak ditemukan", 400);
        if($data->id_tipe_calon_nasabah == Constants::TCN_EFORM) $modul = $data->refEform;
        elseif($data->id_tipe_calon_nasabah == Constants::TCN_AKTIFITAS_PEMASARAN) $modul = $data->refAktifitasPemasaran;
        else $modul = $data->refLeads;
        if($data->step_analisa_kredit >= Constants::STEP_ANALISA_VERIF_DATA) $modul = VerifValidasiData::byIdPipeline($id);
        if(!$modul) throw new \Exception("belum melakukan validasi data", 400);
        $result = $modul;
        $result->nama_produk = $modul->refProduk->nama ?? null;
        $result->nama_status_perkawinan = $modul->refStatusPerkawinan->nama ?? null;
        $result->nama_sub_produk = $modul->refSubProduk->nama ?? null;
        $result->nama_jenis_kelamin = $modul->refJenisKelamin->nama ?? null;
        $result->nama_agama = $modul->refAgama->nama ?? null;
        $result->nama_propinsi = $modul->refPropinsi->nama ?? null;
        $result->nama_kabupaten = $modul->refKabupaten->nama ?? null;
        $result->nama_kecamatan = $modul->refKecamatan->nama ?? null;
        $result->nama_kelurahan = $modul->refKelurahan->nama ?? null;
        $result->nama_propinsi_pasangan = $modul->refPropinsiPasangan->nama ?? null;
        $result->nama_kabupaten_pasangan = $modul->refKabupatenPasangan->nama ?? null;
        $result->nama_kecamatan_pasangan = $modul->refKecamatanPasangan->nama ?? null;
        $result->nama_kelurahan_pasangan = $modul->refKelurahanPasangan->nama ?? null;
        $result->profil_usaha = $modul->manyProfilUsaha->map(function ($item){
            $item->nama_propinsi  = $item->refPropinsi->nama ?? null;
            $item->nama_kabupaten = $item->refKabupaten->nama ?? null;
            $item->nama_kecamatan = $item->refKecamatan->nama ?? null;
            $item->nama_kelurahan = $item->refKelurahan->nama ?? null;
            unset(
                $item->refPropinsi,
                $item->refKabupaten,
                $item->refKecamatan,
                $item->refKelurahan,
            );
            return $item;
        }) ?? null;
        unset(
            $modul->id_cabang,
            $modul->refProduk,
            $modul->refStatusPerkawinan,
            $modul->refCabang,
            $modul->refSubProduk,
            $modul->refJenisKelamin,
            $modul->refAgama,
            $modul->refPropinsi,
            $modul->refKabupaten,
            $modul->refKecamatan,
            $modul->refKelurahan,
            $modul->refPropinsiPasangan,
            $modul->refKabupatenPasangan,
            $modul->refKecamatanPasangan,
            $modul->refKelurahanPasangan,
            $modul->manyProfilUsaha
        );
        return [
            'items' => $result
        ];
    }
}
