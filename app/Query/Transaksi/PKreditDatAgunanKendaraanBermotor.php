<?php

namespace App\Query\Transaksi;

use App\Models\Transaksi\PKreditDatAgunanKendaraanBermotor as Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PKreditDatAgunanKendaraanBermotor
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
            if(!$request->id_proses_data_agunan) $require_fileds[] = 'id_proses_data_agunan';
            if(count($require_fileds) > 0) throw new \Exception('This parameter must be filled '.implode(',',$require_fileds),400);

            $attr = $request->all();
            $attr['created_by'] = $request->current_user->id;
            $attr['updated_by'] = $request->current_user->id;
            $store =  Model::where([
                'id_proses_data_agunan' => $request->id_proses_data_agunan,
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
