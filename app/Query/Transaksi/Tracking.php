<?php

namespace App\Query\Transaksi;
use App\Models\Transaksi\Pipeline as Model;
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
        $data = Eform::byNomorAplikasi($request);
        if(!$data['items']) throw new \Exception("Data tidak ditemukan", 400);
        $ext = new \stdClass;
        $ext->nomor_aplikasi = $data['items']->nomor_aplikasi ?? null;
        $ext->nik = $data['items']->nik;
        $ext->nama = $data['items']->nama;
        $ext->plafond = $data['items']->plafond ?? null;
        $ext->npwp = $data['items']->npwp ?? null;
        $ext->email = $data['items']->email ?? null;
        $ext->no_hp = $data['items']->no_hp ?? null;
        $ext->jangka_waktu = $data['items']->jangka_waktu ?? null;
        $ext->nama_produk = $data['items']->refProduk->nama ?? null;
        $ext->id_produk = $data['items']->id_produk ?? null;
        $ext->nama_produk = $data['items']->refProduk->nama ?? null;
        $ext->id_sub_produk = $data['items']->id_sub_produk ?? null;
        $ext->nama_sub_produk = $data['items']->refSubProduk->nama ?? null;
        $ext->foto_ktp = null; // masih dummy
        $ext->foto_selfie = null; // masih dummy

        if(!$data['items']) throw new \Exception('No Aplikasi dan NIK tidak sesuai');
        $ext->step = [
            [
                'kode' => '01',
                'label' => 'Prescreening',
                'tanggal' => Carbon::now()->format('Y-m-d'),
                'status' => 'lolos',
                'keterangan' => null,
                'step' => null
            ],
            [
                'kode' => '02',
                'label' => 'Analisa Kredit',
                'tanggal' => Carbon::now()->format('Y-m-d'),
                'status' => 'Sedang Diproses',
                'keterangan' => null,
                'step' => 'Verifikasi Data'
            ],
            [
                'kode' => '03',
                'label' => 'Approval',
                'tanggal' => null,
                'status' => null,
                'keterangan' => null,
                'step' => null
            ],
            [
                'kode' => '04',
                'label' => 'Cetak Dokumen',
                'tanggal' => null,
                'status' => null,
                'keterangan' => null,
                'step' => null

            ],
            [
                'kode' => '05',
                'label' => 'Disbursement',
                'tanggal' => null,
                'status' => null,
                'keterangan' => null,
                'step' => null
            ]
        ];

        return [
            'items' => $ext
        ];
    }
}
