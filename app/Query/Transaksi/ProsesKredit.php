<?php

namespace App\Query\Transaksi;
use Carbon\Carbon;
use App\Models\Transaksi\Pipeline as Model;
use App\Query\Transaksi\Pipeline;
use App\ApiHelper as Helper;
use App\Constants\Constants;
use App\Models\Transaksi\VerifValidasiData;
use Illuminate\Support\Facades\DB;


class ProsesKredit {

    public static function getDataCurrent($request) {
        try {
            $data = Model::where(function ($query) use ($request){
                $query->where('tracking',Constants::ANALISA_KREDIT);
                $query->where('step_analisa_kredit','>=',Constants::STEP_ANALISA_SUBMIT);
                $query->where('id_user',$request->current_user->id);
            })->paginate($request->limit);
            return [
                'items' => $data->getCollection()->transform(function ($item){
                    $data = $item->refVerifValidasiData;
                    $id_jenis_kelamin = $data->id_jenis_kelamin ?? null;
                    return [
                        'id' => $item->id ?? null,
                        'nik' => $item->nik ?? null,
                        'nama' => $data->nama ?? null,
                        'nama_produk'=> $data->refProduk->nama ?? null,
                        'nama_sub_produk'=> $data->refSubProduk->nama ?? null,
                        'created_at' => $item->created_at ?? null,
                        'foto' => $id_jenis_kelamin == 2 ? 'female.png' : 'male.png'

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

    public static function getMenu($request, $id_pipeline) {
        try {
            $data = Model::find($id_pipeline);
            if(!$data) throw new \Exception("Data tidak ditemukan",400);
            $menuArr = [
                [
                    'code' => 1,
                    'name' => 'data personal',
                    'is_checked' => false
                ],
                [
                    'code' => 2,
                    'name' => 'data keuangan',
                    'is_checked' => false
                ],
                [
                    'code' => 3,
                    'name' => 'data usaha',
                    'is_checked' => false
                ],
                [
                    'code' => 4,
                    'name' => 'data agunan',
                    'is_checked' => false
                ],
                [
                    'code' => 5,
                    'name' => 'analisa kredit',
                    'is_checked' => false
                ],
                [
                    'code' => 6,
                    'name' => 'verifikasi agunan',
                    'is_checked' => false
                ]
            ];


            return [
                'items' => $menuArr,
                'attributes' => null,
            ];

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function dataPersonal($id)
    {
        $modul = VerifValidasiData::byIdPipeline($id);
        if(!$modul) throw new \Exception("Data tidak ditemukan", 400);
        $result = $modul;
        $result->nama_cabang = $modul->refCabang->nama_cabang ?? null;
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
        $result->nik_kontak_darurat = $modul->refPKreditDataPersonal->nik_kontak_darurat ?? null;
        $result->nama_kontak_darurat = $modul->refPKreditDataPersonal->nama_kontak_darurat ?? null;
        $result->no_hp_kontak_darurat = $modul->refPKreditDataPersonal->no_hp_kontak_darurat ?? null;
        $result->tempat_lahir_kontak_darurat = $modul->refPKreditDataPersonal->tempat_lahir_kontak_darurat ?? null;
        $result->tangal_lahir_kontak_darurat = $modul->refPKreditDataPersonal->tangal_lahir_kontak_darurat ?? null;
        $result->alamat_kontak_darurat = $modul->refPKreditDataPersonal->alamat_kontak_darurat ?? null;
        unset(
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
            $modul->refPKreditDataPersonal
        );
        return [
            'items' => $result
        ];
    }

    public static function updateDataPersonal($request,$is_transaction = true)
    {
        if($is_transaction) DB::beginTransaction();
        try {

            $require_fileds = [];
            if(!$request->id_pipeline) $require_fileds[] = 'id_pipeline';
            if(!$request->nama) $require_fileds[] = 'nama';
            if(!$request->no_hp) $require_fileds[] = 'no_hp';
            if(!$request->email) $require_fileds[] = 'email';
            if(!$request->tempat_lahir) $require_fileds[] = 'tempat_lahir';
            if(!$request->tgl_lahir) $require_fileds[] = 'tgl_lahir';
            if(!$request->npwp) $require_fileds[] = 'npwp';
            if(!$request->alamat) $require_fileds[] = 'alamat';
            if(!$request->id_agama) $require_fileds[] = 'id_agama';
            if(!$request->id_status_perkawinan) $require_fileds[] = 'id_status_perkawinan';
            if(!$request->nik_kontak_darurat) $require_fileds[] = 'nik_kontak_darurat';
            if(!$request->nama_kontak_darurat) $require_fileds[] = 'nama_kontak_darurat';
            if(!$request->no_hp_kontak_darurat) $require_fileds[] = 'no_hp_kontak_darurat';
            if(!$request->tempat_lahir_kontak_darurat) $require_fileds[] = 'tempat_lahir_kontak_darurat';
            if(!$request->tangal_lahir_kontak_darurat) $require_fileds[] = 'tangal_lahir_kontak_darurat';
            if(!$request->alamat_kontak_darurat) $require_fileds[] = 'alamat_kontak_darurat';
            // Jika kawin
            if($request->id_status_perkawinan == 1) {
                if(!$request->nama_pasangan) $require_fileds[] = 'nama_pasangan';
                if(!$request->no_hp_pasangan) $require_fileds[] = 'no_hp_pasangan';
                if(!$request->email_pasangan) $require_fileds[] = 'email_pasangan';
                if(!$request->tempat_lahir_pasangan) $require_fileds[] = 'tempat_lahir_pasangan';
                if(!$request->tgl_lahir_pasangan) $require_fileds[] = 'tgl_lahir_pasangan';
                if(!$request->npwp_pasangan) $require_fileds[] = 'npwp_pasangan';
                if(!$request->alamat_pasangan) $require_fileds[] = 'alamat_pasangan';
                if(!$request->id_agama_pasangan) $require_fileds[] = 'id_agama_pasangan';
            }
            if(count($require_fileds) > 0) throw new \Exception('This parameter must be filled '.implode(',',$require_fileds),400);

            $store =  VerifValidasiData::where('id_pipeline',$request->id_pipeline)->first();
            if(!$store) throw new \Exception("Data tidak ditemukan", 400);
            $store->fill($request->all());
            $store->save();

            PKreditDataPersonal::store($request,false);
            Pipeline::updateStepAnalisaKredit([
                'id_pipeline' => $request->id_pipeline,
                'step_analisa_kredit' => Constants::STEP_DATA_PERSONAL
            ],false);

            if($is_transaction) DB::commit();
        } catch (\Throwable $th) {
            if($is_transaction) DB::rollback();
            throw $th;
        }
    }

    // keuangan
    public static function dataKeuangan($id)
    {
        $data = PKreditDataKeuangan::byIdPipeline($id);
        return [
            'items' => $data
        ];
    }

    public static function updateDataKeuangan($request,$is_transaction = true)
    {
        if($is_transaction) DB::beginTransaction();
        try {

            $require_fileds = [];
            if(!$request->id_pipeline) $require_fileds[] = 'id_pipeline';
            if(!$request->omzet_usaha) $require_fileds[] = 'omzet_usaha';
            if(!$request->hpp_usaha) $require_fileds[] = 'hpp_usaha';
            if(!$request->sewa_kontrak_usaha) $require_fileds[] = 'sewa_kontrak_usaha';
            if(!$request->gaji_pegawai_usaha) $require_fileds[] = 'gaji_pegawai_usaha';
            if(!$request->telp_listrik_air_usaha) $require_fileds[] = 'telp_listrik_air_usaha';
            if(!$request->transportasi_usaha) $require_fileds[] = 'transportasi_usaha';
            if(!$request->pengeluaran_lainnya_usaha) $require_fileds[] = 'pengeluaran_lainnya_usaha';
            if(!$request->penghasilan_lainnya_usaha) $require_fileds[] = 'penghasilan_lainnya_usaha';
            if(!$request->belanja_rumah_tangga_umah_tangga) $require_fileds[] = 'belanja_rumah_tangga_umah_tangga';
            if(!$request->sewa_kontrak_rumah_tangga) $require_fileds[] = 'sewa_kontrak_rumah_tangga';
            if(!$request->pendidikan_rumah_tangga) $require_fileds[] = 'pendidikan_rumah_tangga';
            if(!$request->telp_listrik_air_rumah_tangga) $require_fileds[] = 'telp_listrik_air_rumah_tangga';
            if(!$request->transportasi_rumah_tangga) $require_fileds[] = 'transportasi_rumah_tangga';
            if(!$request->pengeluaran_lainnya_rumah_tangga) $require_fileds[] = 'pengeluaran_lainnya_rumah_tangga';
            if(!$request->angsuran_pinjaman_saat_ini_rumah_tangga) $require_fileds[] = 'angsuran_pinjaman_saat_ini_rumah_tangga';
            if(!$request->angsuran_kredit_bank_dki_rumah_tangga) $require_fileds[] = 'angsuran_kredit_bank_dki_rumah_tangga';
            if(count($require_fileds) > 0) throw new \Exception('This parameter must be filled '.implode(',',$require_fileds),400);

            $store =  VerifValidasiData::where('id_pipeline',$request->id_pipeline)->first();
            if(!$store) throw new \Exception("Data tidak ditemukan", 400);
            PKreditDataKeuangan::store($request,false);
            Pipeline::updateStepAnalisaKredit([
                'id_pipeline' => $request->id_pipeline,
                'step_analisa_kredit' => Constants::STEP_DATA_KEUANGAN
            ],false);

            if($is_transaction) DB::commit();
        } catch (\Throwable $th) {
            if($is_transaction) DB::rollback();
            throw $th;
        }
    }

    public static function agunan($id_pipeline)
    {
        return [
            'items' => PKreditDataAgunan::byIdpipeline($id_pipeline)
        ];
    }

    public static function storeAgunan($request,$is_transaction = true)
    {
        if($is_transaction) DB::beginTransaction();
        try {

            $require_fileds = [];
            if(!$request->id_pipeline) $require_fileds[] = 'id_pipeline';
            if(count($require_fileds) > 0) throw new \Exception('This parameter must be filled '.implode(',',$require_fileds),400);

            $store =  VerifValidasiData::where('id_pipeline',$request->id_pipeline)->first();
            if(!$store) throw new \Exception("Data tidak ditemukan", 400);
            $result = PKreditDataAgunan::store($request,false);
            if($is_transaction) DB::commit();
            return [
                'items' => $result
            ];
        } catch (\Throwable $th) {
            if($is_transaction) DB::rollback();
            throw $th;
        }
    }

    public static function tanahBangunan($id_proses_data_agunan)
    {
        return [
            'items' => PKreditDatAgunanTanahBangunan::byIdProsesDataAgunan($id_proses_data_agunan)
        ];
    }

    public static function storeTanahBangunan($request,$is_transaction = true)
    {
        if($is_transaction) DB::beginTransaction();
        try {

            $require_fileds = [];
            if(!$request->id_proses_data_agunan) $require_fileds[] = 'id_proses_data_agunan';
            if(count($require_fileds) > 0) throw new \Exception('This parameter must be filled '.implode(',',$require_fileds),400);

            $result = PKreditDatAgunanTanahBangunan::store($request,false);
            if($is_transaction) DB::commit();
            return [
                'items' => $result
            ];
        } catch (\Throwable $th) {
            if($is_transaction) DB::rollback();
            throw $th;
        }
    }


    public static function tanahKosong($id_proses_data_agunan)
    {
        // return [
        //     'items' => PKreditDatAgunanTanahKosong::byIdProsesDataAgunan($id_proses_data_agunan)
        // ];
    }

    public static function storeTanahKosong($request,$is_transaction = true)
    {
        // if($is_transaction) DB::beginTransaction();
        // try {

        //     $require_fileds = [];
        //     if(!$request->id_proses_data_agunan) $require_fileds[] = 'id_proses_data_agunan';
        //     if(count($require_fileds) > 0) throw new \Exception('This parameter must be filled '.implode(',',$require_fileds),400);

        //     $result = PKreditDatAgunanTanahKosong::store($request,false);
        //     if($is_transaction) DB::commit();
        //     return [
        //         'items' => $result
        //     ];
        // } catch (\Throwable $th) {
        //     if($is_transaction) DB::rollback();
        //     throw $th;
        // }
    }

    public static function kios($id_proses_data_agunan)
    {
        // return [
        //     'items' => PKreditDatAgunanTanahKios::byIdProsesDataAgunan($id_proses_data_agunan)
        // ];
    }

    public static function storeKios($request,$is_transaction = true)
    {
        // if($is_transaction) DB::beginTransaction();
        // try {

        //     $require_fileds = [];
        //     if(!$request->id_proses_data_agunan) $require_fileds[] = 'id_proses_data_agunan';
        //     if(count($require_fileds) > 0) throw new \Exception('This parameter must be filled '.implode(',',$require_fileds),400);

        //     $result = PKreditDatAgunanTanahKios::store($request,false);
        //     if($is_transaction) DB::commit();
        //     return [
        //         'items' => $result
        //     ];
        // } catch (\Throwable $th) {
        //     if($is_transaction) DB::rollback();
        //     throw $th;
        // }
    }

    public static function kendaraan($id_proses_data_agunan)
    {
        // return [
        //     'items' => PKreditDatAgunanTanahKendaraan::byIdProsesDataAgunan($id_proses_data_agunan)
        // ];
    }

    public static function storeKendaraan($request,$is_transaction = true)
    {
        // if($is_transaction) DB::beginTransaction();
        // try {

        //     $require_fileds = [];
        //     if(!$request->id_proses_data_agunan) $require_fileds[] = 'id_proses_data_agunan';
        //     if(count($require_fileds) > 0) throw new \Exception('This parameter must be filled '.implode(',',$require_fileds),400);

        //     $result = PKreditDatAgunanTanahKendaraan::store($request,false);
        //     if($is_transaction) DB::commit();
        //     return [
        //         'items' => $result
        //     ];
        // } catch (\Throwable $th) {
        //     if($is_transaction) DB::rollback();
        //     throw $th;
        // }
    }

    public static function deposito($id_proses_data_agunan)
    {
        // return [
        //     'items' => PKreditDatAgunanTanahDeposito::byIdProsesDataAgunan($id_proses_data_agunan)
        // ];
    }

    public static function storeDeposito($request,$is_transaction = true)
    {
        // if($is_transaction) DB::beginTransaction();
        // try {

        //     $require_fileds = [];
        //     if(!$request->id_proses_data_agunan) $require_fileds[] = 'id_proses_data_agunan';
        //     if(count($require_fileds) > 0) throw new \Exception('This parameter must be filled '.implode(',',$require_fileds),400);

        //     $result = PKreditDatAgunanTanahDeposito::store($request,false);
        //     if($is_transaction) DB::commit();
        //     return [
        //         'items' => $result
        //     ];
        // } catch (\Throwable $th) {
        //     if($is_transaction) DB::rollback();
        //     throw $th;
        // }
    }
}
