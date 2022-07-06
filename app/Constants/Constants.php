<?php

namespace App\Constants;


class Constants
{
    const IS_ACTIVE = 1;
    const IS_INACTIVE = 1;
    const IS_NOL = 0;
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
}
