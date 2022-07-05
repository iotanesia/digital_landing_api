<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class MStatusTempatTinggal extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'status_tempat_tinggal';
    protected $connection = 'master';
    protected $primaryKey = 'id_status_tempat_tinggal';
    public $fillable = [
        'nama_status_tempat_tinggal',
        'kode_status_tempat_tinggal',
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
