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
        $data = ['items' => DB::connection('transaksi')->table('v_list_pipeline')
        ->where('nomor_aplikasi',$request->nomor_aplikasi)
        ->where('nik',$request->nik)
        ->first()];
        if(!$data) throw new \Exception("Data tidak ditemukan", 400);

        $ext = new \stdClass;
        $ext->nomor_aplikasi = $data['items']->nomor_aplikasi;
        $ext->nik = $data['items']->nik;
        $ext->plafond = $data['items']->plafond ?? null;
        $ext->npwp = $data['items']->npwp ?? null;
        $ext->email = $data['items']->email ?? null;
        $ext->no_hp = $data['items']->no_hp ?? null;
        $ext->jangka_waktu = $data['items']->jangka_waktu ?? null;
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
            'items' => [ $ext ]
        ];
    }
}
