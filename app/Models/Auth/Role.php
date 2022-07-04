<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Role extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'roles';
    protected $connection = 'auth';

    public function refRolesProduk()
    {
        return $this->belongsTo(RoleProduk::class,'id','id_role');
    }

}
