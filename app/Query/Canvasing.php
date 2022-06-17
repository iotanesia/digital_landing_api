<?php

namespace App\Query;
use App\Models\Eform as Model;
use App\ApiHelper as Helper;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Constants\Group;

class Canvasing {

    public static function getDataPusat($request)
    {
        // dummy-data
        try {
            return [
                'items' => [
                    [
                        'nama' =>  'Andi Ruswandi',
                        'nik' =>  '1234567890123456',
                        'created_at' => '2021-06-18'
                    ],
                    [
                        'nama' =>  'Rexy Main Gundu',
                        'nik' =>  '1234567890123457',
                        'created_at' => '2021-06-18'
                    ]
                ],
                'attributes' => [
                    'total' => 5,
                    'current_page' => 1,
                    'from' => 1,
                    'per_page' => 2,
                ]
            ];
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
