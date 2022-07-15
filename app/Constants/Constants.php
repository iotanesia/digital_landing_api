<?php

namespace App\Constants;


class Constants
{
    const IS_ACTIVE = 1;
    const IS_INACTIVE = 1;
    const IS_NOL = 0;
    const CUT_OFF = 3;
    //step verifikasi
    const PROSES_VERIFIKASI = 0;
    const VALIDASI_DATA = 1;
    const ON_SITE_VISIT = 2;
    const KELENGKAPAN_DOKUMEN = 3;
    //tracking
    const PRESCREENING = 1;
    const ANALISA_KREDIT = 2;
    const APPROVAL = 3;
    const CETAK_DOKUMEN = 4;
    const DISBURSMENT = 5;

    const AP_STS_BELUM_BERMINAT = 1;
    const AP_STS_BERMINAT = 2;
    const AP_STS_TIDAK_BERMINAT = 3;

    //tipe calon nasabah
    const TCN_AKTIFITAS_PEMASARAN = 1;
    const TCN_EFORM = 2;
    const TCN_LEAD = 3;

    const MODEL_PRESCREENING = [
        'eform' => '\App\Query\Transaksi\EformPrescreening',
        'aktifitas_pemasaran' => '\App\Query\Transaksi\AktifitasPemasaranPrescreening',
        'leads' => '\App\Query\Transaksi\LeadsPrescreening',
    ];

    const MODEL_MAIN = [
        'eform' => '\App\Query\Transaksi\Eform',
        'aktifitas_pemasaran' => '\App\Query\Transaksi\AktifitasPemasaran',
        'leads' => '\App\Query\Transaksi\Leads',
    ];

}
