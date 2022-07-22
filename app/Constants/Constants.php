<?php

namespace App\Constants;


class Constants
{
    const IS_ACTIVE = 1;
    const IS_INACTIVE = 0;
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

    const STEP_ANALISA_VERIF_DATA = 1;
    const STEP_ANALISA_ONSITE_VISIT = 2;
    const STEP_ANALISA_KELENGKAPAN = 3;
    const STEP_ANALISA_SUBMIT = 4;
    const STEP_DATA_PERSONAL = 5;
    const STEP_DATA_KEUANGAN = 6;
    const STEP_DATA_USAHA = 7;
    const STEP_DATA_AGUNAN = 8;
    const STEP_DATA_ANALISA_KREDIT = 9;
    const STEP_DATA_VERIFIKASI_AGUNAN = 10;
    const STEP_DATA_SEDANG_PROSES_SKORING = 11;

    const PIP_TRACKING_PRESCREENING = 1;
    const PIP_TRACKING_VERIFIKASI = 2;

    const MTD_RULES_DHN_BI = 1;
    const MTD_RULES_DHN_DKI = 2;
    const MTD_SIKP_Calon = 3;
    const MTD_SIKP_Plafond = 4;
    const MTD_DIGI_DATA = 5;
    const MTD_SLIK_NAE = 6;

    const MENU_PROSES_KREDIT = [
        [
            'code' => 5,
            'name' => 'data personal',
            'validate' => [
                self::STEP_DATA_PERSONAL,
                self::STEP_DATA_KEUANGAN,
                self::STEP_DATA_USAHA,
                self::STEP_DATA_ANALISA_KREDIT,
                self::STEP_DATA_VERIFIKASI_AGUNAN,
                self::STEP_DATA_SEDANG_PROSES_SKORING,
            ]
        ],
        [
            'code' => 6,
            'name' => 'data keuangan',
            'validate' => [
                self::STEP_DATA_KEUANGAN,
                self::STEP_DATA_USAHA,
                self::STEP_DATA_ANALISA_KREDIT,
                self::STEP_DATA_VERIFIKASI_AGUNAN,
                self::STEP_DATA_SEDANG_PROSES_SKORING,
            ]
        ],
        [
            'code' => 7,
            'name' => 'data usaha',
            'validate' => [
                self::STEP_DATA_USAHA,
                self::STEP_DATA_ANALISA_KREDIT,
                self::STEP_DATA_VERIFIKASI_AGUNAN,
                self::STEP_DATA_SEDANG_PROSES_SKORING,
            ]
        ],
        [
            'code' => 8,
            'name' => 'data agunan',
            'validate' => [
                self::STEP_DATA_AGUNAN,
                self::STEP_DATA_ANALISA_KREDIT,
                self::STEP_DATA_VERIFIKASI_AGUNAN,
                self::STEP_DATA_SEDANG_PROSES_SKORING,
            ]
        ],
        [
            'code' => 9,
            'name' => 'analisa kredit',
            'validate' => [
                self::STEP_DATA_ANALISA_KREDIT,
                self::STEP_DATA_VERIFIKASI_AGUNAN,
                self::STEP_DATA_SEDANG_PROSES_SKORING,
            ]

        ],
        [
            'code' => 10,
            'name' => 'verifikasi agunan',
            'validate' => [
                self::STEP_DATA_VERIFIKASI_AGUNAN,
                self::STEP_DATA_SEDANG_PROSES_SKORING,
            ]
        ]
    ];

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
