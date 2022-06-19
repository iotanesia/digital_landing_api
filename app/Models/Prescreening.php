<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Prescreening extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'prescreening_aktifitas';

    public $fillable = [
        'metode',
        'keterangan',
        'status',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
    ];
}
