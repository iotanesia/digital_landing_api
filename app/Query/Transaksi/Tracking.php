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

//     tracking
// - cek pipeline berdasarkan nomor aplikasi
//   jika ada
//      tracking 2 berpasaangan dengan step verifikasi
//      liat tracking jika tracking = 2 dan step verifikasi 0
//   	Gambar 1
//         - prescreening
// 	       - lolos
//      Gambar 2
// 	        - analisa kredit
//          - verifikasi data
// 	        - sedang di proses
//     liat tracking jika tracking = 2 dan step verifikas 1
//         Gambar 1
// 	        - prescreening
// 	        - lolos
//         Gambar 2
// 	        - analisa kredit
//          - verifikasi data
//          - sedang di proses
//     liat tracking jika tracking = 2 dan step verifikas 2
//         Gambar 1
// 	        - prescreening
// 	        - lolos
//         Gambar 2
// 	       - analisa kredit
//         - verifikasi data
//         - sedang diproses
//    liat tracking jika tracking = 2 dan step verifikas 3
// 	Gambar 1
// 	- prescreening
// 	- lolos
//         Gambar 2
// 	- analisa kredit
//         - verifikasi data
//         - selesai
// 	gambar 3
// 	- aprroval
// 	- sedang di proses
//   tracking 3 berpasaangan dengan step approval
//   jika tracking 3 dan step approval = 0
// 	Gambar 1
// 	- prescreening
// 	- lolos
//         Gambar 2
// 	- analisa kredit
//         - selesai
//         Gambar 3
//  	- approval
// 	- sedang proses
//    tracking 3 dan step approval 1
// 	Gambar 1
// 	- prescreening
// 	- lolos
//         Gambar 2
// 	- analisa kredit
//         - selesai
//         Gambar 3
//  	- approval
// 	- selesai
// 	 Gambar 4
//  	- cetak dokumen
// 	- sedang diproses
//    tracking 3 berpasaangan dengan step approval 2
// 	Gambar 1
// 	- prescreening
// 	- lolos
//         Gambar 2
// 	- analisa kredit
//         - selesai
//         Gambar 3
//  	- approval
// 	- ditolak
//    tracking 4 berpasangan dengan step cekat dokumen
// 	jika tracking 4 dan step cetak dokumen = 0
// 	Gambar 1
// 	- prescreening
// 	- lolos
//         Gambar 2
// 	- analisa kredit
//         - selesai
//         Gambar 3
//  	- approval
// 	- sedang proses
//    	tracking 4 berpasaangan dengan step approval 1
// 	Gambar 1
// 	- prescreening
// 	- lolos
//         Gambar 2
// 	- analisa kredit
//         - selesai
//         Gambar 3
//  	- approval
// 	- selesai
//     	Gambar 4
//  	- cetak dokumen
// 	- selesai
// 	Gambar 5
//  	- Disbursment
// 	- sedang diproses
//     tracking 5 berpasangan dengan step_disbursment
// 	 jika traking = 5 dan step_disbursement = 1
// 	Gambar 1
// 	- prescreening
// 	- lolos
//         Gambar 2
// 	- analisa kredit
//         - selesai
//         Gambar 3
//  	- approval
// 	- selesai
//     	Gambar 4
//  	- cetak dokumen
// 	- selesai
// 	Gambar 5
//  	- Disbursment
// 	- selesai
// 	jika traking = 5 dan step_disbursement = 2
// 	Gambar 1
// 	- prescreening
// 	- lolos
//         Gambar 2
// 	- analisa kredit
//         - selesai
//         Gambar 3
//  	- approval
// 	- selesai
//     	Gambar 4
//  	- cetak dokumen
// 	- selesai
// 	Gambar 5
//  	- Disbursment
// 	- ditolak

//   jika tidak ada
//   cek nomor_apilkasi di tabel eform ambil is presecrening
//   jika 0
//      return
// 	sedang di proses
//   jika 2
//     return
// 	-lolos dengan catatan
//   jika 3
//     return
//        -tidak lolos


    public static function data($request)
    {

        $require_fileds = [];
        if(!$request->nomor_aplikasi) $require_fileds[] = 'nomor_aplikasi';
        if(!$request->nik) $require_fileds[] = 'nik';
        if(count($require_fileds) > 0) throw new \Exception('This parameter must be filled '.implode(',',$require_fileds),400);
        $data = Model::with([
            'refPrescreening' => function ($query){
                $query->where('request','ilike','%trx%');
            }
        ])->where('nomor_aplikasi',$request->nomor_aplikasi)
        ->where('nik',$request->nik)
        ->first();
        if(!$data) throw new \Exception("Data tidak ditemukan.", 400);

        $sikp = EformPrescreening::sikpMetode($data->id);
        $ket_sikp = $sikp->keterangan ?? null;
        $sts_sikp = $ket_sikp == 'Failed' ? false : true;
        $ket_digidata = $data->refPrescreening->keterangan ?? null;
        $sts_digidata = $ket_digidata == 'Failed' ? false : true;

        $ket = null;
        if(!$sts_sikp)  $ket = 'sikp';
        if(!$sts_digidata) $ket = 'dukcapil';

        $data->status_perkawinan = $data->refStatusPerkawinan->nama ?? null;
        $data->nama_cabang = $data->refCabang->nama_cabang ?? null;
        $data->nama_produk = $data->refProduk->nama ?? null;
        $data->nama_sub_produk = $data->refSubProduk->nama ?? null;
        $data->jenis_kelamin = $data->refJenisKelamin->nama ?? null;


        $data->step = [
            [
                'kode' => '01',
                'label' => 'Prescreening',
                'tanggal' => Carbon::parse($data->created_at)->format('Y-m-d'),
                'status' => $data->refStsPrescreening->nama ?? null,
                'id_status' => $data->is_prescreening ?? null,
                'keterangan' => $ket,
                'step' => null
            ],
            [
                'kode' => '02',
                'label' => 'Analisa Kredit',
                'tanggal' => in_array($data->is_prescreening,[Constants::IS_ACTIVE]) ? Carbon::now()->format('Y-m-d') : null,
                'status' =>  in_array($data->is_prescreening,[Constants::IS_ACTIVE]) ? 'Sedang Diproses' : null ,
                'id_status' => 0,
                'keterangan' => null,
                'step' => in_array($data->is_prescreening,[Constants::IS_ACTIVE]) ? 'Verifikasi Data' : null,
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
            $data->refPrescreening,
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
