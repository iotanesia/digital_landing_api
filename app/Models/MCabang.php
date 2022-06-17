<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class MCabang extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'master_cabang';
    protected $primaryKey = 'id_cabang';
    public $fillable = [
        'nama_cabang',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];

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
