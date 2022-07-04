<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleProduk extends Model
{
    use HasFactory;
    protected $table = 'roles_produk';
    protected $connection = 'auth';

    public $fillable = [
        'id_roles',
        'id_produk',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by',
    ];

    public function refProduk()
    {
        return $this->belongsTo(MProduk::class,'id_produk','id_produk');
    }

    public function refJenisProduk()
    {
        return $this->belongsTo(MJenisProduk::class,'id_jenis_produk','id_jenis_produk');
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model){
            $model->created_by = request()->current_user->id;
        });
        static::updating(function ($model){
            $model->updated_by = request()->current_user->id;
        });
        static::deleting(function ($model){
            $model->deleted_by = request()->current_user->id;
        });
    }
}
