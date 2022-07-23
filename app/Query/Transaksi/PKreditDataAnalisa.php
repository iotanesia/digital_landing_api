<?php

namespace App\Query\Transaksi;

use App\Constants\Constants;
use App\Models\Transaksi\Pipeline;
use App\Models\Transaksi\PKreditDataAnalisa as Model;
use App\Query\Transaksi\Pipeline as TransaksiPipeline;
use Illuminate\Support\Facades\DB;

class PKreditDataAnalisa
{
    public static function byIdPipeline($id_pipeline)
    {
        try {
            $pipeline = Pipeline::find($id_pipeline);
            if($pipeline->id_tipe_calon_nasabah == Constants::TCN_EFORM) $modul = $pipeline->refEform;
            elseif($pipeline->id_tipe_calon_nasabah == Constants::TCN_AKTIFITAS_PEMASARAN) $modul = $pipeline->refAktifitasPemasaran;
            else $modul = $pipeline->refLeads;

            $keuangan = PKreditDataKeuangan::byIdPipeline($id_pipeline);

            $data = Model::where('id_pipeline',$id_pipeline)->first();
            $result = new \stdClass;
            $result->id_pipeline = $data->id_pipeline ?? null;
            $result->plafond_diberikan = $data->plafond_diberikan ?? null; // dari rumus
            $result->jangka_waktu = $data->jangka_waktu ?? $modul->jangka_waktu;
            $result->nilai_angsuran = $data->nilai_angsuran ?? null;
            $result->maks_plafond = $data->maks_plafond ?? null;
            $result->maks_angsuran = $data->maks_angsuran ?? null;
            $result->maks_jangka_waktu = $data->maks_jangka_waktu ?? null;
            $result->id_sub_sub_produk = $data->id_sub_sub_produk ?? null;
            $result->id_jenis_skema = $data->id_jenis_skema ?? null;
            $result->kode_plan = $data->kode_plan ?? null;
            $result->kode_dinas = $data->kode_dinas ?? null;
            $result->id_sektor_ekonomi = $data->id_sektor_ekonomi ?? null;
            $result->idir = $keuangan->idir ?? null;
            $result->rpc =  $keuangan->rpc ?? null;
            $result->kategori_debitur = $data->kategori_debitur ?? null;
            $result->kategori_portofolio = $data->kategori_portofolio ?? null;
            $result->jenis_kredit = $data->jenis_kredit ?? null;
            $result->sifat_kredit = $data->sifat_kredit ?? null;
            $result->id_jenis_penggunaan = $data->id_jenis_penggunaan ?? null;
            $result->orientasi_penggunaan = $data->orientasi_penggunaan ?? null;
            $result->kategori_kredit = $data->kategori_kredit ?? null;
            $result->sk_bng = $data->sk_bng ?? null;
            $result->sk_bunga = $data->sk_bunga ?? null;
            $result->pendapatan_ditangguhkan = $data->pendapatan_ditangguhkan ?? null;
            $result->tujuan_bank_garansi = $data->tujuan_bank_garansi ?? null;
            $result->jenis_bank_garansi = $data->jenis_bank_garansi ?? null;
            $result->id_lokasi_proyek = $data->id_lokasi_proyek ?? null;
            $result->sandi_realisasi = $data->sandi_realisasi ?? null;
            $result->rpc_sisa_penghasilan = $keuangan->rpc_sisa_penghasilan ?? null;
            $result->nama_sub_sub_produk = $data->refSubSubProduk->nama ?? null;
            $result->nama_sub_produk = $data->refSubSubProduk->refSubProduk->nama ?? null;
            $result->nama_produk = $data->refSubSubProduk->refSubProduk->refProduk->nama ?? null;
            $result->limit_aktif = $pipeline->refPlafondDebitur->limit_aktif ?? 0;
            unset(
                $data->refPipeline
            );
            return $result;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function store($request,$is_transaction = true)
    {
        if($is_transaction) DB::beginTransaction();
        try {

            $require_fileds = [];
            if(!$request->id_pipeline) $require_fileds[] = 'id_pipeline';
            if(!$request->idir) $require_fileds[] = 'idir';
            if(!$request->rpc) $require_fileds[] = 'rpc';
            if(count($require_fileds) > 0) throw new \Exception('This parameter must be filled '.implode(',',$require_fileds),400);
            $attr = $request->all();
            $attr['created_by'] = $request->current_user->id;
            $attr['updated_by'] = $request->current_user->id;
            $store =  Model::where([
                'id_pipeline' => $request->id_pipeline,
            ])->first();
            if(!$store) $store = new Model;
            $store->fill($attr);
            $store->save();

            TransaksiPipeline::updateStepAnalisaKredit([
                'id_pipeline' => $request->id_pipeline,
                'step_analisa_kredit' => Constants::STEP_DATA_ANALISA_KREDIT
            ],false);

            if($is_transaction) DB::commit();
            return $store;
        } catch (\Throwable $th) {
            if($is_transaction) DB::rollback();
            throw $th;
        }
    }
}
