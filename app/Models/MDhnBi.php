<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class MDhnBi extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'master_dhn_bi';

    public $guardable = [
        'id'
    ];

    public static function cekDhn($nik) {
        try {
            $dhn = MDhnBi::where('d30ktp', $nik)->first();

            return $dhn ?? false;

        } catch (\Throwable $th) {
            throw $th;
        }
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
