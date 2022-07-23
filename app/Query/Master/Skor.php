<?php

namespace App\Query\Master;

use App\Models\Master\Skor as Model;

class Skor
{
    public static function byId($id)
    {
        return Model::find($id);
    }
}
