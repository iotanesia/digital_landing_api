<?php

namespace App\Query\Skema;

use App\Models\Skema\AgunanSkemaProduk as Model;

class AgunanSkemaProduk
{

    public static function getSkema($data)
    {
        try {
            $skema = Model::where([
                'id_produk' => $data->id_produk,
                'id_sub_produk' => $data->id_sub_produk,
            ])
            ->get()->map(function ($item) use ($data){
                /** check */ if($data->plafond >= $item->min_plafond && $data->plafond <= $item->maks_plafond) {
                    return $item->keterangan;
                }
            })->toArray();
            $filter = array_values(array_filter($skema));
            $tanpa_agunan = in_array('TANPA AGUNAN',$filter);
            $agunan = in_array('AGUNAN',$filter);
            if($tanpa_agunan) $result = [
                'step' => false,
                'menu' => false
            ];
            if($agunan) $result = [
                'step' => true,
                'menu' => true
            ];
            if(count($filter) == 2) $result = [
                'step' => false,
                'menu' => true
            ];
            return $result;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

}
