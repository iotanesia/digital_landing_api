<?php

namespace App\Query\Transaksi;
use App\Models\Transaksi\Eform as Model;
use App\ApiHelper as Helper;
use App\Constants\Constants;
use App\Query\Status\StsTracking;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class Tracking {

    public static function data($request)
    {

        $require_fileds = [];
        if(!$request->nomor_aplikasi) $require_fileds[] = 'nomor_aplikasi';
        if(!$request->nik) $require_fileds[] = 'nik';
        if(count($require_fileds) > 0) throw new \Exception('This parameter must be filled '.implode(',',$require_fileds),400);
        $data = Model::where('nomor_aplikasi',$request->nomor_aplikasi)
        ->where('nik',$request->nik)
        ->first();
        if(!$data) throw new \Exception("Data tidak ditemukan.", 400);
        $data->status_perkawinan = $data->refStatusPerkawinan->nama ?? null;
        $data->nama_cabang = $data->refCabang->nama_cabang ?? null;
        $data->nama_produk = $data->refProduk->nama ?? null;
        $data->nama_sub_produk = $data->refSubProduk->nama ?? null;
        $data->jenis_kelamin = $data->refJenisKelamin->nama ?? null;

        $data->step = [
            [
                'kode' => '01',
                'label' => 'Prescreening',
                'tanggal' => Carbon::now()->format('Y-m-d'),
                'status' => $data->refStsPrescreening->nama ?? null,
                'id_status' => $data->is_prescreening ?? null,
                'keterangan' => null,
                'step' => null
            ],
            [
                'kode' => '02',
                'label' => 'Analisa Kredit',
                'tanggal' => Carbon::now()->format('Y-m-d'),
                'status' => 'Sedang Diproses',
                'id_status' => 0,
                'keterangan' => null,
                'step' => 'Verifikasi Data'
            ],
            [
                'kode' => '03',
                'label' => 'Approval',
                'tanggal' => null,
                'status' => null,
                'id_status' => 0,
                'keterangan' => null,
                'step' => null
            ],
            [
                'kode' => '04',
                'label' => 'Cetak Dokumen',
                'tanggal' => null,
                'status' => null,
                'id_status' => 0,
                'keterangan' => null,
                'step' => null

            ],
            [
                'kode' => '05',
                'label' => 'Disbursement',
                'tanggal' => null,
                'status' => null,
                'id_status' => 0,
                'keterangan' => null,
                'step' => null
            ]
        ];

        unset(
            $data->refStsPrescreening,
            $data->refStatusPerkawinan,
            $data->refCabang,
            $data->refAgama,
            $data->cif,
            $data->platform,
            $data->id_agama,
            $data->refProduk,
            $data->refSubProduk,
            $data->is_pipeline,
            $data->is_cutoff,
            $data->id_client_api,
            $data->manyProfilUsaha,
            $data->refJenisKelamin,
        );
        
        return ['items' => $data];
    }
}
