<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Pengaturan\RolesMenu;
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

    public function manyRolesProduk()
    {
        return $this->hasMany(RoleProduk::class,'id_role','id');
    }

    public function manyRolesMenu()
    {
        return $this->hasMany(RolesMenu::class,'id_role','id');
    }
}
