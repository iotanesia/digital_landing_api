<?php

namespace App\Models\Skema;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AgunanSkemaProduk extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'agunan_skema_produk';
    protected $connection = 'skema';
    public $fillable = [
        'id_produk',
        'id_sub_produk',
        'id_sub_sub_produk',
        'min_plafond',
        'maks_plafond',
        'keterangan',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by',
    ];
}
