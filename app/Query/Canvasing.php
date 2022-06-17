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
                        'id' =>  1,
                        'nama' =>  'Andi Ruswandi',
                        'nik' =>  '1234567890123456',
                        'created_at' => '2021-06-18'
                    ],
                    [
                        'id' =>  2,
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

    public static function byId($request)
    {
        // dummy-data
        try {
            return [
                'items' => [
                    'id' => 1,
                    'nik' => '123455667789',
                    'nama_lengkap' => 'Rexy',
                    'no_hp' => '089201023911',
                    'alamat' => 'Ds KKN Penari',
                    'status' => null,
                    'produk' => 'MIKRO',
                    'kode_produk' => 'MKR'
                ],
                'attributes' => null,
            ];
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function store($request)
    {
        // dummy-data
        try {
            return [
                'items' => null,
                'attributes' => null,
             ];
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
