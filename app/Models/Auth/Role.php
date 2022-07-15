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

    public $fillable = [
        'nama',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by',
    ];

    public function refRolesProduk()
    {
        return $this->belongsTo(RoleProduk::class,'id','id_role');
    }

}
