<?php

namespace App\Query\Transaksi;


class Pencairan
{
    public static function byId($id)
    {
        $dataDebitur = [];
        $dataRM = [];
        $output = [];
        $dataDebitur[] = [
            'nama_debitur' => 'Mira Setiawan',
            'produk' => 'MIKRO',
        ];
        $dataRM[] = [
            'nama_rm' => 'Wan Adli',
            'kode_rm' => '1111111111',
            'cabang' => 'PUSAT'
        ];
        $output[] = [
            $dataDebitur,$dataRM
        ];
        return ['items' => response()->json( [$output] )];
    }

    public static function getAll($request)
    {
        try {
            $data2 = [];
            $data2[] = [
                    'nama_debitur' => 'Mira Setiawan',
                    'produk' => 'MIKRO',
                    'sub_produk' => 'MIKRO',
                    'plafond' => 'Rp. 100.000.000'
                ];
            $data2[] = [
                'nama_debitur' => 'Mira Setiawan',
                'produk' => 'MIKRO',
                'sub_produk' => 'KUR',
                'plafond' => 'Rp. 100.000.000'
            ];
            $data2[] = [
                'nama_debitur' => 'Mira Setiawan',
                'produk' => 'KMG',
                'sub_produk' => 'KMG Griya Monas',
                'plafond' => 'Rp. 100.000.000'
            ];
            $data2[] = [
                'nama_debitur' => 'Mira Setiawan',
                'produk' => 'KPR',
                'sub_produk' => 'KPR',
                'plafond' => 'Rp. 100.000.000'
            ];

            //$output = json_encode($data2);

            return [
                'items' => response()->json( [$data2] ),
                //'items' => $output,
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
}
