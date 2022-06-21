<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogPrescreening extends Model
{
    use HasFactory;
    protected $connection = 'log';
    protected $table = 'log_prescreening';

    public $fillable = [
        'request',
        'response',
        'id_eform',
        'method',
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

    }
}
