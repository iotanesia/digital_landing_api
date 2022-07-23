<?php

namespace App\Query\Master;

use App\Models\Master\SkorDetail as Model;

class SkorDetail
{
    public static function byIdSkor($id)
    {
        return Model::where('id_skor',$id)->get();
    }
}
