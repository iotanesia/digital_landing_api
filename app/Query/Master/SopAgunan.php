<?php

namespace App\Query\Master;

use App\Models\Master\SopAgunan as Model;

class SopAgunan
{
    public static function byIdAgunan($id_agunan)
    {
        return Model::where('id_agunan',$id_agunan)->first();
    }
}
