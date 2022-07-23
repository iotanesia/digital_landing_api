<?php

namespace App\Query\Master;

use App\Models\Master\SkorDetailNilai as Model;

class SkorDetailNilai
{
    public static function byId($id)
    {
        return Model::find($id);
    }

    public static function pembanding($pembanding,$id)
    {
        $data = Model::where([
            'kolom_pembanding' => $pembanding,
            'kondisi' => $id
        ])->first();
        return $data->nilai ?? 0;
    }

}
