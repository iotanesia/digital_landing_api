<?php

namespace App\Models\Transaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SkoringApproval extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'skoring_approval';
    protected $connection = 'transaksi';

    public $fillable = [
        'id_pipeline',
        'id_user',
        'keterangan',
        'id_cabang',
        "created_at",
        "created_by",
        "updated_at",
        "updated_by",
        "deleted_at",
    ];

    public function refPipeline()
    {
        return $this->belongsTo(Pipeline::class,'id_pipeline','id');
    }

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
