<?php

return [


    'dhn-bi' => [
        'label' => 'dhn-bi',
        'jenis' => 'database',
        'query' => '\App\Query\MDhnBI'
    ],
    'dhn-dki' => [
        'label' => 'dhn-dki',
        'jenis' => 'database',
        'query' => '\App\Query\MDhnDki'
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


];
