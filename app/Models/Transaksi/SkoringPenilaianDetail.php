<?php

namespace App\Models\Transaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SkoringPenilaianDetail extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'skoring_penilaian_detail';
    protected $connection = 'transaksi';

    public $fillable = [
        'id_skoring_penilaian',
        'id_skor',
        'penilaian',
        "created_at",
        "created_by",
        "updated_at",
        "updated_by",
        "deleted_at"
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model){
            if(request()->current_user->id) $model->created_by = request()->current_user->id;
        });
        static::updating(function ($model){
            if(request()->current_user->id) $model->updated_by = request()->current_user->id;
        });
        static::deleting(function ($model){
            if(request()->current_user->id) $model->deleted_by = request()->current_user->id;
        });
    }
}
