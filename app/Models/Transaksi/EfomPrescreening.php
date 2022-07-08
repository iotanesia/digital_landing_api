<?php

namespace App\Models\Transaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EfomPrescreening extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'eform_prescreening';
    protected $connection = 'transaksi';

    protected $fillable = [
        'id_eform',
        'id_prescreening_rules',
        'metode',
        'keterangan',
        'status',
        'request',
        'response',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by',
    ];
}
