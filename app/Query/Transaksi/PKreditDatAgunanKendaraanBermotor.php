<?php

namespace App\Query\Transaksi;

use App\Models\Transaksi\PKreditDatAgunanKendaraanBermotor as Model;
use App\Query\Skema\AgunanNilai;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PKreditDatAgunanKendaraanBermotor
{
    public static function byIdProsesDataAgunan($id_proses_data_agunan)
    {
        try {
            $data_agunan = PKreditDataAgunan::byId($id_proses_data_agunan);
            if(!$data_agunan) throw new \Exception("Data tidak ditemukan", 400);
            $data = Model::where('id_proses_data_agunan',$id_proses_data_agunan)->first();
            $result = new \stdClass;
            $result->id_proses_data_agunan = $data->id_proses_data_agunan ?? null;
            $result->jenis_kendaraan = $data->jenis_kendaraan ?? null;
            $result->penggunaan_jaminan = $data->penggunaan_jaminan ?? null;
            $result->nama_pemilik_bpkb = $data->nama_pemilik_bpkb ?? null;
            $result->nama_pemilik_saat_ini = $data->nama_pemilik_saat_ini ?? null;
            $result->alamat_pemilik_saat_ini = $data->alamat_pemilik_saat_ini ?? null;
            $result->no_faktur = $data->no_faktur ?? null;
            $result->no_mesin = $data->no_mesin ?? null;
            $result->no_rangka = $data->no_rangka ?? null;
            $result->no_polisi = $data->no_polisi ?? null;
            $result->no_bpkb = $data->no_bpkb ?? null;
            $result->no_stnk = $data->no_stnk ?? null;
            $result->warna = $data->warna ?? null;
            $result->tahun = $data->tahun ?? null;
            $result->merk = $data->merk ?? null;
            $result->model = $data->model ?? null;
            $result->nilai_market = $data->nilai_market ?? null;
            $result->collateral_class = $data->collateral_class ?? null;
            $result->jenis_agunan = $data->jenis_agunan ?? null;
            $result->sifat_agunan = $data->sifat_agunan ?? null;
            $result->penerbiit_agunan = $data->penerbiit_agunan ?? null;
            $result->cash_non_cash = $data->cash_non_cash ?? null;
            $result->jenis_pengikatan = $data->jenis_pengikatan ?? null;
            $result->coverage_obligation = $data->coverage_obligation ?? null;
            $result->collateral_mortage_priority = AgunanNilai::setByIdAgunan($data_agunan->id_agunan,'collateral_mortage_priority');
            $result->allow_acct_attached_to_coll = AgunanNilai::setByIdAgunan($data_agunan->id_agunan,'allow_acct_attached_to_coll');
            $result->customer_or_bank_has_coll = AgunanNilai::setByIdAgunan($data_agunan->id_agunan,'customer_or_bank_has_coll');
            $result->nama_perusahaan_appraisal = AgunanNilai::setByIdAgunan($data_agunan->id_agunan,'nama_perusahaan_appraisal');
            $result->collateral_status = AgunanNilai::setByIdAgunan($data_agunan->id_agunan,'collateral_status');
            $result->coll_status_of_acct = AgunanNilai::setByIdAgunan($data_agunan->id_agunan,'coll_status_of_acct');
            $result->coll_utilized_amout = AgunanNilai::setByIdAgunan($data_agunan->id_agunan,'coll_utilized_amout');
            $result->jenis_asuransi = AgunanNilai::setByIdAgunan($data_agunan->id_agunan,'jenis_asuransi');
            $result->jenis_agunan_ppap = AgunanNilai::setByIdAgunan($data_agunan->id_agunan,'jenis_agunan_ppap');
            $result->bi_penilaian_menurut_bank = AgunanNilai::setByIdAgunan($data_agunan->id_agunan,'bi_penilaian_menurut_bank');
            $result->bi_pengikatan_intenal = AgunanNilai::setByIdAgunan($data_agunan->id_agunan,'bi_pengikatan_intenal');
            $result->bi_pengikatan_notaril = AgunanNilai::setByIdAgunan($data_agunan->id_agunan,'bi_pengikatan_notaril');
            $result->bi_bukti_dok_kepemilikan = AgunanNilai::setByIdAgunan($data_agunan->id_agunan,'bi_bukti_dok_kepemilikan');
            $result->bi_dati = $data->bi_dati ?? null;
            $result->utillized_amout = $data->utillized_amout ?? null;
            if($data) {
                $result->aset = $data->manyAset->map(function ($item)
                {
                    return [
                        'id' => $item->id,
                        'foto' => $item->foto
                    ];
                }) ?? [];
                $result->asuransi = $data->manyAsuransi->map(function ($item)
                {
                    return [
                        'id' => $item->id,
                        'nama_perusahaan' => $item->nama_perusahaan,
                        'tgl_awal' => $item->tgl_awal,
                        'tgl_akhir' => $item->tgl_akhir,
                        'nilai' => $item->nilai,
                    ];
                }) ?? [];
            }else {
                $result->aset = [];
                $result->asuransi = [];
            }

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
            if(!$request->id_agunan) $require_fileds[] = 'id_agunan';
            if(!$request->id_pipeline) $require_fileds[] = 'id_pipeline';
            if(count($require_fileds) > 0) throw new \Exception('This parameter must be filled '.implode(',',$require_fileds),400);

            $attr = $request->all();
            $attr['collateral_class'] =  AgunanNilai::setByIdAgunanNilai($request->id_agunan,'collateral_class');
            $attr['jenis_agunan'] =  AgunanNilai::setByIdAgunanNilai($request->id_agunan,'jenis_agunan');
            $attr['sifat_agunan'] = AgunanNilai::setByIdAgunanNilai($request->id_agunan,'sifat_agunan');
            $attr['penerbit_agunan'] = AgunanNilai::setByIdAgunanNilai($request->id_agunan,'penerbit_agunan');
            $attr['cash_non_cash'] = AgunanNilai::setByIdAgunanNilai($request->id_agunan,'cash_non_cash');
            $attr['jenis_pengikatan'] = AgunanNilai::setByIdAgunanNilai($request->id_agunan,'jenis_pengikatan');
            $attr['coverage_oblligation'] = AgunanNilai::setByIdAgunanNilai($request->id_agunan,'coverage_oblligation');
            $attr['collateral_mortage_priority'] = AgunanNilai::setByIdAgunanNilai($request->id_agunan,'collateral_mortage_priority');
            $attr['allow_acct_attached_to_coll'] = AgunanNilai::setByIdAgunanNilai($request->id_agunan,'allow_acct_attached_to_coll');
            $attr['customer_or_bank_has_coll'] = AgunanNilai::setByIdAgunanNilai($request->id_agunan,'customer_or_bank_has_coll');
            $attr['nama_perusahaan_appraisal'] = AgunanNilai::setByIdAgunanNilai($request->id_agunan,'nama_perusahaan_appraisal');
            $attr['coll_status_of_acct'] = AgunanNilai::setByIdAgunanNilai($request->id_agunan,'coll_status_of_acct');
            $attr['coll_utilized_amout'] = AgunanNilai::setByIdAgunanNilai($request->id_agunan,'coll_utilized_amout');
            $attr['jenis_asuransi'] = AgunanNilai::setByIdAgunanNilai($request->id_agunan,'jenis_asuransi');
            $attr['jenis_agunan_ppap'] = AgunanNilai::setByIdAgunanNilai($request->id_agunan,'jenis_agunan_ppap');
            $attr['bi_penilaian_menurut_bank'] = AgunanNilai::setByIdAgunanNilai($request->id_agunan,'bi_penilaian_menurut_bank');
            $attr['bi_pengikatan_internal'] = AgunanNilai::setByIdAgunanNilai($request->id_agunan,'bi_pengikatan_internal');
            $attr['bi_pengikatan_notaril'] = AgunanNilai::setByIdAgunanNilai($request->id_agunan,'bi_pengikatan_notaril');
            $attr['bi_bukti_dok_kepemilikan'] = AgunanNilai::setByIdAgunanNilai($request->id_agunan,'bi_bukti_dok_kepemilikan');
            $attr['created_by'] = $request->current_user->id;
            $attr['updated_by'] = $request->current_user->id;

            $agunan = PKreditDataAgunan::store($request,false);
            $id_proses_data_agunan =  $request->id_proses_data_agunan ?? $agunan->id;
            $store =  Model::where([
                'id_proses_data_agunan' => $id_proses_data_agunan,
            ])->first();
            if(!$store) $store = new Model;
            $attr['id_proses_data_agunan'] = $id_proses_data_agunan;
            $store->fill($attr);
            $store->save();

            if(count($request->delete_aset) > 0) $store->manyAset()->whereIn('id',$request->delete_aset)->delete();
            if(count($request->delete_asuransi) > 0) $store->manyAsuransi()->whereIn('id',$request->delete_asuransi)->delete();

            $store->manyAset()->createMany(self::setParamsAset($request,$store->id));
            $store->manyAsuransi()->createMany(self::setParamsAsuransi($request,$store->id));
            if($is_transaction) DB::commit();
            return $store;
        } catch (\Throwable $th) {
            if($is_transaction) DB::rollback();
            throw $th;
        }
    }

    public static function setParamsAset($request,$id)
    {
        if(!$request->aset) return [];
        $ext = [];
        foreach ($request->aset as $key => $item) {
            if($item['foto']) {
                $foto = (string) Str::uuid().'.png';
                Storage::put($foto, base64_decode($item['foto']));
                $item['foto'] = $foto;
            }
            $item['id_proses_kredit_data_agunan_kendaraan_bermotor'] = $id;
            $ext[] = $item;
        }
        return $ext;
    }

    public static function setParamsAsuransi($request,$id)
    {
        if(!$request->asuransi) return [];
        return array_map(function ($item) use ($id) {
            $item['id_proses_kredit_data_agunan_kendaraan_bermotor'] = $id;
            return $item;
        },$request->asuransi);
    }
}
