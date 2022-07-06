<?php

namespace App\Query\Auth;
use App\Models\Auth\ClientApi as Model;
use App\ApiHelper as Helper;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ClientApi {

    public static function byUsername($username)
    {
        return Model::where('username',$username)->first();
    }
}
