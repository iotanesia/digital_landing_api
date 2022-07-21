<?php

namespace App\Query\Transaksi;

use App\Models\Transaksi\PKreditDatAgunanDeposito as Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Query\Skema\AgunanNilai;

class PKreditDatAgunanDeposito
{
    public static function byIdProsesDataAgunan($id_proses_data_agunan)
    {
        try {
            $data = Model::where('id_proses_data_agunan',$id_proses_data_agunan)->first();
            if(!$data) throw new \Exception("Data tidak ditemukan", 400);
            $data->aset = $data->manyAset->map(function ($item)
            {
                return [
                    'id' => $item->id,
                    'foto' => $item->foto
                ];
            }) ?? null;
            $data->asuransi = $data->manyAsuransi->map(function ($item)
            {
                return [
                    'id' => $item->id,
                    'nama_perusahaan' => $item->nama_perusahaan,
                    'tgl_awal' => $item->tgl_awal,
                    'tgl_akhir' => $item->tgl_akhir,
                    'nilai' => $item->nilai,
                ];
            }) ?? null;
            unset(
                $data->manyAset,
                $data->manyAsuransi
            );
            return $data;
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
            $attr['sifat_agunan'] =  AgunanNilai::setByIdAgunanNilai($request->id_agunan,'sifat_agunan');
            $attr['penerbiit_agunan'] =  AgunanNilai::setByIdAgunanNilai($request->id_agunan,'penerbit_agunan');
            $attr['cash_non_cash'] =  AgunanNilai::setByIdAgunanNilai($request->id_agunan,'cash_non_cash');
            $attr['jenis_pengikatan'] =  AgunanNilai::setByIdAgunanNilai($request->id_agunan,'jenis_pengikatan');
            $attr['coverage_obligation'] =  AgunanNilai::setByIdAgunanNilai($request->id_agunan,'coverage_oblligation');
            $attr['collateral_mortage_priority'] =  AgunanNilai::setByIdAgunanNilai($request->id_agunan,'collateral_mortage_priority');
            $attr['allow_acct_attached_to_coll'] =  AgunanNilai::setByIdAgunanNilai($request->id_agunan,'allow_acct_attached_to_coll');
            $attr['customer_or_bank_has_coll'] =  AgunanNilai::setByIdAgunanNilai($request->id_agunan,'customer_or_bank_has_coll');
            $attr['nama_perusahaan_appraisal'] =  AgunanNilai::setByIdAgunanNilai($request->id_agunan,'nama_perusahaan_appraisal');
            $attr['coll_status_of_acct'] =  AgunanNilai::setByIdAgunanNilai($request->id_agunan,'coll_status_of_acct');
            $attr['coll_utilized_amount'] =  AgunanNilai::setByIdAgunanNilai($request->id_agunan,'coll_utilized_amout');
            $attr['jenis_asuransi'] =  AgunanNilai::setByIdAgunanNilai($request->id_agunan,'jenis_asuransi');
            $attr['jenis_agunan_ppap'] =  AgunanNilai::setByIdAgunanNilai($request->id_agunan,'jenis_agunan_ppap');
            $attr['bi_penilaian_menurut_bank'] =  AgunanNilai::setByIdAgunanNilai($request->id_agunan,'bi_penilaian_menurut_bank');
            $attr['bi_pengikatan_internal'] =  AgunanNilai::setByIdAgunanNilai($request->id_agunan,'bi_pengikatan_internal');
            $attr['bi_pengikatan_notaril'] =  AgunanNilai::setByIdAgunanNilai($request->id_agunan,'bi_pengikatan_notaril');
            $attr['bi_bukti_dok_kepemilikan'] =  AgunanNilai::setByIdAgunanNilai($request->id_agunan,'bi_bukti_dok_kepemilikan');
            $attr['created_by'] = $request->current_user->id;
            $attr['updated_by'] = $request->current_user->id;
            $agunan = PKreditDataAgunan::store($request,false);
            $id_proses_data_agunan =  !$request->id_proses_data_agunan ?? $agunan->id;
            $store =  Model::where([
                'id_proses_data_agunan' => $id_proses_data_agunan,
            ])->first();
            if(!$store) $store = new Model;
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
            $item['id_proses_kredit_data_agunan_kendaraan_deposito'] = $id;
            $ext[] = $item;
        }
        return $ext;
    }

    public static function setParamsAsuransi($request,$id)
    {
        if(!$request->asuransi) return [];
        return array_map(function ($item) use ($id) {
            $item['id_proses_kredit_data_agunan_kendaraan_deposito'] = $id;
            return $item;
        },$request->asuransi);
    }
}
