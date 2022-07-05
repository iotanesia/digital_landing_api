<?php

return [


    'dhn-bi' => [
        'label' => 'dhn-bi',
        'jenis' => 'database',
        'query' => '\App\Query\Master\MDhnBI'
    ],
    'dhn-dki' => [
        'label' => 'dhn-dki',
        'jenis' => 'database',
        'query' => '\App\Query\Master\MDhnDki'
    ],
    'dukcapil' => [
        'label' => 'dukcapil',
        'jenis' => 'service',
        'query' => '\App\Services\Dukcapil'
    ],
    'clik' => [
        'label' => 'clik',
        'jenis' => 'service',
        'query' => '\App\Services\ClikService'
    ],
    'calon-single' => [
        'label' => 'calon-single',
        'jenis' => 'service',
        'query' => '\App\Services\SKIPCalonSingle'
    ],
    'fitur-plafon' => [
        'label' => 'fitur-plafon',
        'jenis' => 'service',
        'query' => '\App\Services\SKIPPlafon'
    ],


];
