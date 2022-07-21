<?php

namespace App\Query\Transaksi;


class Pencairan
{
    public static function byId($id)
    {
        $output = [
            'data_debitur' => [
                'nama_debitur' => 'Mira Setiawan',
                'produk' => 'MIKRO',
            ],
            'data_rm' => [
                'nama_rm' => 'Wan Adli',
                'kode_rm' => '1111111111',
                'cabang' => 'PUSAT'
            ]
        ];
        return ['items' => $output];
    }

    public static function getAll($request)
    {
        try {
            $output = [
                [
                    'nama_debitur' => 'Mira Setiawan',
                    'produk' => 'MIKRO',
                    'sub_produk' => 'MIKRO',
                    'plafond' => 'Rp. 100.000.000'
                ],
                [
                    'nama_debitur' => 'Mira Setiawan',
                    'produk' => 'MIKRO',
                    'sub_produk' => 'KUR',
                    'plafond' => 'Rp. 100.000.000'
                ],
                [
                    'nama_debitur' => 'Mira Setiawan',
                    'produk' => 'KMG',
                    'sub_produk' => 'KMG Griya Monas',
                    'plafond' => 'Rp. 100.000.000'
                ],
                [
                    'nama_debitur' => 'Mira Setiawan',
                    'produk' => 'KPR',
                    'sub_produk' => 'KPR',
                    'plafond' => 'Rp. 100.000.000'
                ]
            ];

            return [
                'items' => $output,
                'attributes' => [
                    'total' => '10',
                    'current_page' => '1',
                    'from' => '10',
                    'per_page' => '4',
                ]
                ];
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function store($request,$is_transaction = true)
    {
        //if($is_transaction) DB::beginTransaction();
        try {

            //if($is_transaction) DB::commit();

            return ["items" => $request->keterangan];
        } catch (\Throwable $th) {
            //if($is_transaction) DB::rollBack();
            throw $th;
        }
    }
}
