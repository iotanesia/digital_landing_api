<?php

namespace App\Query\Transaksi;
use Carbon\Carbon;
use App\Models\Transaksi\Pipeline as Model;
use App\Query\Transaksi\Pipeline;
use App\ApiHelper as Helper;
use App\Constants\Constants;
use App\Query\Master\SopAgunan;
use App\Query\Transaksi\PKreditDataAnalisa;
use App\Query\Skema\AgunanNilai;
use App\Query\Skema\AgunanSkemaProduk;
use App\Query\Transaksi\VerifValidasiData;
use Illuminate\Support\Facades\DB;
use App\Query\Master\SkorDetailNilai;
use App\Query\Master\SkorDetail;
use App\Query\Master\Skor;
use App\Query\Transaksi\PKreditDataKeuangan;
use App\Query\Transaksi\SkoringPenilaian;
use App\Query\Transaksi\SkoringPenilaianDetail;


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
            $valid = VerifValidasiData::byIdPipeline($id_pipeline);
            if(!$valid) throw new \Exception("Belum melakukan validasi data",400);
            $skema = AgunanSkemaProduk::getSkema($valid);
            $menu = array_map(function ($item) use ($skema,$data){

                // check step
                if($skema['step']){
                    if(in_array($item['code'],[
                        Constants::STEP_DATA_PERSONAL,
                        Constants::STEP_DATA_KEUANGAN,
                        Constants::STEP_DATA_USAHA,
                    ])) array_push($item['validate'],Constants::STEP_DATA_AGUNAN);
                }

                // checked info
                $item['is_checked'] = in_array($data->step_analisa_kredit,$item['validate']);
                unset(
                    $item['validate']
                );

                // set menu
                if(!$skema['menu']) {
                    if(!in_array($item['code'],[Constants::STEP_DATA_AGUNAN])) return $item;
                }else return $item;
            },Constants::MENU_PROSES_KREDIT);


            return [
                'items' => array_values(array_filter($menu)),
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

            $store =  VerifValidasiData::byIdPipeline($request->id_pipeline);
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
            if($request->sewa_kontrak_usaha === null) $require_fileds[] = 'sewa_kontrak_usaha';
            if(!$request->gaji_pegawai_usaha) $require_fileds[] = 'gaji_pegawai_usaha';
            if(!$request->telp_listrik_air_usaha) $require_fileds[] = 'telp_listrik_air_usaha';
            if(!$request->transportasi_usaha) $require_fileds[] = 'transportasi_usaha';
            if(!$request->pengeluaran_lainnya_usaha === null) $require_fileds[] = 'pengeluaran_lainnya_usaha';
            if(!$request->belanja_rumah_tangga_umah_tangga) $require_fileds[] = 'belanja_rumah_tangga_umah_tangga';
            if(!$request->sewa_kontrak_rumah_tangga === null) $require_fileds[] = 'sewa_kontrak_rumah_tangga';
            if(!$request->pendidikan_rumah_tangga === null) $require_fileds[] = 'pendidikan_rumah_tangga';
            if(!$request->telp_listrik_air_rumah_tangga) $require_fileds[] = 'telp_listrik_air_rumah_tangga';
            if(!$request->transportasi_rumah_tangga) $require_fileds[] = 'transportasi_rumah_tangga';
            if(!$request->pengeluaran_lainnya_rumah_tangga) $require_fileds[] = 'pengeluaran_lainnya_rumah_tangga';
            if(!$request->angsuran_pinjaman_saat_ini_rumah_tangga === null) $require_fileds[] = 'angsuran_pinjaman_saat_ini_rumah_tangga';
            if(!$request->angsuran_kredit_bank_dki_rumah_tangga) $require_fileds[] = 'angsuran_kredit_bank_dki_rumah_tangga';
            if(count($require_fileds) > 0) throw new \Exception('This parameter must be filled '.implode(',',$require_fileds),400);

            $store =  VerifValidasiData::byIdPipeline($request->id_pipeline);
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

            $store =  VerifValidasiData::byIdPipeline($request->id_pipeline);
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
        try {

            return [
                'items' => PKreditDatAgunanTanahBangunan::byIdProsesDataAgunan($id_proses_data_agunan),
            ];

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function storeTanahBangunan($request,$is_transaction = true)
    {
        if($is_transaction) DB::beginTransaction();
        try {

            $require_fileds = [];
            if(!$request->id_agunan) $require_fileds[] = 'id_agunan';
            if(!$request->id_pipeline) $require_fileds[] = 'id_pipeline';
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
        return [
            'items' => PKreditDatAgunanTanahKosong::byIdProsesDataAgunan($id_proses_data_agunan)
        ];
    }

    public static function storeTanahKosong($request,$is_transaction = true)
    {
        if($is_transaction) DB::beginTransaction();
        try {

            $require_fileds = [];
            if(!$request->id_agunan) $require_fileds[] = 'id_agunan';
            if(!$request->id_pipeline) $require_fileds[] = 'id_pipeline';
            if(count($require_fileds) > 0) throw new \Exception('This parameter must be filled '.implode(',',$require_fileds),400);

            $result = PKreditDatAgunanTanahKosong::store($request,false);
            if($is_transaction) DB::commit();
            return [
                'items' => $result
            ];
        } catch (\Throwable $th) {
            if($is_transaction) DB::rollback();
            throw $th;
        }
    }

    public static function kios($id_proses_data_agunan)
    {
        return [
            'items' => PKreditDatAgunanKios::byIdProsesDataAgunan($id_proses_data_agunan)
        ];
    }

    public static function storeKios($request,$is_transaction = true)
    {
        if($is_transaction) DB::beginTransaction();
        try {

            $require_fileds = [];
            if(!$request->id_agunan) $require_fileds[] = 'id_agunan';
            if(!$request->id_pipeline) $require_fileds[] = 'id_pipeline';
            if(count($require_fileds) > 0) throw new \Exception('This parameter must be filled '.implode(',',$require_fileds),400);

            $result = PKreditDatAgunanKios::store($request,false);
            if($is_transaction) DB::commit();
            return [
                'items' => $result
            ];
        } catch (\Throwable $th) {
            if($is_transaction) DB::rollback();
            throw $th;
        }
    }

    public static function kendaraan($id_proses_data_agunan)
    {
        return [
        'items' => PKreditDatAgunanKendaraanBermotor::byIdProsesDataAgunan($id_proses_data_agunan)
        ];
    }

    public static function storeKendaraan($request,$is_transaction = true)
    {
        if($is_transaction) DB::beginTransaction();
        try {

            $require_fileds = [];
            if(!$request->id_agunan) $require_fileds[] = 'id_agunan';
            if(!$request->id_pipeline) $require_fileds[] = 'id_pipeline';
            if(count($require_fileds) > 0) throw new \Exception('This parameter must be filled '.implode(',',$require_fileds),400);

            $result = PKreditDatAgunanKendaraanBermotor::store($request,false);
            if($is_transaction) DB::commit();
            return [
                'items' => $result
            ];
        } catch (\Throwable $th) {
            if($is_transaction) DB::rollback();
            throw $th;
        }
    }

    public static function deposito($id_proses_data_agunan)
    {
        return [
            'items' => PKreditDatAgunanDeposito::byIdProsesDataAgunan($id_proses_data_agunan)
        ];
    }

    public static function storeDeposito($request,$is_transaction = true)
    {
        if($is_transaction) DB::beginTransaction();
        try {
            $require_fileds = [];
            if(!$request->id_agunan) $require_fileds[] = 'id_agunan';
            if(!$request->id_pipeline) $require_fileds[] = 'id_pipeline';
            if(count($require_fileds) > 0) throw new \Exception('This parameter must be filled '.implode(',',$require_fileds),400);

            $result = PKreditDatAgunanDeposito::store($request,false);
            if($is_transaction) DB::commit();
            return [
                'items' => $result
            ];
        } catch (\Throwable $th) {
            if($is_transaction) DB::rollback();
            throw $th;
        }
    }

    public static function dataUsaha($id_pipeline)
    {
        return [
            'items' => PKreditDataUsaha::byIdpipeline($id_pipeline)
        ];
    }

    public static function storeDataUsaha($request,$is_transaction = true)
    {
        if($is_transaction) DB::beginTransaction();
        try {

            $require_fileds = [];
            if(!$request->id_pipeline) $require_fileds[] = 'id_pipeline';
            if(count($require_fileds) > 0) throw new \Exception('This parameter must be filled '.implode(',',$require_fileds),400);

            $store =  VerifValidasiData::byIdPipeline($request->id_pipeline);
            if(!$store) throw new \Exception("Data tidak ditemukan", 400);
            $result = PKreditDataUsaha::store($request,false);
            if($is_transaction) DB::commit();
            return [
                'items' => $result
            ];
        } catch (\Throwable $th) {
            if($is_transaction) DB::rollback();
            throw $th;
        }
    }

    public static function analisa($id_pipeline)
    {
        return [
            'items' => PKreditDataAnalisa::byIdpipeline($id_pipeline)
        ];
    }

    public static function storeAnalisa($request,$is_transaction = true)
    {
        if($is_transaction) DB::beginTransaction();
        try {
            PKreditDataAnalisa::store($request);
        } catch (\Throwable $th) {
            if($is_transaction) DB::rollback();
            throw $th;
        }
    }

    public static function verifikasi($id_pipeline)
    {
        return [
            'items' => PKreditDatAgunanVerifikasi::byIdpipeline($id_pipeline)
        ];
    }

    public static function storeVerifikasi($request,$is_transaction = true)
    {

        if($is_transaction) DB::beginTransaction();
        try {

            PKreditDatAgunanVerifikasi::storeDokumen($request);
        } catch (\Throwable $th) {
            if($is_transaction) DB::rollback();
            throw $th;
        }
    }

    // proses scoring
    public static function selesai($id,$is_transaction = true)
    {

        if($is_transaction) DB::beginTransaction();
        try {
            $id_skor = [1,2,3,4];
            $financial = ProsesKredit::setFinancial($id,$id_skor[0]);
            $karakter = ProsesKredit::setKarakter($id,$id_skor[1]);
            $manajemen = ProsesKredit::setManajemen($id,$id_skor[2]);
            $lingkungan_bisnis = ProsesKredit::setLingkunganBisnis($id,$id_skor[3]);
            $total_financial = $financial['skor_rpc'] + $financial['skor_idir'] + $financial['skor_profitability'];
            $total_karakter = $karakter['skor_integritas_usaha'] + $karakter['skor_riwayat_hub_bank'];
            $total_manajemen = $manajemen['skor_prospek_usaha'] + $manajemen['skor_lama_usaha'] + $manajemen['skor_jangka_waktu'];
            $total_lingkungan_bisnis = $lingkungan_bisnis['skor_ketergantungan_pelanggan'] + $lingkungan_bisnis['skor_jenis_produk'] + $lingkungan_bisnis['skor_ketergantungan_supplier'] + $lingkungan_bisnis['skor_wilayah_pemasaran'];
            $total = round($total_financial + $total_karakter + $total_manajemen + $total_lingkungan_bisnis,2);

            $total_financial_arr = [$financial['skor_rpc'], $financial['skor_idir'], $financial['skor_profitability']];
            $total_karakter_arr = [$karakter['skor_integritas_usaha'], $karakter['skor_riwayat_hub_bank']];
            $total_manajemen_arr = [$manajemen['skor_prospek_usaha'], $manajemen['skor_lama_usaha'], $manajemen['skor_jangka_waktu']];
            $total_lingkungan_bisnis_arr = [$lingkungan_bisnis['skor_ketergantungan_pelanggan'], $lingkungan_bisnis['skor_jenis_produk'], $lingkungan_bisnis['skor_ketergantungan_supplier'], $lingkungan_bisnis['skor_wilayah_pemasaran']];
            $penilaianDetail = array(1 => $total_financial_arr, 2 => $total_karakter_arr, 3 => $total_manajemen_arr, 4 => $total_lingkungan_bisnis_arr);

            $penilaian = [];
            $penilaian['id_pipeline'] = $id;
            $penilaian['skor'] = $total;

            if($total > 85) $penilaian['jenis'] = 'approved';
            elseif($total <= 85 && $total > 60) $penilaian['jenis'] = 'menunggu approval';
            else $penilaian['jenis'] = 'reject';

            $result = SkoringPenilaian::store($penilaian,false);

            foreach($penilaianDetail as $key=>$val) {
                $storeDetail = [];
                $storeDetail['id'] = null;
                $storeDetail['val_penilaian'] = $val;
                $storeDetail['id_skoring_penilaian'] = $result->id;
                $storeDetail['id_skor'] = $key;
                $index = 1;
                foreach ($storeDetail['val_penilaian'] as $val){
                    $storeDetail['id_skor_detail'] = $index;
                    $storeDetail['penilaian'] = $val;
                    $resultDetail = SkoringPenilaianDetail::store($storeDetail);
                    $index++;
                }
            }

            Pipeline::updateStepAnalisaKredit([
                'id_pipeline' => $id,
                'step_analisa_kredit' => Constants::STEP_DATA_SEDANG_PROSES_SKORING
            ],false);
            return ['items' => $result];
        } catch (\Throwable $th) {
            if($is_transaction) DB::rollback();
            throw $th;
        }
    }

    public static function setLtvTaksasi($request)
    {
        try {

            $sop = SopAgunan::byIdAgunan($request->id_agunan);
            if(!$sop) throw new \Exception("Sop agunan belum dibuat", 400);
            $data = VerifValidasiData::byIdPipeline($request->id_pipeline);
            if(!$data) throw new \Exception("Data Pipeline tidak ditemukan", 400);
            $taksasi = $request->nilai_market * $sop->presentase / 100;
            $ltv = $request->nilai_market ? $data->plafond / $request->nilai_market : 0;
            return [
                'ltv' =>  $ltv,
                'taksasi' =>  $taksasi
            ];
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function setKarakter($id_pipeline,$id_skor)
    {
        try {
            $usaha = PKreditDataUsaha::byIdPipeline($id_pipeline);
            if(!$usaha) throw new \Exception("Data Pipeline tidak ditemukan", 400);

            $skor = Skor::byId($id_skor);
            $skor_detail = SkorDetail::byIdSkor($id_skor);
            $itemsArr = [];
            foreach($skor_detail as $key) {
                $itemsArr[] = $key->bobot;
            }
            $nilai1 = ((SkorDetailNilai::pembanding('id_integritas_usaha',$usaha->id_integritas_usaha)/3) * $itemsArr[0]) * $skor->bobot;
            $nilai2 = ((SkorDetailNilai::pembanding('id_riwayat_hubungan_bank',$usaha->id_riwayat_hubungan_bank)/3) * $itemsArr[1]) * $skor->bobot;
            return [
                'skor_integritas_usaha' =>  round($nilai1,2),
                'skor_riwayat_hub_bank' =>  round($nilai2,2)
            ];
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function setFinancial($id_pipeline,$id_skor)
    {
        try {
            $keuangan = PKreditDataKeuangan::byIdPipeline($id_pipeline);
            if(!$keuangan) throw new \Exception("Data Pipeline tidak ditemukan", 400);

            $skor = Skor::byId($id_skor);
            $skor_detail = SkorDetail::byIdSkor($id_skor);
            $itemsArr = [];
            foreach($skor_detail as $key) {
                $itemsArr[] = $key->bobot;
            }
            if($keuangan->rpc < 2) $kondisi1 = '< 2';
            elseif ($keuangan->rpc >= 2 && $keuangan->rpc <= 2.90) $kondisi1 = '>= 2 and <= 2.90';
            elseif ($keuangan->rpc > 2.90) $kondisi1 = '> 2.90';
            else $kondisi1 = null;

            if($keuangan->idir >= 75 && $keuangan->idir <= 80) $kondisi2 = '>= 75 and <= 80';
            elseif ($keuangan->idir >= 70 && $keuangan->idir < 75) $kondisi2 = '>= 70 and < 75';
            elseif ($keuangan->idir < 70) $kondisi2 = '< 70';
            else $kondisi2 = null;

            if($keuangan->profitability < 15) $kondisi3 = '< 15';
            elseif ($keuangan->profitability > 25) $kondisi3 = '> 25';
            elseif ($keuangan->profitability >= 15 && $keuangan->profitability <= 25) $kondisi3 = '>= 15 and <= 25';
            else $kondisi3 = null;

            $nilai1 = ((SkorDetailNilai::pembanding('rpc',$kondisi1)/3) * $itemsArr[0]) * $skor->bobot;
            $nilai2 = ((SkorDetailNilai::pembanding('idir',$kondisi2)/3) * $itemsArr[1]) * $skor->bobot;
            $nilai3 = ((SkorDetailNilai::pembanding('profitability',$kondisi3)/3) * $itemsArr[2]) * $skor->bobot;
            return [
                'skor_rpc' =>  round($nilai1,2),
                'skor_idir' =>  round($nilai2,2),
                'skor_profitability' =>  round($nilai3,2)
            ];
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function setManajemen($id_pipeline,$id_skor)
    {
        try {
            $usaha = PKreditDataUsaha::byIdPipeline($id_pipeline);
            if(!$usaha) throw new \Exception("Data Pipeline tidak ditemukan", 400);

            $skor = Skor::byId($id_skor);
            $skor_detail = SkorDetail::byIdSkor($id_skor);
            $itemsArr = [];
            foreach($skor_detail as $key) {
                $itemsArr[] = $key->bobot;
            }
            if($usaha->lama_usaha <= 3) $kondisi1 = '<= 3';
            elseif ($usaha->lama_usaha > 3 && $usaha->lama_usaha <= 7) $kondisi1 = '> 3 and <= 7';
            elseif ($usaha->lama_usaha > 7) $kondisi1 = '> 7';
            else $kondisi1 = null;

            if($usaha->jangka_waktu > 3) $kondisi2 = '> 3';
            elseif ($usaha->jangka_waktu >= 1 && $usaha->jangka_waktu <= 3) $kondisi2 = '>= 1 and <= 3';
            elseif ($usaha->jangka_waktu < 1) $kondisi2 = '< 1';
            else $kondisi2 = null;

            $nilai1 = ((SkorDetailNilai::pembanding('id_prospek_usaha',$usaha->id_prospek_usaha)/3) * $itemsArr[0]) * $skor->bobot;
            $nilai2 = ((SkorDetailNilai::pembanding('lama_usaha',$kondisi1)/3) * $itemsArr[1]) * $skor->bobot;
            $nilai3 = ((SkorDetailNilai::pembanding('jangka_waktu',$kondisi2)/3) * $itemsArr[2]) * $skor->bobot;
            return [
                'skor_prospek_usaha' =>  round($nilai1,2),
                'skor_lama_usaha' =>  round($nilai2,2),
                'skor_jangka_waktu' =>  round($nilai3,2)
            ];
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function setLingkunganBisnis($id_pipeline,$id_skor)
    {
        try {
            $usaha = PKreditDataUsaha::byIdPipeline($id_pipeline);
            if(!$usaha) throw new \Exception("Data Pipeline tidak ditemukan", 400);

            $skor = Skor::byId($id_skor);
            $skor_detail = SkorDetail::byIdSkor($id_skor);
            $itemsArr = [];
            foreach($skor_detail as $key) {
                $itemsArr[] = $key->bobot;
            }
            $nilai1 = ((SkorDetailNilai::pembanding('id_ketergantungan_pelanggan',$usaha->id_integritas_usaha)/3) * $itemsArr[0]) * $skor->bobot;
            $nilai2 = ((SkorDetailNilai::pembanding('id_jenis_produk',$usaha->id_riwayat_hubungan_bank)/3) * $itemsArr[1]) * $skor->bobot;
            $nilai3 = ((SkorDetailNilai::pembanding('id_ketergantungan_supplier',$usaha->id_riwayat_hubungan_bank)/3) * $itemsArr[2]) * $skor->bobot;
            $nilai4 = ((SkorDetailNilai::pembanding('id_wilayah_pemasaran',$usaha->id_riwayat_hubungan_bank)/3) * $itemsArr[3]) * $skor->bobot;
            return [
                'skor_ketergantungan_pelanggan' =>  round($nilai1,2),
                'skor_jenis_produk' =>  round($nilai2,2),
                'skor_ketergantungan_supplier' =>  round($nilai3,2),
                'skor_wilayah_pemasaran' =>  round($nilai4,2)
            ];
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
