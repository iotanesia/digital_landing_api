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
        'id_prescreening',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];

    public static function boot()
    {
        parent::boot();


    }
}
